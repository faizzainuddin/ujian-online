<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class TeacherResultController extends Controller
{
    public function index(Request $request)
    {
        // Ambil teacher dari session
        $teacher = session('teacher');
        $guruId = null;
        $teacherName = null;

        if (is_array($teacher)) {
            $guruId = $teacher['guru_id'] ?? $teacher['id'] ?? $teacher['teacher_id'] ?? null;
            $teacherName = $teacher['name'] ?? $teacher['nama_guru'] ?? null;
        } elseif (is_object($teacher)) {
            $guruId = $teacher->guru_id ?? $teacher->id ?? null;
            $teacherName = $teacher->name ?? $teacher->nama_guru ?? null;
        }

        if (!$guruId && Auth::check()) {
            $user = Auth::user();
            $guruId = $user->guru_id ?? $user->id ?? null;
            $teacherName = $teacherName ?? ($user->name ?? $user->nama_guru ?? null);
        }

        $teacherForView = ['name' => $teacherName ?? 'Guru'];

        // Tentukan nama tabel ujian dan question_sets jika ada
        $ujianTable = Schema::hasTable('ujian') ? 'ujian' : (Schema::hasTable('ujian_sekolah') ? 'ujian_sekolah' : null);
        $examScheduleTable = Schema::hasTable('exam_schedules') ? 'exam_schedules' : null;
        $questionSetTable = Schema::hasTable('question_sets') ? 'question_sets' : null;
        $canJoinQuestionSet = $ujianTable && $questionSetTable && Schema::hasColumn($ujianTable, 'question_set_id');
        $canJoinExamSchedule = $examScheduleTable && $questionSetTable && Schema::hasColumn($examScheduleTable, 'question_set_id');

        // Ambil data dropdown
        $mataPelajaranList = DB::table('guru')->distinct()->pluck('matapelajaran');
        $jenisUjianList = collect();
        if ($questionSetTable && Schema::hasColumn($questionSetTable, 'exam_type')) {
            $jenisUjianList = DB::table($questionSetTable)->distinct()->pluck('exam_type');
        } elseif ($ujianTable) {
            $jenisUjianList = DB::table($ujianTable)->distinct()->pluck('nama_ujian');
        }
        $kelasList = DB::table('siswa')->distinct()->pluck('kelas');

        $defaultMataPelajaran = collect([
            'biologi',
            'fisika',
        ]);

        $defaultKelas = collect([
            'X IPA 1',
            'X IPA 2',
            'X IPA 3',
            'X IPS 1',
            'X IPS 2',
            'X IPS 3',
            'XI IPA 1',
            'XI IPA 2',
            'XI IPA 3',
            'XI IPS 1',
            'XI IPS 2',
            'XI IPS 3',
            'XII IPA 1',
            'XII IPA 2',
            'XII IPA 3',
            'XII IPS 1',
            'XII IPS 2',
            'XII IPS 3',
        ]);

        $mataPelajaranList = $mataPelajaranList
            ->merge($defaultMataPelajaran)
            ->filter(fn($value) => is_string($value) && trim($value) !== '')
            ->map(fn($value) => trim($value))
            ->unique()
            ->values();

        $kelasList = $kelasList
            ->merge($defaultKelas)
            ->filter(fn($value) => is_string($value) && trim($value) !== '')
            ->map(fn($value) => trim($value))
            ->unique()
            ->values();
        if ($questionSetTable) {
            $semesterList = DB::table($questionSetTable)->distinct()->pluck('semester');
        } elseif ($ujianTable && Schema::hasColumn($ujianTable, 'semester')) {
            $semesterList = DB::table($ujianTable)->distinct()->pluck('semester');
        } else {
            $semesterList = collect();
        }

        // Query utama
        $query = DB::table('hasil_ujian')
            ->join('siswa', 'hasil_ujian.siswa_id', '=', 'siswa.siswa_id');

        $joinedGuru = false;
        $joinedQuestionSet = false;
        $joinedExamSchedule = false;

        if ($canJoinExamSchedule) {
            $query->join($examScheduleTable, 'hasil_ujian.ujian_id', '=', $examScheduleTable . '.id')
                  ->leftJoin($questionSetTable, $examScheduleTable . '.question_set_id', '=', $questionSetTable . '.id')
                  ->leftJoin('guru', $questionSetTable . '.teacher_id', '=', 'guru.guru_id');
            $joinedExamSchedule = true;
            $joinedQuestionSet = true;
            $joinedGuru = true;
        } elseif ($ujianTable) {
            $query->join($ujianTable, 'hasil_ujian.ujian_id', '=', $ujianTable . '.ujian_id')
                  ->leftJoin('guru', $ujianTable . '.guru_id', '=', 'guru.guru_id');
            $joinedGuru = true;

            if ($canJoinQuestionSet) {
                $query->leftJoin($questionSetTable, $ujianTable . '.question_set_id', '=', $questionSetTable . '.id');
                $joinedQuestionSet = true;
            }
        } elseif (Schema::hasColumn('hasil_ujian', 'guru_id')) {
            // Fallback jika kolom guru_id memang ada di tabel hasil_ujian
            $query->leftJoin('guru', 'hasil_ujian.guru_id', '=', 'guru.guru_id');
            $joinedGuru = true;
        }

        // Filter dari request
        if ($joinedGuru && $request->filled('mata_pelajaran')) {
            $query->where('guru.matapelajaran', $request->mata_pelajaran);
        }
        if ($request->filled('jenis_ujian')) {
            if ($joinedQuestionSet && Schema::hasColumn($questionSetTable, 'exam_type')) {
                $query->where($questionSetTable . '.exam_type', $request->jenis_ujian);
            } elseif ($ujianTable) {
                $query->where($ujianTable . '.nama_ujian', $request->jenis_ujian);
            }
        }
        if ($request->filled('semester')) {
            if ($joinedQuestionSet) {
                $query->where($questionSetTable . '.semester', $request->semester);
            } elseif ($ujianTable && Schema::hasColumn($ujianTable, 'semester')) {
                $query->where($ujianTable . '.semester', $request->semester);
            }
        }
        if ($request->filled('kelas')) {
            $query->where('siswa.kelas', $request->kelas);
        }

        // Ambil hasil
        $select = [
            'hasil_ujian.*',
            'siswa.nama_siswa',
            'siswa.kelas',
        ];

        if ($joinedGuru) {
            $select[] = 'guru.matapelajaran';
        }

        $results = $query->select($select)->get();

        // Return view
        return view('guru.hasil_ujian.hasilujian', compact(
            'results', 'teacherForView', 'mataPelajaranList', 'jenisUjianList', 'kelasList', 'semesterList'
        ));
    }
}
