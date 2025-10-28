<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class TeacherQuestionController extends Controller
{
    public function index(): View
    {
        $teacher = session('teacher', [
            'name' => 'Ibu Siti Nurhaliza',
            'role' => 'Teacher',
            'initials' => 'IS',
        ]);

        $questionSets = collect(range(1, 6))->map(function (int $i) {
            return [
                'id' => $i,
                'title' => 'Bahasa Indonesia',
                'exam_type' => $i % 2 === 0 ? 'UAS Semester '.ceil($i / 2) : 'UTS Semester '.ceil($i / 2),
                'class' => 'Kelas 12',
                'teacher' => 'Rini Wulandari, S.Pd',
            ];
        });

        return view('guru.questions.index', compact('teacher', 'questionSets'));
    }

    public function create(): View
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

        $meta = [
            'subject' => $request->query('subject', 'Bahasa Indonesia'),
            'exam_type' => $request->query('exam_type', 'Ujian Tengah Semester'),
            'semester' => $request->query('semester', 'Semester 1'),
            'class_level' => $request->query('class_level', 'Kelas 12'),
        ];

        if ($mode === 'edit') {
            $questionSet = [
                'title' => 'Bahasa Indonesia',
                'exam_type' => 'UAS Semester 1',
                'class_level' => 'Kelas 12',
                'questions' => [
                    [
                        'prompt' => 'Apa judul teks yang sedang dibahas?',
                        'options' => ['Pilihan A', 'Pilihan B', 'Pilihan C', 'Pilihan D', 'Pilihan E'],
                        'answer' => 1,
                    ],
                ],
            ];
            $meta = [
                'subject' => $questionSet['title'],
                'exam_type' => $questionSet['exam_type'],
                'semester' => 'Semester 1',
                'class_level' => $questionSet['class_level'],
            ];
        }

        return view('guru.questions.builder', compact('teacher', 'mode', 'questionSet', 'meta'));
    }
}
