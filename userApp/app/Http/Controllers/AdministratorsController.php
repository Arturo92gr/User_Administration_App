<?php

namespace App\Http\Controllers;

use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\SuperAdminMiddleware;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdministratorsController extends Controller
{
    function __construct() {
        // Primero el middleware de admin para todas las rutas excepto las de superadmin
        $this->middleware(AdminMiddleware::class)->except(['indexSuper', 'editAdmin', 'updateAdmin', 'assignSuperAdminRole', 'verifyEmail', 'destroy']);
    
        // Luego el middleware de superadmin solo para las rutas específicas
        $this->middleware(SuperAdminMiddleware::class)->only(['indexSuper', 'editAdmin', 'updateAdmin', 'assignSuperAdminRole', 'verifyEmail', 'destroy']);
    }

    function index() {
        $users = User::where('role', 'user')->orderBy('name')->get();
        return view('admin.index', compact('users'));
    }

    function indexSuper() {
        $users = User::all();
        return view('superadmin.index', compact('users'));
    }

    function editProfile($id) {
        $user = User::findOrFail($id);
        return view('admin.edit', compact('user'));
    }

    function updateProfile(Request $request, $id) {
        $user = User::findOrFail($id);
    
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $id],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'string', 'in:user,admin']
        ]);

        $updateData = [];
        $nameUpdated = false;
        $emailUpdated = false;
        $passwordUpdated = false;
        $roleUpdated = false;

        if ($validated['name'] !== $user->name) {
            $updateData['name'] = $validated['name'];
            $nameUpdated = true;
        }

        if ($validated['email'] !== $user->email) {
            $updateData['email'] = $validated['email'];
            $updateData['email_verified_at'] = null;
            $emailUpdated = true;
        }

        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
            $passwordUpdated = true;
        }

        if ($validated['role'] !== $user->role) {
            if (auth()->user()->role === 'admin' && !in_array($validated['role'], ['user', 'admin'])) {
                return back()->withErrors(['role' => 'Invalid role selected']);
            }
            $updateData['role'] = $validated['role'];
            $roleUpdated = true;
        }

        if (!empty($updateData)) {
            $user->update($updateData);
            return redirect()->route('admin.index')->with('status', 'User updated successfully');
        }

        return redirect()->route('admin.index')->with('status', 'No changes were made');
    }

    function verifyEmail($id) {
        $user = User::findOrFail($id);
        $verified = request('verified');
        
        $user->email_verified_at = $verified == 1 ? now() : null;
        $user->save();
        
        // Redirigir según el rol del usuario autenticado
        $redirectRoute = auth()->user()->id === 1 ? 'superadmin.index' : 'admin.index';
        
        return redirect()->route($redirectRoute)->with('status', 'Email verification status updated successfully');
    }

    function assignAdminRole($id) {
        $user = User::findOrFail($id);
        $user->role = 'admin';
        $user->save();
        return redirect()->route('admin.index');
    }

    function editAdmin($id) {
        $user = User::findOrFail($id);
        return view('superadmin.edit', [ 'user' => $user ]);
    }

    function updateAdmin(Request $request, $id) {
        $user = User::findOrFail($id);
    
    // Validaciones diferentes según si el usuario es superadmin o no
    if ($user->id === 1) {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $id],
            'password' => ['nullable', 'string', 'min:8', 'confirmed']
        ]);
    } else {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $id],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'string', 'in:user,admin,superadmin']
        ]);
    }

    $updateData = [];

    // Actualizar nombre
    if ($validated['name'] !== $user->name) {
        $updateData['name'] = $validated['name'];
    }

    // Actualizar email
    if ($validated['email'] !== $user->email) {
        $updateData['email'] = $validated['email'];
        $updateData['email_verified_at'] = null;
    }

    // Actualizar contraseña
    if (!empty($validated['password'])) {
        $updateData['password'] = Hash::make($validated['password']);
    }

    // Actualizar rol solo si el usuario no es superadmin
    if (!$user->isSuperAdmin() && isset($validated['role']) && $validated['role'] !== $user->role) {
        $updateData['role'] = $validated['role'];
    }

    if (!empty($updateData)) {
        $user->update($updateData);
        return redirect()->route('superadmin.index')->with('status', 'User updated successfully');
    }

    return redirect()->route('superadmin.index')->with('status', 'No changes were made');
    }

    function assignSuperAdminRole($id) {
        $admin = User::findOrFail($id);
        $admin->role = 'superadmin';
        $admin->save();
        return redirect()->route('superadmin.index');
    }

    public function destroy($id) 
    {
        $user = User::findOrFail($id);

        // Prevenir borrado del superadmin o de sí mismo, aunque por otro lado se ha hecho que no se muestre la opción
        if ($user->id === 1 || $user->id === auth()->id()) {
            return back()->withErrors(['message' => 'Cannot delete this user.']);
        }

        $user->delete();
        
        // Redirigir según el rol del usuario autenticado
        $redirectRoute = auth()->user()->id === 1 ? 'superadmin.index' : 'admin.index';
        return redirect()->route($redirectRoute)->with('status', 'User deleted successfully.');
    }
}