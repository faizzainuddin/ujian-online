<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\QuestionSet;
use App\Models\ExamSchedule;

class TeacherScheduleController extends Controller
{
    public function index()
    {
        $teacher = $this->resolveTeacher();
        if (! $teacher) {
            return redirect()->route('login')->withErrors(['auth' => 'Sesi guru tidak ditemukan.']);
        }

        $schedules = ExamSchedule::orderBy('date_start', 'asc')
                         ->orderBy('time_start', 'asc')
                         ->get();


        return view('guru.schedules.index', compact('schedules', 'teacher'));
    }

   public function create()
    {
        $teacher = $this->resolveTeacher();
        if (! $teacher) {
            return redirect()->route('login')->withErrors(['auth' => 'Sesi guru tidak ditemukan.']);
        }

        // Ambil semua question set milik guru
        $sets = QuestionSet::all();

        // list kelas otomatis, nanti difilter di blade
        $classGroups = [
            '10' => ['X IPA 1', 'X IPA 2', 'X IPS 1', 'X IPS 2'],
            '11' => ['XI IPA 1', 'XI IPA 2', 'XI IPS 1', 'XI IPS 2'],
            '12' => ['XII IPA 1', 'XII IPA 2', 'XII IPS 1', 'XII IPS 2'],
        ];

        return view('guru.schedules.create', compact('sets', 'classGroups', 'teacher'));
    }


    public function store(Request $request)
    {
        $teacher = $this->resolveTeacher();
        if (! $teacher) {
            return redirect()->route('login')->withErrors(['auth' => 'Sesi guru tidak ditemukan.']);
        }

        $request->validate([
            'question_set_id' => 'required|exists:question_sets,id',
            'class' => 'required|string',
            'date_start' => 'required|date',
            'time_start' => 'required',
            'date_end' => 'nullable|date',
            'time_end' => 'required',
        ]);

        ExamSchedule::create([
            'question_set_id' => $request->question_set_id,
            'class' => $request->class,
            'date_start' => $request->date_start,
            'time_start' => $request->time_start,
            'date_end' => $request->date_end,
            'time_end' => $request->time_end,
        ]);

        return redirect()->route('teacher.schedules.index')
                        ->with('success', 'Jadwal ujian berhasil dibuat.');
    }

    public function destroy($id)
    {
        $teacher = $this->resolveTeacher();
        if (! $teacher) {
            return redirect()->route('login')->withErrors(['auth' => 'Sesi guru tidak ditemukan.']);
        }

        $schedule = ExamSchedule::findOrFail($id);
        $schedule->delete();

        return redirect()->route('teacher.schedules.index')
                         ->with('success', 'Jadwal ujian berhasil dihapus.');
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
