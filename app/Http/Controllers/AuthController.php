<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AuthController extends Controller
{
    /**
     * Display the login form
     */
    public function showLogin(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('pos');
        }

        return view('auth.login');
    }

    /**
     * Handle cashier authentication with rate-limiting
     */
    public function login(Request $request): RedirectResponse
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $throttleKey = Str::transliterate(Str::lower($request->input('username')) . '|' . $request->ip());

        // Enforce 5 login attempts per minute
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return back()
                ->withInput($request->only('username'))
                ->withErrors([
                    'username' => "Too many login attempts. Please try again in {$seconds} seconds.",
                ]);
        }

        // Attempt login (User model getAuthPassword maps to pwd column)
        if (Auth::attempt($request->only('username', 'password'))) {
            RateLimiter::clear($throttleKey);
            $request->session()->regenerate();

            return redirect()->intended(route('pos'))
                ->with('success', 'Welcome back, ' . Auth::user()->name . '!');
        }

        RateLimiter::hit($throttleKey, 60);

        return back()
            ->withInput($request->only('username'))
            ->withErrors([
                'username' => 'Invalid username or password.',
            ]);
    }

    /**
     * Display the registration form
     */
    public function showRegister(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('pos');
        }

        return view('auth.register');
    }

    /**
     * Handle cashier registration into shared app1.users table
     */
    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'idno'     => ['required', 'string', 'max:50', Rule::unique(User::class, 'idno')],
            'username' => ['required', 'string', 'max:50', Rule::unique(User::class, 'username')],
            'name'     => ['required', 'string', 'max:100'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ], [
            'idno.unique'     => 'A cashier with this ID Number is already registered.',
            'username.unique' => 'This username is already taken.',
        ]);

        // Insert into app1.users with bcrypt hash (pwd column)
        User::create([
            'idno'     => trim($validated['idno']),
            'username' => trim($validated['username']),
            'pwd'      => Hash::make($validated['password']),
            'name'     => trim($validated['name']),
        ]);

        return redirect()->route('login')
            ->with('success', 'Account registered successfully! You may now sign in.');
    }

    /**
     * Log out the cashier and invalidate the session
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('info', 'You have been safely signed out. Have a great day!');
    }
}

