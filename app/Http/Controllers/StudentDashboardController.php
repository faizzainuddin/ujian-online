<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class StudentDashboardController extends Controller
{
    public function index(Request $request): View
    {
        /** @var array{name:string,role:string,initials:string,class:?string} $student */
        $student = $request->session()->get('student', [
            'name' => 'Student',
            'role' => 'Student',
            'initials' => 'ST',
            'class' => null,
        ]);

        $quickLinks = [
            [
                'label' => 'Ujian',
                'description' => 'Lihat jadwal dan kerjakan ujian yang tersedia.',
                'icon' => asset('assets/img/icon-question.svg'),
                'href' => '#',
            ],
            [
                'label' => 'Nilai Ujian',
                'description' => 'Pantau nilai ujian yang sudah kamu selesaikan.',
                'icon' => asset('assets/img/icon-result.svg'),
                'href' => '#',
            ],
        ];

        $announcement = [
            'title' => 'Pengumuman Ujian Akhir Semester (UAS)',
            'body' => 'Diberitahukan kepada seluruh siswa kelas X, XI, dan XII bahwa Ujian Akhir Semester (UAS) '
                .'akan dilaksanakan mulai Senin, 24 Juni 2025 hingga Jumat, 28 Juni 2025 secara online melalui platform TrustExam.',
            'guidelines' => [
                'Siswa wajib login 15 menit sebelum ujian dimulai.',
                'Setiap mata pelajaran memiliki batas waktu yang telah ditentukan.',
                'Setiap soal di beri waktu 30 detik untuk mengerjakan.',
                'Dilarang membuka tab lain selama ujian berlangsung (fitur anti-cheating aktif).',
                'Gunakan perangkat yang stabil dan koneksi internet yang baik.',
            ],
            'footer' => 'Jadwal lengkap dan detail sesi ujian dapat diakses melalui menu "Ujian". Jika ada pertanyaan, hubungi wali kelas masing-masing.',
        ];

        return view('siswa.dashboard', compact('student', 'quickLinks', 'announcement'));
    }
}
