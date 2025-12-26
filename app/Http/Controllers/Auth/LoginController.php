<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect('/');
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Onjuiste email of wachtwoord',
                ], 401);
            }
            return back()->withErrors(['email' => 'Onjuiste email of wachtwoord']);
        }

        Auth::login($user, $request->boolean('remember'));

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'setup_pin' => true,
                'redirect' => '/auth/setup-pin',
            ]);
        }

        // Redirect to PIN setup after password login
        return redirect('/auth/setup-pin');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }

    public function setupPin()
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        return view('auth.setup-pin');
    }
}
