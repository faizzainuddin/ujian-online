<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Siswa</title>
    <link rel="stylesheet" href="{{ asset('assets/css/admin.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/users.css') }}">
</head>
<body>

<div class="page">
    <header class="topbar">
        <div class="brand">
            <span class="brand-logo">
              <img src="{{ asset('assets/img/trustexam-illustration.svg') }}" alt="Logo" />
            </span>
            TrustExam
        </div>

        @php
          $teacher = session('teacher');
        @endphp

        <div class="user-menu">
            <span class="name">{{ $teacher['name'] }}</span>
            <span class="role-tag">(Guru)</span>
            <span class="avatar-circle">{{ $teacher['initials'] }}</span>
        </div>
    </header>

    <main class="content">
        <div class="users-container">
            <a class="back-link" href="{{ route('teacher.dashboard') }}">&#8592; Kembali ke Dashboard</a>

            <form action="{{ route('teacher.students.index') }}" method="get" class="filter-group" style="margin-bottom:20px;">
                <label for="kelasSelect">Kelas</label>

                <select id="kelasSelect" name="kelas" class="select" aria-label="Pilih kelas">
                    @foreach ($classList as $kelas)
                        <option value="{{ $kelas }}" {{ $selectedClass === $kelas ? 'selected' : '' }}>
                            {{ $kelas }}
                        </option>
                    @endforeach
                </select>

                <button class="btn btn-primary" type="submit">Tampilkan</button>
            </form>

            <div class="table-wrapper">
                <table class="users-table">
                    <thead>
                        <tr>
                            <th style="width: 15px; text-align:center;">No</th>
                            <th style="width: 90px; text-align:center;">NIS</th>
                            <th style="width: 160px; text-align:center;">Nama Siswa</th>
                            <th style="width: 120px; text-align:center;">Kelas</th>
                            <th>Username</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($students as $siswa)
                            <tr>
                                <td style="text-align:center;">{{ $loop->iteration }}</td>
                                <td style="text-align:center;">{{ $siswa->nis }}</td>
                                <td style="text-align:center;">{{ $siswa->nama_siswa }}</td>
                                <td style="text-align:center;">{{ $siswa->kelas }}</td>
                                <td>{{ $siswa->username }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="empty-state">Belum ada data siswa.</td>
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
