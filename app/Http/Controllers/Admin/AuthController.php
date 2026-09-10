<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('admin.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => [
                'required',
                'email',
            ],
            'password' => [
                'required',
            ],
        ]);

        $remember = $request->boolean('remember');

        if (Auth::guard('admin')->attempt($credentials, $remember)) {

            $request->session()->regenerate();

            $admin = Auth::guard('admin')->user();

            if (! $admin->status) {
                Auth::guard('admin')->logout();

                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()->withErrors([
                    'email' => 'Your admin account is inactive.',
                ]);
            }

            if ($admin->isMemberManager()) {
                return redirect()->route('admin.dashboard');
            }

            if ($admin->hasRole('content-manager') && ! $admin->hasRole('super-admin')) {
                return redirect()->route('admin.blog-posts.index');
            }

            return redirect()->intended(
                route('admin.dashboard')
            );
        }

        return back()
            ->withErrors([
                'email' => 'Invalid email or password.',
            ])
            ->onlyInput('email');
    }

    public function editPassword()
    {
        return view('admin.settings.change-password');
    }

    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password:admin'],
            'password' => ['required', 'string', 'min:8', 'confirmed', 'different:current_password'],
        ]);

        $admin = $request->user('admin');
        $admin->password = $validated['password'];
        $admin->setRememberToken(Str::random(60));
        $admin->save();

        $request->session()->regenerate();

        return redirect()->route('admin.settings.password.edit')
            ->with('success', 'Your password has been changed successfully.');
    }

    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}
