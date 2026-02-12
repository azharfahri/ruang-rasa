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
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $user = auth()->user();


        // Menggunakan contains untuk mengecek nama role di dalam collection roles
        if ($user->roles->contains('name', 'admin')) {
            return redirect()->route('admin.dashboard');
        }

        if ($user->roles->contains('name', 'admincabang')) {
            return redirect()->route('admincabang.dashboard');
        }

        if ($user->roles->contains('name', 'cashier')) {
            return redirect()->route('cashier.dashboard');
        }

        return view('home');
    }

    public function create(){
        
    }
}
