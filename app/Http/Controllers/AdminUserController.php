<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Guru;
use App\Models\Siswa;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminUserController extends Controller
{
    private const ROLES = ['Siswa', 'Guru', 'Admin'];

    public function index(Request $request): View
    {
        $role = $this->resolveRole($request->query('role', 'Siswa'));

        $table = $this->buildTableData($role);

        return view('admin.users.index', [
            'activeRole' => $role,
            'tableHeaders' => $table['headers'],
            'tableRows' => $table['rows'],
        ]);
    }

    public function data(Request $request): View
    {
        $role = $this->resolveRole($request->query('role', 'Siswa'));

        $table = $this->buildDetailTable($role);

        return view('admin.users.data', [
            'activeRole' => $role,
            'tableHeaders' => $table['headers'],
            'tableRows' => $table['rows'],
        ]);
    }

    public function create(Request $request): View
    {
        $role = $this->resolveRole($request->query('role', 'Siswa'));

        return view('admin.users.create', [
            'formRole' => $role,
            'statuses' => ['Aktif', 'Nonaktif'],
            'genders' => ['Laki-laki', 'Perempuan'],
            'kelasList' => ['X IPA 1', 'X IPA 2', 'XI IPS 1', 'XII IPA 1'],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $admin = $request->session()->get('admin');

        if (! $admin || ! isset($admin['id'])) {
            abort(403, 'Admin tidak terautentikasi.');
        }

        $roleType = $this->resolveRole($request->input('role_type', 'Siswa'));

        switch ($roleType) {
            case 'Guru':
                $validated = $request->validate([
                    'nama_guru' => ['required', 'string', 'max:100'],
                    'username' => ['required', 'string', 'max:50', 'unique:guru,username'],
                    'password' => ['required', 'string', 'min:6'],
                    'matapelajaran' => ['required', 'string', 'max:100'],
                ]);

                Guru::create([
                    'username' => $validated['username'],
                    'password' => $validated['password'],
                    'nama_guru' => $validated['nama_guru'],
                    'matapelajaran' => $validated['matapelajaran'],
                    'admin_id' => $admin['id'],
                ]);

                $message = 'Data guru berhasil ditambahkan.';
                break;
            case 'Admin':
                $validated = $request->validate([
                    'nama_admin' => ['required', 'string', 'max:100'],
                    'username' => ['required', 'string', 'max:50', 'unique:admin,username'],
                    'password' => ['required', 'string', 'min:6'],
                ]);

                Admin::create([
                    'username' => $validated['username'],
                    'password' => $validated['password'],
                    'nama_admin' => $validated['nama_admin'],
                ]);

                $message = 'Data admin berhasil ditambahkan.';
                break;
            case 'Siswa':
            default:
                $validated = $request->validate([
                    'nama_siswa' => ['required', 'string', 'max:100'],
                    'nis' => ['required', 'string', 'max:20', 'unique:siswa,nis'],
                    'username' => ['required', 'string', 'max:50', 'unique:siswa,username'],
                    'password' => ['required', 'string', 'min:6'],
                    'jenis_kelamin' => ['required', 'in:Laki-laki,Perempuan'],
                    'kelas' => ['required', 'string', 'max:50'],
                    'tempat_lahir' => ['required', 'string', 'max:100'],
                    'tanggal_lahir' => ['required', 'date'],
                    'status' => ['required', 'in:Aktif,Nonaktif'],
                    'alamat' => ['required', 'string', 'max:255'],
                ]);

                Siswa::create([
                    'nis' => $validated['nis'],
                    'username' => $validated['username'],
                    'password' => $validated['password'],
                    'password_hint' => $validated['password'],
                    'nama_siswa' => $validated['nama_siswa'],
                    'jenis_kelamin' => $validated['jenis_kelamin'],
                    'kelas' => $validated['kelas'],
                    'tempat_lahir' => $validated['tempat_lahir'],
                    'tanggal_lahir' => $validated['tanggal_lahir'],
                    'status' => $validated['status'],
                    'alamat' => $validated['alamat'],
                    'role' => 'Siswa',
                    'admin_id' => $admin['id'],
                ]);

                $message = 'Data siswa berhasil ditambahkan.';
                break;
        }

        return redirect()
            ->route('admin.users.index', ['role' => $roleType])
            ->with('status', $message);
    }

    private function resolveRole(string $input): string
    {
        return in_array($input, self::ROLES, true) ? $input : 'Siswa';
    }

    private function buildTableData(string $role): array
    {
        $headers = ['No', 'Nama Pengguna', 'Username', 'Password', 'Role', 'Status', 'Aksi'];

        $rows = match ($role) {
            'Guru' => $this->mapGuruRows(),
            'Admin' => $this->mapAdminRows(),
            default => $this->mapSiswaRows(),
        };

        return compact('headers', 'rows');
    }

    private function mapSiswaRows()
    {
        return Siswa::query()
            ->orderBy('nama_siswa')
            ->get()
            ->values()
            ->map(function (Siswa $siswa, int $index) {
                $status = $siswa->status ?? 'Aktif';

                return [
                    'no' => $index + 1,
                    'name' => $siswa->nama_siswa,
                    'username' => $siswa->username,
                    'password' => $siswa->password_hint ?? '••••••',
                    'role' => 'Siswa',
                    'status' => $status,
                    'status_class' => strtolower($status) === 'aktif' ? 'aktif' : 'nonaktif',
                ];
            });
    }

    private function mapGuruRows()
    {
        return Guru::query()
            ->orderBy('nama_guru')
            ->get()
            ->values()
            ->map(function (Guru $guru, int $index) {
                return [
                    'no' => $index + 1,
                    'name' => $guru->nama_guru,
                    'username' => $guru->username,
                    'password' => '••••••',
                    'role' => 'Guru',
                    'status' => 'Aktif',
                    'status_class' => 'aktif',
                ];
            });
    }

    private function mapAdminRows()
    {
        return Admin::query()
            ->orderBy('nama_admin')
            ->get()
            ->values()
            ->map(function (Admin $admin, int $index) {
                return [
                    'no' => $index + 1,
                    'name' => $admin->nama_admin,
                    'username' => $admin->username,
                    'password' => '••••••',
                    'role' => 'Admin',
                    'status' => 'Aktif',
                    'status_class' => 'aktif',
                ];
            });
    }

    private function buildDetailTable(string $role): array
    {
        return match ($role) {
            'Guru' => [
                'headers' => ['No', 'Nama Guru', 'Username', 'Mata Pelajaran', 'Aksi'],
                'rows' => Guru::query()
                    ->orderBy('nama_guru')
                    ->get()
                    ->values()
                    ->map(function (Guru $guru, int $index) {
                        return [
                            'data' => [
                                $index + 1,
                                $guru->nama_guru,
                                $guru->username,
                                $guru->matapelajaran,
                            ],
                            'actions' => true,
                        ];
                    }),
            ],
            'Admin' => [
                'headers' => ['No', 'Nama Admin', 'Username', 'Role', 'Aksi'],
                'rows' => Admin::query()
                    ->orderBy('nama_admin')
                    ->get()
                    ->values()
                    ->map(function (Admin $admin, int $index) {
                        return [
                            'data' => [
                                $index + 1,
                                $admin->nama_admin,
                                $admin->username,
                                'Admin',
                            ],
                            'actions' => true,
                        ];
                    }),
            ],
            default => [
                'headers' => ['No', 'Nama Lengkap', 'NIS', 'Jenis Kelamin', 'Kelas', 'Tempat Lahir', 'Tanggal Lahir', 'Alamat', 'Aksi'],
                'rows' => Siswa::query()
                    ->orderBy('nama_siswa')
                    ->get()
                    ->values()
                    ->map(function (Siswa $siswa, int $index) {
                        $lahir = $siswa->tanggal_lahir ? $siswa->tanggal_lahir->format('M d, Y') : '-';

                        return [
                            'data' => [
                                $index + 1,
                                $siswa->nama_siswa,
                                $siswa->nis ?? '-',
                                $siswa->jenis_kelamin ?? '-',
                                $siswa->kelas ?? '-',
                                $siswa->tempat_lahir ?? '-',
                                $lahir,
                                $siswa->alamat ?? '-',
                            ],
                            'actions' => true,
                        ];
                    }),
            ],
        };
    }
}
