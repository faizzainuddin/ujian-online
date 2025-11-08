<?php

namespace App\Http\Controllers;

use App\Models\Question;
use App\Models\QuestionSet;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class TeacherQuestionController extends Controller
{
    public function index(Request $request): View|RedirectResponse
    {
        $teacher = $this->resolveTeacher();
        if (! $teacher) {
            return redirect()->route('login')->withErrors(['auth' => 'Sesi guru tidak ditemukan.']);
        }

        $questionSets = QuestionSet::with('teacher')
            ->withCount('questions')
            ->where('teacher_id', $teacher['id'])
            ->orderByDesc('updated_at')
            ->get();

        return view('guru.questions.index', compact('teacher', 'questionSets'));
    }

    public function create(): View|RedirectResponse
    {
        $teacher = $this->resolveTeacher();
        if (! $teacher) {
            return redirect()->route('login')->withErrors(['auth' => 'Sesi guru tidak ditemukan.']);
        }

        $subjects = ['Bahasa Indonesia', 'Matematika', 'Fisika', 'Biologi'];
        $semesters = ['Semester 1', 'Semester 2', 'Semester 3'];

        return view('guru.questions.create', compact('teacher', 'subjects', 'semesters'));
    }

    public function builder(Request $request, ?QuestionSet $questionSet = null): View|RedirectResponse
    {
        $teacher = $this->resolveTeacher();
        if (! $teacher) {
            return redirect()->route('login')->withErrors(['auth' => 'Sesi guru tidak ditemukan.']);
        }

        $mode = $questionSet ? 'edit' : $request->query('mode', 'create');
        $meta = [
            'subject' => $request->query('subject', 'Bahasa Indonesia'),
            'exam_type' => $request->query('exam_type', 'Ujian Tengah Semester'),
            'semester' => $request->query('semester', 'Semester 1'),
            'class_level' => $request->query('class_level', 'Kelas 12'),
        ];

        $questions = $questionSet
            ? $questionSet->questions
                ->sortBy('order')
                ->values()
                ->map(fn (Question $question) => [
                    'prompt' => $question->prompt,
                    'options' => $question->options,
                    'answer' => $question->answer_index,
                ])
                ->toArray()
            : [];

        if ($questionSet) {
            if ((int) $questionSet->teacher_id !== (int) $teacher['id']) {
                return redirect()->route('teacher.questions.index')->withErrors(['questions' => 'Anda tidak memiliki akses ke set soal tersebut.']);
            }

            $meta = [
                'subject' => $questionSet->subject,
                'exam_type' => $questionSet->exam_type,
                'semester' => $questionSet->semester,
                'class_level' => $questionSet->class_level,
            ];
        }

        $questions = old('questions', $questions);

        if (empty($questions)) {
            $questions = [[
                'prompt' => '',
                'options' => ['', '', '', '', ''],
                'answer' => 0,
            ]];
        }

        return view('guru.questions.builder', compact('teacher', 'mode', 'questionSet', 'meta', 'questions'));
    }

    public function store(Request $request): RedirectResponse
    {
        $teacher = $this->resolveTeacher();
        if (! $teacher) {
            return redirect()->route('login')->withErrors(['auth' => 'Sesi guru tidak ditemukan.']);
        }

        $validated = $this->validateQuestionRequest($request);

        DB::transaction(function () use ($validated, $teacher) {
            $questionSet = QuestionSet::create([
                'teacher_id' => $teacher['id'],
                'subject' => $validated['subject'],
                'exam_type' => $validated['exam_type'],
                'semester' => $validated['semester'],
                'class_level' => $validated['class_level'],
                'description' => Arr::get($validated, 'description'),
            ]);

            foreach ($validated['questions'] as $index => $question) {
                $questionSet->questions()->create([
                    'prompt' => $question['prompt'],
                    'options' => $question['options'],
                    'answer_index' => $question['answer'],
                    'order' => $index + 1,
                ]);
            }
        });

        return redirect()->route('teacher.questions.index')->with('status', 'Set soal baru berhasil disimpan.');
    }

    public function update(Request $request, QuestionSet $questionSet): RedirectResponse
    {
        $teacher = $this->resolveTeacher();
        if (! $teacher) {
            return redirect()->route('login')->withErrors(['auth' => 'Sesi guru tidak ditemukan.']);
        }

        if ((int) $questionSet->teacher_id !== (int) $teacher['id']) {
            return redirect()->route('teacher.questions.index')->withErrors(['questions' => 'Anda tidak memiliki akses ke set soal tersebut.']);
        }

        $validated = $this->validateQuestionRequest($request);

        DB::transaction(function () use ($questionSet, $validated) {
            $questionSet->update([
                'subject' => $validated['subject'],
                'exam_type' => $validated['exam_type'],
                'semester' => $validated['semester'],
                'class_level' => $validated['class_level'],
                'description' => Arr::get($validated, 'description'),
            ]);

            $questionSet->questions()->delete();

            foreach ($validated['questions'] as $index => $question) {
                $questionSet->questions()->create([
                    'prompt' => $question['prompt'],
                    'options' => $question['options'],
                    'answer_index' => $question['answer'],
                    'order' => $index + 1,
                ]);
            }
        });

        return redirect()->route('teacher.questions.index')->with('status', 'Set soal berhasil diperbarui.');
    }

    public function destroy(QuestionSet $questionSet): RedirectResponse
    {
        $teacher = $this->resolveTeacher();
        if (! $teacher) {
            return redirect()->route('login')->withErrors(['auth' => 'Sesi guru tidak ditemukan.']);
        }

        if ((int) $questionSet->teacher_id !== (int) $teacher['id']) {
            return redirect()->route('teacher.questions.index')->withErrors(['questions' => 'Anda tidak memiliki akses ke set soal tersebut.']);
        }

        $questionSet->delete();

        return redirect()->route('teacher.questions.index')->with('status', 'Set soal berhasil dihapus.');
    }

    private function validateQuestionRequest(Request $request): array
    {
        $data = $request->validate([
            'subject' => ['required', 'string', 'max:100'],
            'exam_type' => ['required', 'string', 'max:120'],
            'semester' => ['required', 'string', 'max:60'],
            'class_level' => ['required', 'string', 'max:60'],
            'description' => ['nullable', 'string'],
            'questions' => ['required', 'array', 'min:1'],
            'questions.*.prompt' => ['required', 'string'],
            'questions.*.options' => ['required', 'array', 'min:2'],
            'questions.*.options.*' => ['required', 'string'],
            'questions.*.answer' => ['required', 'integer', 'min:0'],
        ]);

        $questions = collect($data['questions'])
            ->map(function (array $question, int $index) {
                $options = array_values($question['options']);
                $answer = (int) $question['answer'];

                if ($answer >= count($options)) {
                    throw ValidationException::withMessages([
                        "questions.$index.answer" => 'Pilihan jawaban benar harus dipilih dari opsi yang tersedia.',
                    ]);
                }

                return [
                    'prompt' => $question['prompt'],
                    'options' => $options,
                    'answer' => $answer,
                ];
            })
            ->values()
            ->all();

        $data['questions'] = $questions;

        return $data;
    }

    private function resolveTeacher(): ?array
    {
        $teacher = session('teacher');

        if (! $teacher || ! array_key_exists('id', $teacher)) {
            return null;
        }

        return $teacher;
    }
}
