<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AdminAuthController extends Controller
{
    public function showLoginForm(Request $request): View|RedirectResponse
    {
        if ($request->session()->get('admin_logged_in')) {
            return redirect()->route('admin.dashboard');
        }

        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'username' => ['required', 'string', 'max:50'],
            'password' => ['required', 'string'],
            'captcha' => ['required', 'string', 'size:5'],
        ], [
            'captcha.size' => 'Kode keamanan harus terdiri dari 5 karakter.',
        ]);

        if (strtolower($validated['captcha']) !== 'vm9fe') {
            return back()
                ->withInput($request->except('password'))
                ->withErrors(['captcha' => 'Kode keamanan tidak sesuai.']);
        }

        $admin = Admin::where('username', $validated['username'])->first();

        if (! $admin || ! Hash::check($validated['password'], $admin->password)) {
            return back()
                ->withInput($request->except('password'))
                ->withErrors(['username' => 'Username atau password salah.']);
        }

        $request->session()->put('admin_logged_in', true);
        $request->session()->put('admin', [
            'id' => $admin->admin_id,
            'username' => $admin->username,
            'name' => $admin->nama_admin,
            'role' => 'Admin',
            'initials' => $this->generateInitials($admin->nama_admin),
        ]);

        return redirect()->route('admin.dashboard');
    }

    public function logout(Request $request): RedirectResponse
    {
        $request->session()->forget(['admin_logged_in', 'admin']);
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('status', 'Anda telah keluar dari sesi admin.');
    }

    private function generateInitials(string $name): string
    {
        $words = preg_split('/\s+/', trim($name));
        $initials = '';
        foreach ($words as $word) {
            $initials .= mb_strtoupper(mb_substr($word, 0, 1));
            if (strlen($initials) === 2) {
                break;
            }
        }

        return $initials ?: 'AD';
    }
}
