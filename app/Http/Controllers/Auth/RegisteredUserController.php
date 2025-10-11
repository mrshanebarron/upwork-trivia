<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:'.User::class,
            'birthdate' => 'required|date|before:-18 years',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            // Honeypot fields - must be empty
            'website' => 'nullable|max:0',
            'phone' => 'nullable|max:0',
            'company' => 'nullable|max:0',
        ]);

        // Honeypot check - if any honeypot field is filled, it's a bot
        if (!empty($request->website) || !empty($request->phone) || !empty($request->company)) {
            \Log::warning('Registration honeypot triggered', [
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'email' => $request->email,
            ]);

            // Pretend success to not reveal the honeypot
            return redirect(route('login'))->with('status', 'Registration successful! Please log in.');
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'birthdate' => $request->birthdate,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
