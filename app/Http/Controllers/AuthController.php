<?php

namespace App\Http\Controllers;

use App\Models\InventoryUser;
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

        // Pass Group 3 canteen accounts for easy cashier evaluation
        $canteenUsers = User::where('is_active', true)->orderBy('id')->get();

        return view('auth.login', compact('canteenUsers'));
    }

    /**
     * Handle authentication for Canteen POS Cashiers and Staff
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

        // Attempt login
        if (Auth::attempt($request->only('username', 'password'))) {
            // Guard: Disallow Inventory users on Canteen POS
            if (method_exists(Auth::user(), 'isInventoryUser') && Auth::user()->isInventoryUser()) {
                Auth::logout();
                return back()
                    ->withInput($request->only('username'))
                    ->withErrors([
                        'username' => 'Access denied. Inventory staff accounts must sign in at the Inventory System on port 8001.',
                    ]);
            }

            RateLimiter::clear($throttleKey);
            $request->session()->regenerate();

            // Record last login
            Auth::user()->update(['last_login_at' => now()]);

            return redirect()->intended(route('pos'))
                ->with('success', "Welcome to Canteen Dawn, " . Auth::user()->name . "!");
        }

        RateLimiter::hit($throttleKey, 60);

        return back()
            ->withInput($request->only('username'))
            ->withErrors([
                'username' => 'Invalid username or password.',
            ]);
    }

    /**
     * Quick 1-click login switcher for demonstration/presentation
     */
    public function quickLogin(Request $request): RedirectResponse
    {
        $username = $request->input('username');

        // Check Canteen Users only
        $canteen = User::where('username', $username)->first();
        if ($canteen) {
            session(['auth_system' => 'canteen']);
            Auth::login($canteen);
            $request->session()->regenerate();
            return redirect()->route('pos')->with('success', "Active session: {$canteen->name} (Canteen POS)");
        }

        return redirect()->route('login')->with('error', 'Only Canteen POS team members can sign in to this terminal.');
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
     * Handle cashier registration into canteen.users table
     */
    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'username' => ['required', 'string', 'max:50', Rule::unique(User::class, 'username')],
            'name'     => ['required', 'string', 'max:100'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ], [
            'username.unique' => 'This username is already taken.',
        ]);

        // Insert into canteen.users with bcrypt hash
        $user = User::create([
            'name'        => trim($validated['name']),
            'username'    => trim($validated['username']),
            'password'    => Hash::make($validated['password']),
            'role'        => 'cashier',
            'is_active'   => true,
        ]);

        session(['auth_system' => 'canteen']);
        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('pos')
            ->with('success', 'Registration successful! Welcome to Canteen Dawn, ' . $user->name . '.');
    }

    /**
     * Log out current user
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('info', 'You have been logged out securely.');
    }
}
