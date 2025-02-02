<?php

namespace App\Http\Controllers;

use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\SuperAdminMiddleware;
use App\Models\User;
use Illuminate\Http\Request;

class AdministratorsController extends Controller
{
    function __construct() {
        $this->middleware(AdminMiddleware::class);
        $this->middleware(SuperAdminMiddleware::class)->only(['indexSuper']);
    }

    function index() {
        /* $users = User::where('role', '<>' ,'admin')->orderBy('name')->get(); */
        $users = User::where('id', '<>' ,'1')->orderBy('name')->get();
        dd($users);
    }

    function indexSuper() {
        $users = User::orderBy('name')->get();
        dd($users);
    }
}
