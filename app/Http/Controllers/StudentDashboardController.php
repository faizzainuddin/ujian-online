<?php

namespace App\Http\Controllers;

use App\Models\ExamSchedule;
use App\Models\HasilUjian;
use App\Models\QuestionSet;
use App\Models\Ujian;
use App\Services\ExamService;
use Carbon\Carbon;
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

        // Konteks SMK: Semester Ganjil (1,3,5..) hanya menampilkan UTS, Genap (2,4,6..) hanya UAS.
        preg_match('/\d+/', $activeSemester, $matches);

// Ubah hasil regex jadi integer
        $semesterInt = isset($matches[0]) ? (int) $matches[0] : 0;

// Logika ganjil/genap
    if ($semesterInt % 2 !== 0) {
        $results = ['UTS' => $results['UTS'] ?? []];
    } else {
        $results = ['UAS' => $results['UAS'] ?? []];
    }

        return view('siswa.nilai', compact('student', 'results', 'availableSemesters', 'activeSemester'));
    }

    public function takeExam(Request $request, int $ujianId): View
    {
        $user = auth()->user();

        $defaultName = $user ? $user->nama_siswa : 'Student';
        $defaultInitials = $user ? strtoupper(substr($user->nama_siswa, 0, 2)) : 'ST';

        $student = $request->session()->get('student', [
            'name' => $defaultName,
            'role' => 'Student',
            'initials' => $defaultInitials,
        ]);

        // Ambil data jadwal ujian beserta soal-soalnya
        $examSchedule = ExamSchedule::with(['questionSet.questions' => function ($query) {
            $query->orderBy('order');
        }])->findOrFail($ujianId);

        $questions = $examSchedule->questionSet->questions;
        $totalQuestions = $questions->count();
        $requestedQuestion = (int) $request->get('q', 1);

        // Batasi linear: hanya boleh akses soal yang sedang dibuka
        $unlocked = (int) $request->session()->get("exam_{$ujianId}_unlocked", 1);
        $request->session()->put("exam_{$ujianId}_unlocked", $unlocked); // pastikan terset
        $currentQuestion = max(1, min($requestedQuestion, $totalQuestions));
        if ($currentQuestion !== $unlocked) {
            return redirect()->route('student.exam.take', ['ujianId' => $ujianId, 'q' => $unlocked]);
        }
        
        // Ambil jawaban yang sudah dipilih dari session
        $selectedAnswer = $request->session()->get("exam_{$ujianId}_q{$currentQuestion}", null);

        // Ambil soal saat ini (index mulai dari 0)
        $currentQuestionData = $questions[$currentQuestion - 1] ?? null;
        
        $question = [
            'text' => $currentQuestionData?->prompt ?? 'Soal tidak ditemukan',
            'options' => $currentQuestionData?->options ?? [],
        ];

        // Timer per soal: 30 detik
        $perQuestionDuration = 30;

        $startTime = $request->session()->get("exam_{$ujianId}_q{$currentQuestion}_start");
        if (! $startTime) {
            $startTime = now()->timestamp;
            $request->session()->put("exam_{$ujianId}_q{$currentQuestion}_start", $startTime);
        }

        $elapsed = now()->timestamp - $startTime;
        $remaining = max(0, $perQuestionDuration - $elapsed);

        $minutes = floor($remaining / 60);
        $seconds = $remaining % 60;
        $timeRemaining = sprintf('%02d:%02d', $minutes, $seconds);

        // Data tambahan untuk view
        $examTitle = $examSchedule->questionSet->subject ?? 'Ujian';

        return view('siswa.exam-taking', compact(
            'student',
            'totalQuestions',
            'currentQuestion',
            'selectedAnswer',
            'question',
            'timeRemaining',
            'ujianId',
            'examTitle'
        ));
    }

    public function saveAnswer(Request $request, int $ujianId)
    {
        $questionNumber = $request->input('question_number');
        $answer = $request->input('answer');
        $action = $request->input('action');

        // Simpan jawaban ke session
        if ($answer !== null) {
            $request->session()->put("exam_{$ujianId}_q{$questionNumber}", $answer);
        }

        // Ambil total soal untuk validasi
        $examSchedule = ExamSchedule::with('questionSet.questions')->findOrFail($ujianId);
        $totalQuestions = $examSchedule->questionSet->questions->count();

        // Reset timer untuk soal berikutnya
        $request->session()->forget("exam_{$ujianId}_q{$questionNumber}_start");

        // Jika masih ada soal berikutnya, buka soal berikutnya
        if ($questionNumber < $totalQuestions) {
            $nextQuestion = $questionNumber + 1;
            $request->session()->put("exam_{$ujianId}_unlocked", $nextQuestion);
            return redirect()->route('student.exam.take', ['ujianId' => $ujianId, 'q' => $nextQuestion]);
        }

        // Hanya boleh selesai jika sedang di soal terakhir
        if ($action === 'finish' && $questionNumber === $totalQuestions) {
            return redirect()->route('student.exam.finish', ['ujianId' => $ujianId]);
        }

        // Default: tetap di soal terakhir jika belum valid
        return redirect()->route('student.exam.take', ['ujianId' => $ujianId, 'q' => $totalQuestions]);
    }

    public function finishExam(Request $request, int $ujianId): View
    {
        $user = auth()->user();
        $siswaId = $user?->siswa_id ?? 1;

        // Ambil data ujian
        $examSchedule = ExamSchedule::with(['questionSet.questions' => function ($query) {
            $query->orderBy('order');
        }])->findOrFail($ujianId);

        $questions = $examSchedule->questionSet->questions;
        $totalQuestions = $questions->count();

        // Hitung nilai
        $correctAnswers = 0;
        foreach ($questions as $index => $question) {
            $questionNumber = $index + 1;
            $studentAnswer = $request->session()->get("exam_{$ujianId}_q{$questionNumber}");
            
            if ($studentAnswer !== null && (int) $studentAnswer === $question->answer_index) {
                $correctAnswers++;
            }
        }

        $score = $totalQuestions > 0 ? round(($correctAnswers / $totalQuestions) * 100) : 0;

        // Hapus data session ujian
        for ($i = 1; $i <= $totalQuestions; $i++) {
            $request->session()->forget("exam_{$ujianId}_q{$i}");
        }
        $request->session()->forget("exam_{$ujianId}_start");

        // Data untuk view
        $student = $request->session()->get('student', [
            'name' => $user?->nama_siswa ?? 'Student',
            'role' => 'Student',
            'initials' => $user ? strtoupper(substr($user->nama_siswa, 0, 2)) : 'ST',
        ]);

        $examTitle = $examSchedule->questionSet->subject ?? 'Ujian';

        return view('siswa.exam-result', compact(
            'student',
            'examTitle',
            'score',
            'correctAnswers',
            'totalQuestions'
        ));
    }
}
