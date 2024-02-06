<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;

use Illuminate\Http\Request;

class DashBoardController extends Controller
{
    public function index()
    {
        $user = Auth::user(); 
        return view('admin.index', compact('user'));
    }


}
