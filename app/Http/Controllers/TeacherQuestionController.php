<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TeacherQuestionController extends Controller
{
    public function index(Request $request): View
    {
        $teacher = session('teacher', [
            'name' => 'Ibu Siti Nurhaliza',
            'role' => 'Teacher',
            'initials' => 'IS',
        ]);

        $questionSets = collect($request->session()->get('teacher_questions', []));

        return view('guru.questions.index', compact('teacher', 'questionSets'));
    }

    public function create(Request $request): View
    {
        $teacher = session('teacher', [
            'name' => 'Ibu Siti Nurhaliza',
            'role' => 'Teacher',
            'initials' => 'IS',
        ]);

        $subjects = ['Bahasa Indonesia', 'Matematika', 'Fisika', 'Biologi'];
        $semesters = ['Semester 1', 'Semester 2', 'Semester 3'];

        return view('guru.questions.create', compact('teacher', 'subjects', 'semesters'));
    }

    public function builder(Request $request, ?int $id = null): View
    {
        $teacher = session('teacher', [
            'name' => 'Ibu Siti Nurhaliza',
            'role' => 'Teacher',
            'initials' => 'IS',
        ]);

        $mode = $id ? 'edit' : $request->query('mode', 'create');
        $questionSet = null;
        $questionSets = collect($request->session()->get('teacher_questions', []));

        $meta = [
            'subject' => $request->query('subject', 'Bahasa Indonesia'),
            'exam_type' => $request->query('exam_type', 'Ujian Tengah Semester'),
            'semester' => $request->query('semester', 'Semester 1'),
            'class_level' => $request->query('class_level', 'Kelas 12'),
        ];

        if ($mode === 'edit') {
            $questionSet = $questionSets->firstWhere('id', $id);

            if (! $questionSet) {
                return redirect()
                    ->route('teacher.questions.index')
                    ->withErrors(['questions' => 'Set soal tidak ditemukan atau telah dihapus.']);
            }

            $meta = [
                'subject' => $questionSet['subject'],
                'exam_type' => $questionSet['exam_type'],
                'semester' => $questionSet['semester'],
                'class_level' => $questionSet['class_level'],
            ];
        }

        return view('guru.questions.builder', compact('teacher', 'mode', 'questionSet', 'meta'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateQuestionRequest($request);

        $questionSets = collect($request->session()->get('teacher_questions', []));
        $nextId = ($questionSets->max('id') ?? 0) + 1;

        $questionSets->push([
            'id' => $nextId,
            'subject' => $validated['subject'],
            'exam_type' => $validated['exam_type'],
            'semester' => $validated['semester'],
            'class_level' => $validated['class_level'],
            'author' => session('teacher.name', 'Guru'),
            'questions' => $validated['questions'],
        ]);

        $request->session()->put('teacher_questions', $questionSets->values()->all());

        return redirect()
            ->route('teacher.questions.index')
            ->with('status', 'Set soal baru berhasil disimpan.');
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $validated = $this->validateQuestionRequest($request);

        $questionSets = collect($request->session()->get('teacher_questions', []));
        $index = $questionSets->search(fn ($set) => (int) $set['id'] === $id);

        if ($index === false) {
            return redirect()
                ->route('teacher.questions.index')
                ->withErrors(['questions' => 'Set soal tidak ditemukan atau telah dihapus.']);
        }

        $questionSets[$index] = array_merge($questionSets[$index], [
            'subject' => $validated['subject'],
            'exam_type' => $validated['exam_type'],
            'semester' => $validated['semester'],
            'class_level' => $validated['class_level'],
            'questions' => $validated['questions'],
        ]);

        $request->session()->put('teacher_questions', $questionSets->values()->all());

        return redirect()
            ->route('teacher.questions.index')
            ->with('status', 'Set soal berhasil diperbarui.');
    }

    public function destroy(Request $request, int $id): RedirectResponse
    {
        $questionSets = collect($request->session()->get('teacher_questions', []));
        $filtered = $questionSets->reject(fn ($set) => (int) $set['id'] === $id)->values();

        if ($filtered->count() === $questionSets->count()) {
            return redirect()
                ->route('teacher.questions.index')
                ->withErrors(['questions' => 'Set soal tidak ditemukan atau telah dihapus.']);
        }

        $request->session()->put('teacher_questions', $filtered->all());

        return redirect()
            ->route('teacher.questions.index')
            ->with('status', 'Set soal berhasil dihapus.');
    }

    private function validateQuestionRequest(Request $request): array
    {
        $data = $request->validate([
            'subject' => ['required', 'string', 'max:100'],
            'exam_type' => ['required', 'string', 'max:120'],
            'semester' => ['required', 'string', 'max:60'],
            'class_level' => ['required', 'string', 'max:60'],
            'questions' => ['required', 'array', 'min:1'],
            'questions.*.prompt' => ['required', 'string'],
            'questions.*.options' => ['required', 'array', 'min:2'],
            'questions.*.options.*' => ['required', 'string'],
            'questions.*.answer' => ['required', 'integer', 'min:0'],
        ]);

        $data['questions'] = collect($data['questions'])
            ->map(function (array $question) {
                return [
                    'prompt' => $question['prompt'],
                    'options' => array_values($question['options']),
                    'answer' => (int) $question['answer'],
                ];
            })
            ->values()
            ->all();

        return $data;
    }
}
