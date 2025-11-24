<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Data Siswa — Guru</title>
  <link rel="stylesheet" href="{{ asset('assets/css/admin.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/users.css') }}">
</head>
<body>

  @php
    $guru = session('guru', [
        'name' => 'Guru',
        'initials' => 'GR',
    ]);
  @endphp

  <div class="page">

    <header class="topbar">
      <div class="brand">
        <span class="brand-logo">
          <img src="{{ asset('assets/img/trustexam-illustration.svg') }}" alt="Logo"/>
        </span>
        TrustExam
      </div>

      <div class="user-menu">
        <span class="name">{{ $guru['name'] }}</span>
        <span class="role-tag">(Guru)</span>
        <span class="avatar-circle">{{ $guru['initials'] }}</span>
      </div>
    </header>

    <main class="content">
      <div class="users-container">

        <a class="back-link" href="{{ route('teacher.dashboard') }}">
          &#8592; Kembali ke Dashboard
        </a>

        <h2>Data Siswa</h2>

        <div class="table-wrapper">
          <table class="users-table">
            <thead>
              <tr>
                <th>No</th>
                <th>Nama Lengkap</th>
                <th>NIS</th>
                <th>Jenis Kelamin</th>
                <th>Kelas</th>
                <th>Tempat Lahir</th>
                <th>Tanggal Lahir</th>
                <th>Alamat</th>
              </tr>
            </thead>

            <tbody>
              @forelse ($students as $index => $siswa)
                <tr>
                  <td>{{ $index + 1 }}</td>
                  <td>{{ $siswa->nama_siswa }}</td>
                  <td>{{ $siswa->nis }}</td>
                  <td>{{ $siswa->jenis_kelamin }}</td>
                  <td>{{ $siswa->kelas }}</td>
                  <td>{{ $siswa->tempat_lahir }}</td>
                  <td>{{ $siswa->tanggal_lahir?->format('d M Y') }}</td>
                  <td>{{ $siswa->alamat }}</td>
                </tr>
              @empty
                <tr>
                  <td colspan="8" class="empty-state">Belum ada data siswa.</td>
                </tr>
              @endforelse
            </tbody>

          </table>
        </div>

      </div>
    </main>

  </div>

</body>
</html>
