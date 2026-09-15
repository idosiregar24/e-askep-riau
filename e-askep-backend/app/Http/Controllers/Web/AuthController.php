<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use Inertia\Inertia;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            return $this->authenticatedRedirect(Auth::user());
        }

        return Inertia::render('Auth/Login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        // Support login by email OR by nim_nip
        $field = filter_var($credentials['email'], FILTER_VALIDATE_EMAIL) ? 'email' : 'nim_nip';
        $attempt = [
            $field     => $credentials['email'],
            'password' => $credentials['password'],
        ];

        if (Auth::attempt($attempt, $request->boolean('remember'))) {
            $request->session()->regenerate();

            /** @var \App\Models\User $user */
            $user = Auth::user();

            if (! $user->is_active) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()->withErrors(['email' => 'Akun Anda sedang dinonaktifkan. Silakan hubungi administrator.']);
            }

            return $this->authenticatedRedirect($user);
        }

        return back()->withErrors([
            'email' => 'Kredensial yang Anda masukkan tidak sesuai dengan catatan kami.',
        ])->onlyInput('email');
    }

    public function showRegisterForm()
    {
        if (Auth::check()) {
            return $this->authenticatedRedirect(Auth::user());
        }

        return Inertia::render('Auth/Register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'nim_nip'  => ['required', 'string', 'max:50', 'unique:users,nim_nip'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'role'     => ['required', 'in:mahasiswa,dosen'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        $user = \App\Models\User::create([
            'name'      => $validated['name'],
            'nim_nip'   => $validated['nim_nip'],
            'email'     => $validated['email'],
            'role'      => $validated['role'],
            'password'  => \Illuminate\Support\Facades\Hash::make($validated['password']),
            'is_active' => true,
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return $this->authenticatedRedirect($user)->with('success', 'Pendaftaran akun berhasil! Selamat datang di e-Askep Poltekkes Riau.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda telah berhasil keluar dari sistem e-Askep.');
    }

    private function authenticatedRedirect($user)
    {
        if ($user->isAdmin()) {
            return redirect()->intended(route('admin.dashboard'));
        }

        if ($user->isDosen()) {
            return redirect()->intended(route('dosen.dashboard'));
        }

        return redirect()->intended(route('mahasiswa.dashboard'));
    }
}
