<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use Illuminate\Http\Request;

class TeacherStudentController extends Controller
{
    public function index(Request $request)
    {
        $selectedClass = $request->query('kelas', 'all');

        $classList = Siswa::select('kelas')
            ->distinct()
            ->orderBy('kelas')
            ->pluck('kelas');

        $query = Siswa::orderBy('kelas')->orderBy('nama_siswa');

        if ($selectedClass !== 'all') {
            $query->where('kelas', $selectedClass);
        }

        $students = $query->get();

        return view('guru.siswa.index', [
            'students' => $students,
            'classList' => $classList,
            'selectedClass' => $selectedClass
        ]);
    }

}
