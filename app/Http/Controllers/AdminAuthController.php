<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Guru;
use App\Models\Siswa;
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
        if ($request->session()->get('teacher_logged_in')) {
            return redirect()->route('teacher.dashboard');
        }
        if ($request->session()->get('student_logged_in')) {
            return redirect()->route('student.dashboard');
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

        $teacher = Guru::where('username', $validated['username'])->first();
        if ($teacher && Hash::check($validated['password'], $teacher->password)) {
            $request->session()->forget(['admin_logged_in', 'admin', 'student_logged_in', 'student']);
            $request->session()->put('teacher_logged_in', true);
            $request->session()->put('teacher', [
                'id' => $teacher->guru_id,
                'username' => $teacher->username,
                'name' => $teacher->nama_guru,
                'role' => 'Teacher',
                'initials' => $this->generateInitials($teacher->nama_guru),
            ]);

            return redirect()->route('teacher.dashboard');
        }

        $student = Siswa::where('username', $validated['username'])->first();
        if ($student && Hash::check($validated['password'], $student->password)) {
            if (strtolower($student->status) === 'nonaktif') {
                return back()
                    ->withInput($request->except('password'))
                    ->withErrors(['username' => 'Akun siswa sedang dinonaktifkan. Silakan hubungi admin.']);
            }

            $request->session()->forget([
                'admin_logged_in',
                'admin',
                'teacher_logged_in',
                'teacher',
            ]);

            $request->session()->put('student_logged_in', true);
            $request->session()->put('student', [
                'id' => $student->siswa_id,
                'username' => $student->username,
                'name' => $student->nama_siswa,
                'role' => 'Student',
                'class' => $student->kelas,
                'initials' => $this->generateInitials($student->nama_siswa),
            ]);

            return redirect()->route('student.dashboard');
        }

        $admin = Admin::where('username', $validated['username'])->first();

        if (! $admin || ! Hash::check($validated['password'], $admin->password)) {
            return back()
                ->withInput($request->except('password'))
                ->withErrors(['username' => 'Username atau password salah.']);
        }

        $request->session()->forget(['teacher_logged_in', 'teacher', 'student_logged_in', 'student']);
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
        $request->session()->forget([
            'admin_logged_in',
            'admin',
            'teacher_logged_in',
            'teacher',
            'student_logged_in',
            'student',
        ]);
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('status', 'Anda telah keluar dari sesi.');
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
