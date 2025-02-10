<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth');  //Excepción de autenticación para el método home se aplica a todos los métodos
        $this->middleware('auth')->except(['index']);  //Excepción de autenticación para el método index se aplica a todos los métodos excepto el introducido
        //$this->middleware('auth')->only();  //Excepción de autenticación para el método index solo se aplica al método introducido
        $this->middleware('verified')->only(['verify']);  //Excepción de verificación de correo para el método verify solo se aplica al método introducido
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function home()
    {
        return view('home');
    }

    public function index()
    {
        return view('index');
    }

    public function verificado()
    {
        return view('verificado');
    }

    public function redirectBasedOnRole()
    {
        if (auth()->user()->role === 'superadmin') {
            return redirect()->route('superadmin.index');
        } elseif (auth()->user()->role === 'admin') {
            return redirect()->route('admin.index');
        } else {
            return redirect()->route('home');
        }
    }
}
