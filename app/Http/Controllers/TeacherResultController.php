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

        // Tentukan nama tabel ujian
        $ujianTable = Schema::hasTable('ujian') ? 'ujian' : (Schema::hasTable('ujian_sekolah') ? 'ujian_sekolah' : null);

        // Ambil data dropdown
        $mataPelajaranList = DB::table('guru')->distinct()->pluck('matapelajaran');
        $jenisUjianList = $ujianTable ? DB::table($ujianTable)->distinct()->pluck('nama_ujian') : collect();
        $kelasList = DB::table('siswa')->distinct()->pluck('kelas');
        $semesterList = $ujianTable && Schema::hasColumn($ujianTable, 'semester')
            ? DB::table($ujianTable)->distinct()->pluck('semester')
            : collect();

        // Query utama
        $query = DB::table('hasil_ujian')
            ->join('siswa', 'hasil_ujian.siswa_id', '=', 'siswa.siswa_id')
            ->leftJoin('guru', 'hasil_ujian.guru_id', '=', 'guru.guru_id');

        if ($ujianTable) {
            $query->join($ujianTable, 'hasil_ujian.ujian_id', '=', $ujianTable . '.ujian_id');
        }

        // Filter dari request
        if ($request->filled('mata_pelajaran')) {
            $query->where('guru.matapelajaran', $request->mata_pelajaran);
        }
        if ($request->filled('jenis_ujian') && $ujianTable) {
            $query->where($ujianTable . '.nama_ujian', $request->jenis_ujian);
        }
        if ($request->filled('semester') && $ujianTable) {
            $query->where($ujianTable . '.semester', $request->semester);
        }
        if ($request->filled('kelas')) {
            $query->where('siswa.kelas', $request->kelas);
        }

        // Ambil hasil
        $results = $query->select(
            'hasil_ujian.*',
            'siswa.nama_siswa',
            'siswa.kelas',
            'guru.matapelajaran'
        )->get();

        // Return view
        return view('guru.hasil_ujian.hasilujian', compact(
            'results', 'teacherForView', 'mataPelajaranList', 'jenisUjianList', 'kelasList', 'semesterList'
        ));
    }
}
