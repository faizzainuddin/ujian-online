<!DOCTYPE html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ config('app.name', 'TrustExam') }} — Kelola Akun Pengguna</title>
    <link rel="stylesheet" href="{{ asset('assets/css/admin.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/users.css') }}" />
  </head>
  <body>
    @php
      $admin = session('admin', [
          'name' => 'Admin System',
          'role' => 'Admin',
          'initials' => 'AS',
      ]);
    @endphp

    <div class="page">
      <header class="topbar">
        <div class="brand">
          <span class="brand-logo">
            <img src="{{ asset('assets/img/trustexam-illustration.svg') }}" alt="Logo TrustExam" />
          </span>
          TrustExam
        </div>
        <div class="user-menu">
          <span class="name">{{ $admin['name'] }}</span>
          <span class="role-tag">({{ $admin['role'] }})</span>
          <span class="avatar-circle">{{ $admin['initials'] }}</span>
          <form action="{{ route('logout') }}" method="post">
            @csrf
            <button type="submit" class="logout-button" title="Keluar dari sesi admin">&#10162;</button>
          </form>
        </div>
      </header>

      <main class="content">
        <div class="users-container">
          <a class="back-link" href="{{ route('admin.dashboard') }}">&#8592; Kembali ke Dashboard</a>

          @if (session('status'))
            <div class="alert-success" role="status">{{ session('status') }}</div>
          @endif

          <div class="toolbar">
            <form class="filter-group" action="{{ route('admin.users.index') }}" method="get">
              <label for="roleSelect">Peran Pengguna</label>
              <select id="roleSelect" name="role" class="select" aria-label="Pilih peran">
                @foreach (['Siswa', 'Guru', 'Admin'] as $roleOption)
                  <option value="{{ $roleOption }}" {{ $activeRole === $roleOption ? 'selected' : '' }}>{{ $roleOption }}</option>
                @endforeach
              </select>
              <button class="btn btn-primary" type="submit">Tampilkan</button>
            </form>
            <a class="btn btn-success" href="{{ route('admin.users.create', ['role' => $activeRole]) }}">+ Tambah</a>
          </div>

          <div class="table-wrapper">
            <table class="users-table">
              <thead>
                <tr>
                  @foreach ($tableHeaders as $header)
                    <th>{{ $header }}</th>
                  @endforeach
                </tr>
              </thead>
              <tbody>
                @forelse ($tableRows as $row)
                  <tr>
                    <td>{{ $row['no'] }}</td>
                    <td>{{ $row['name'] }}</td>
                    <td>{{ $row['username'] }}</td>
                    <td>{{ $row['password'] }}</td>
                    <td>{{ $row['role'] }}</td>
                    <td>
                      <span class="status {{ $row['status_class'] ?? 'aktif' }}">{{ $row['status'] ?? '-' }}</span>
                    </td>
                    <td>
                      <div class="actions">
                        <button class="icon-btn icon-edit" title="Ubah" type="button" disabled>
                          <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04a1.003 1.003 0 0 0 0-1.42l-2.34-2.34a1.003 1.003 0 0 0-1.42 0l-1.83 1.83 3.75 3.75 1.84-1.82z"/></svg>
                        </button>
                        <button class="icon-btn icon-delete" title="Hapus" type="button" disabled>
                          <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6 7h12v2H6zm2 3h8l-1 10H9L8 10zm3-6h2l1 1h4v2H4V5h4l1-1z"/></svg>
                        </button>
                      </div>
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="{{ count($tableHeaders) }}" class="empty-state">Belum ada data {{ strtolower($activeRole) }}.</td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </main>
      <button type="button" class="floating-button">N</button>
    </div>
  </body>
  </html>
