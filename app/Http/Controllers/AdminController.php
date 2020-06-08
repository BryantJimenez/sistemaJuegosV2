<?php

namespace App\Http\Controllers;

use App\Club;
use App\Gamer;
use App\User;
use App\Tournament;
use Illuminate\Http\Request;

class AdminController extends Controller
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
    public function index() {
        $gamers=Gamer::all()->count();
        $clubs=Club::all()->count();
        $tournaments=Tournament::all()->count();
        $active=User::where('state', 1)->count();
        $inactive=User::where('state', 2)->count();
        return view('admin.home', compact('gamers', 'clubs', 'tournaments', 'active', 'inactive'));
    }
}
