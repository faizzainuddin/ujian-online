<?php

namespace App\Http\Controllers;

use App\Models\HasilUjian;
use App\Models\QuestionSet;
use App\Models\Ujian;
use App\Services\ExamService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StudentDashboardController extends Controller
{
    private ExamService $examService;

    public function __construct(ExamService $examService)
    {
        $this->examService = $examService;
    }

    public function index(Request $request): View
    {
        $user = auth()->user();

        $defaultName = $user ? $user->nama_siswa : 'Student';
        $defaultInitials = $user ? strtoupper(substr($user->nama_siswa, 0, 2)) : 'ST';
        $defaultClass = $user ? $user->kelas : null;

        /** @var array{name:string,role:string,initials:string,class:?string} $student */
        $student = $request->session()->get('student', [
            'name' => $defaultName,
            'role' => 'Student',
            'initials' => $defaultInitials,
            'class' => $defaultClass,
        ]);

        $quickLinks = [
            [
                'label' => 'Ujian',
                'description' => 'Lihat jadwal dan kerjakan ujian yang tersedia.',
                'icon' => asset('assets/img/icon-question.svg'),
                'href' => route('student.exams'),
            ],
            [
                'label' => 'Nilai Ujian',
                'description' => 'Pantau nilai ujian yang sudah kamu selesaikan.',
                'icon' => asset('assets/img/icon-result.svg'),
                'href' => route('student.nilai'),
            ],
        ];

        $announcement = [
            'title' => 'Pengumuman Ujian Akhir Semester (UAS)',
            'body' => 'Diberitahukan kepada seluruh siswa kelas X, XI, dan XII bahwa Ujian Akhir Semester (UAS) '
                .'akan dilaksanakan mulai Senin, 5 januari 2026 hingga Jumat, 9 januari 2026 secara online melalui platform TrustExam.',
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

    public function exams(Request $request): View
    {
        $user = auth()->user();

        $defaultName = $user ? $user->nama_siswa : 'Student';
        $defaultInitials = $user ? strtoupper(substr($user->nama_siswa, 0, 2)) : 'ST';
        $defaultClass = $user ? $user->kelas : null;

        /** @var array{name:string,role:string,initials:string,class:?string} $student */
        $student = $request->session()->get('student', [
            'name' => $defaultName,
            'role' => 'Student',
            'initials' => $defaultInitials,
            'class' => $defaultClass,
        ]);

        $studentClass = $student['class'] ?? $defaultClass ?? null;
        $exams = $this->examService->getExamsForStudent($studentClass);

        // Jika kelas tidak terisi atau tidak ada ujian untuk kelas tersebut, tampilkan semua jadwal sebagai fallback
        if ($exams->isEmpty()) {
            $exams = $this->examService->getExamsForStudent();
        }

        return view('siswa.exams', compact('student', 'exams'));
    }

    public function nilai(Request $request, ?string $semester = null): View
    {
        $user = auth()->user();

        $defaultName = $user ? $user->nama_siswa : 'Student';
        $defaultInitials = $user ? strtoupper(substr($user->nama_siswa, 0, 2)) : 'ST';
        $defaultClass = $user ? $user->kelas : null;
        $defaultSiswaId = $user ? $user->siswa_id : 1;

        $student = $request->session()->get('student', [
            'name' => $defaultName,
            'role' => 'Student',
            'initials' => $defaultInitials,
            'class' => $defaultClass,
        ]);

        $siswaId = $request->session()->get('siswa_id', $defaultSiswaId);

        $availableSemesters = QuestionSet::distinct('semester')->pluck('semester')->sort()->toArray();

        $activeSemester = $semester ?: ($availableSemesters[0] ?? null);

        $results = [];

        if ($activeSemester) {
            $resultsData = HasilUjian::select([
                'question_sets.subject',
                'question_sets.exam_type',
                'hasil_ujian.nilai',
            ])
                ->join('ujian', 'hasil_ujian.ujian_id', '=', 'ujian.ujian_id')
                ->join('question_sets', 'ujian.question_set_id', '=', 'question_sets.id')
                ->where('hasil_ujian.siswa_id', $siswaId)
                ->where('question_sets.semester', $activeSemester)
                ->get();

            $results = $resultsData->groupBy('exam_type')
                ->map(function ($items) {
                    return $items->map(function ($item, $index) {
                        $kkm = 75;
                        $isLulus = $item->nilai >= $kkm;

                        return [
                            'no' => $index + 1,
                            'subject' => $item->subject,
                            'nilai' => (int) $item->nilai,
                            'kkm' => $kkm,
                            'status' => $isLulus ? 'Lulus' : 'Tidak Lulus',
                        ];
                    })->values();
                })
                ->toArray();
        }

        $results['UTS'] = $results['UTS'] ?? [];
        $results['UAS'] = $results['UAS'] ?? [];

        return view('siswa.nilai', compact('student', 'results', 'availableSemesters', 'activeSemester'));
    }
}
