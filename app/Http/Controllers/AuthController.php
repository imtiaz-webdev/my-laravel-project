<?php

namespace App\Http\Controllers;

use App\Models\FarmerProfile;
use App\Models\Market;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole(Auth::user());
        }
        return view('frontend.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            $user = Auth::user();

            if ($user->status === 'suspended') {
                Auth::logout();
                return back()->withErrors(['email' => 'Your account has been suspended by market administrators.']);
            }

            return $this->redirectBasedOnRole($user);
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function showRegister()
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole(Auth::user());
        }
        $markets = Market::where('status', 'active')->get();
        return view('frontend.auth.register', compact('markets'));
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required|string|max:50',
            'address' => 'required|string|max:255',
            'role' => 'required|in:customer,vendor',
            'password' => 'required|string|min:6|confirmed',
            // Vendor specific fields
            'stall_name' => 'required_if:role,vendor|nullable|string|max:255',
            'market_id' => 'required_if:role,vendor|nullable|exists:markets,id',
            'certifications' => 'nullable|string|max:255',
            'bio' => 'nullable|string',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'address' => $validated['address'],
            'role' => $validated['role'],
            'status' => 'active',
            'password' => Hash::make($validated['password']),
        ]);

        if ($user->role === 'vendor') {
            $market = Market::find($validated['market_id']);
            FarmerProfile::create([
                'user_id' => $user->id,
                'stall_name' => $validated['stall_name'],
                'slug' => Str::slug($validated['stall_name']) . '-' . Str::random(4),
                'market_id' => $validated['market_id'],
                'stall_number' => 'Stall ' . strtoupper(Str::random(3)),
                'bio' => $validated['bio'] ?? 'Dedicated to fresh, local, pesticide-free harvest.',
                'lat' => $market ? $market->latitude + (rand(-10, 10) / 10000) : 37.7793,
                'long' => $market ? $market->longitude + (rand(-10, 10) / 10000) : -122.4192,
                'farm_origin' => 'Regional Smallholder Organic Farm',
                'certifications' => $validated['certifications'] ?? 'Naturally Grown Verified',
                'carbon_score_rating' => 4.8,
                'phone_contact' => $validated['phone'],
                'is_approved' => true,
            ]);
        }

        Auth::login($user);
        return $this->redirectBasedOnRole($user);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('home')->with('success', 'You have been successfully signed out.');
    }

    protected function redirectBasedOnRole(User $user)
    {
        return match ($user->role) {
            'admin' => redirect()->route('admin.dashboard'),
            'vendor' => redirect()->route('vendor.dashboard'),
            'customer' => redirect()->route('customer.dashboard'),
            default => redirect()->route('home'),
        };
    }
}
