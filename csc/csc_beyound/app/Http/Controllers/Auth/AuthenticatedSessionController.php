<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
{
    $request->authenticate();

    $user = Auth::user(); 

    if ($user->activated == 0) {
        Auth::logout(); 

        return redirect()->route('login') 
            ->withInput($request->only('email', 'remember')) 
            ->with('warning', 'Your account is not active. Please wait for activation or contact the administrator.');
    }

    $url = '';

    if ($user->role === 'teacher') {
        $url = 'admin/dashboard';
    } elseif ($user->role === 'student') {
        $url = 'student/dashboard';
    }

    return redirect()->intended($url);
}

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
