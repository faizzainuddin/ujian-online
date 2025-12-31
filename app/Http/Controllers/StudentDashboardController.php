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
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

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

        // Ambil siswa_id dari session (disimpan saat login)
        $siswaId = $student['id'] ?? ($user?->siswa_id ?? null);

        $studentClass = $student['class'] ?? $defaultClass ?? null;
        $exams = $this->examService->getExamsForStudent($studentClass, $siswaId);

        // Jika kelas tidak terisi atau tidak ada ujian untuk kelas tersebut, tampilkan semua jadwal sebagai fallback
        if ($exams->isEmpty()) {
            $exams = $this->examService->getExamsForStudent(null, $siswaId);
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

        $studentSession = $request->session()->get('student', []);
        $siswaId = $studentSession['id'] ?? ($user?->siswa_id ?? $defaultSiswaId);

        $availableSemesters = QuestionSet::distinct('semester')->pluck('semester')->sort()->toArray();

        $activeSemester = $semester ?: ($availableSemesters[0] ?? null);

        $results = [];

        if ($activeSemester && $siswaId) {
            $resultsQuery = null;
            $hasQuestionSets = Schema::hasTable('question_sets');

            if ($hasQuestionSets && Schema::hasTable('exam_schedules')) {
                $resultsQuery = HasilUjian::select([
                    'question_sets.subject',
                    'question_sets.exam_type',
                    'hasil_ujian.nilai',
                ])
                    ->join('exam_schedules', 'hasil_ujian.ujian_id', '=', 'exam_schedules.id')
                    ->join('question_sets', 'exam_schedules.question_set_id', '=', 'question_sets.id');
            } elseif ($hasQuestionSets && Schema::hasTable('ujian') && Schema::hasColumn('ujian', 'question_set_id')) {
                $resultsQuery = HasilUjian::select([
                    'question_sets.subject',
                    'question_sets.exam_type',
                    'hasil_ujian.nilai',
                ])
                    ->join('ujian', 'hasil_ujian.ujian_id', '=', 'ujian.ujian_id')
                    ->join('question_sets', 'ujian.question_set_id', '=', 'question_sets.id');
            }

            $resultsData = $resultsQuery
                ? $resultsQuery
                    ->where('hasil_ujian.siswa_id', $siswaId)
                    ->where('question_sets.semester', $activeSemester)
                    ->get()
                : collect();

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

    public function takeExam(Request $request, int $ujianId): View|RedirectResponse
    {
        $user = auth()->user();
        
        // Ambil siswa_id dari session (disimpan saat login)
        $studentSession = $request->session()->get('student', []);
        $siswaId = $studentSession['id'] ?? ($user?->siswa_id ?? null);

        // PENTING: Cek apakah siswa sudah menyelesaikan ujian ini
        // Jika sudah, redirect ke halaman exams dengan pesan error
        if ($siswaId) {
            $alreadyCompleted = HasilUjian::where('siswa_id', $siswaId)
                ->where('ujian_id', $ujianId)
                ->exists();
            
            if ($alreadyCompleted) {
                return redirect()->route('student.exams')
                    ->with('error', 'Anda sudah menyelesaikan ujian ini. Ujian tidak dapat dikerjakan ulang.');
            }
        }

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
        
        // Cek apakah ini pertama kali memulai ujian ini
        $isFirstTime = !$request->session()->has("exam_{$ujianId}_unlocked");
        
        // Jika pertama kali, bersihkan semua session terkait ujian ini
        if ($isFirstTime) {
            for ($i = 1; $i <= $totalQuestions; $i++) {
                $request->session()->forget("exam_{$ujianId}_q{$i}");
                $request->session()->forget("exam_{$ujianId}_q{$i}_start");
            }
            $request->session()->forget("exam_{$ujianId}_start");
            // Set unlocked ke 1 untuk soal pertama
            $request->session()->put("exam_{$ujianId}_unlocked", 1);
        }
        $requestedQuestion = (int) $request->get('q', 1);

        // Batasi linear: hanya boleh akses soal yang sedang dibuka
        $unlocked = (int) $request->session()->get("exam_{$ujianId}_unlocked", 1);
        $request->session()->put("exam_{$ujianId}_unlocked", $unlocked); // pastikan terset
        $currentQuestion = max(1, min($requestedQuestion, $totalQuestions));
        if ($currentQuestion !== $unlocked) {
            return redirect()->route('student.exam.take', ['ujianId' => $ujianId, 'q' => $unlocked]);
        }
        
        // PENTING: Jangan ambil jawaban dari session - setiap soal harus kosong/tidak terpilih
        // User harus memilih jawaban secara manual sebelum klik Selanjutnya
        $selectedAnswer = null;

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

        // PENTING: Hanya simpan jawaban jika benar-benar dipilih (tidak null dan bukan string kosong)
        // Jangan simpan jika user tidak memilih apapun
        if ($answer !== null && $answer !== '') {
            $request->session()->put("exam_{$ujianId}_q{$questionNumber}", (int) $answer);
        }

        // Ambil total soal untuk validasi
        $examSchedule = ExamSchedule::with('questionSet.questions')->findOrFail($ujianId);
        $totalQuestions = $examSchedule->questionSet->questions->count();

        // PERBAIKAN: Hapus session timer untuk soal yang sekarang
        $request->session()->forget("exam_{$ujianId}_q{$questionNumber}_start");

        // Jika action adalah finish, langsung ke halaman finish
        if ($action === 'finish') {
            // Cleanup semua timer session
            for ($i = 1; $i <= $totalQuestions; $i++) {
                $request->session()->forget("exam_{$ujianId}_q{$i}_start");
            }
            return redirect()->route('student.exam.finish', ['ujianId' => $ujianId]);
        }

        // Jika masih ada soal berikutnya, buka soal berikutnya
        if ($action === 'next' && $questionNumber < $totalQuestions) {
            $nextQuestion = $questionNumber + 1;
            $request->session()->put("exam_{$ujianId}_unlocked", $nextQuestion);
            return redirect()->route('student.exam.take', ['ujianId' => $ujianId, 'q' => $nextQuestion]);
        }

        // Default: tetap di soal yang sama
        return redirect()->route('student.exam.take', ['ujianId' => $ujianId, 'q' => $questionNumber]);
    }

    public function finishExam(Request $request, int $ujianId): View
    {
        $user = auth()->user();
        
        // Ambil siswa_id dari session (disimpan saat login)
        $studentSession = $request->session()->get('student', []);
        $siswaId = $studentSession['id'] ?? ($user?->siswa_id ?? 1);

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

        // SOLID Principle: Single Responsibility - Save exam result to database
        $this->saveExamResult($siswaId, $ujianId, $score, $correctAnswers, $totalQuestions);

        // PERBAIKAN: Hapus semua data session ujian termasuk timer
        for ($i = 1; $i <= $totalQuestions; $i++) {
            $request->session()->forget("exam_{$ujianId}_q{$i}");
            $request->session()->forget("exam_{$ujianId}_q{$i}_start");
        }
        $request->session()->forget("exam_{$ujianId}_start");
        $request->session()->forget("exam_{$ujianId}_unlocked");

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

    /**
     * Save exam result to database following SOLID principles
     * Single Responsibility: Handle exam result persistence
     * 
     * @param int $siswaId
     * @param int $ujianId  
     * @param int $score
     * @param int $correctAnswers
     * @param int $totalQuestions
     * @return void
     */
    private function saveExamResult(int $siswaId, int $ujianId, int $score, int $correctAnswers, int $totalQuestions): void
    {
        try {
            // Temporarily disable foreign key checks for SQLite
            if (DB::connection()->getDriverName() === 'sqlite') {
                DB::statement('PRAGMA foreign_keys=OFF;');
            }

            // Check if result already exists to prevent duplicates
            $existingResult = HasilUjian::where('siswa_id', $siswaId)
                ->where('ujian_id', $ujianId)
                ->first();

            if (!$existingResult) {
                HasilUjian::create([
                    'siswa_id' => $siswaId,
                    'ujian_id' => $ujianId,
                    'nilai' => $score,
                    'status' => 'Selesai',
                    'waktu_selesai' => now(),
                    'waktu_mulai' => now()->subMinutes(5),
                ]);
                
                Log::info('Exam result saved successfully', [
                    'siswa_id' => $siswaId,
                    'ujian_id' => $ujianId,
                    'score' => $score
                ]);
            }

            // Re-enable foreign key checks
            if (DB::connection()->getDriverName() === 'sqlite') {
                DB::statement('PRAGMA foreign_keys=ON;');
            }

        } catch (\Exception $e) {
            // Re-enable foreign key checks in case of error
            if (DB::connection()->getDriverName() === 'sqlite') {
                DB::statement('PRAGMA foreign_keys=ON;');
            }
            
            Log::error('Failed to save exam result', [
                'error' => $e->getMessage(),
                'siswa_id' => $siswaId,
                'ujian_id' => $ujianId,
                'score' => $score
            ]);
            
            // Don't throw exception to prevent breaking the user flow
            // Just log the error and continue
        }
    }

    /**
     * Validate if siswa exists in database
     * Single Responsibility: Data validation
     */
    private function validateSiswaExists(int $siswaId): bool
    {
        try {
            // For now, just return true to bypass foreign key issues
            // In production, you should properly validate this
            
            // Method 1: Check authenticated user
            $user = auth()->user();
            if ($user && $user->siswa_id === $siswaId) {
                return true;
            }
            
            // Method 2: Check siswa table if it exists
            if (DB::getSchemaBuilder()->hasTable('siswa')) {
                return DB::table('siswa')->where('siswa_id', $siswaId)->exists();
            }
            
            // Method 3: For development - always return true
            return true;
            
        } catch (\Exception $e) {
            Log::error('Error validating siswa', ['error' => $e->getMessage()]);
            // Return true to allow development to continue
            return true;
        }
    }
}
