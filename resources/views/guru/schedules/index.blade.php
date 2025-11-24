<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Jadwal Ujian</title>
    <link rel="stylesheet" href="{{ asset('assets/css/admin.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/users.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/teacher-questions.css') }}" />
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
          <span class="role-tag">({{ $teacher['role'] }})</span>
          <span class="avatar-circle">{{ $teacher['initials'] }}</span>
          <form action="{{ route('logout') }}" method="post">
            @csrf
            <button type="submit" class="logout-button" title="Keluar">&#10162;</button>
          </form>
        </div>
    </header>

    <main class="content">
        <div class="users-container">
            <a class="back-link" href="{{ route('teacher.dashboard') }}">&#8592; Kembali ke Dashboard</a>

            <div class="page-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                <h1 class="page-title">Jadwal Ujian</h1>
                <a href="{{ route('teacher.schedules.create') }}" class="btn btn-primary" style="text-decoration: none; cursor: pointer;">+ Buat Jadwal Baru</a>
            </div>

            <div class="table-wrapper">
                <table class="users-table">
                    <thead>
                        <tr>
                            <th style="width: 15px; text-align:center;">No</th>
                            <th style="text-align:center;">Soal Ujian</th>
                            <th style="width: 160px; text-align:center;">Tanggal</th>
                            <th style="width: 140px; text-align:center;">Jam</th>
                            <th style="text-align:center;">Keterangan</th>
                            <th style="text-align:center;">Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($schedules as $jdw)
                            <tr>
                                <td style="text-align:center;">{{ $loop->iteration }}</td>

                                <td style="text-align:center;">
                                    {{ $jdw->questionSet->full_name ?? '' }}
                                </td>

                                <td style="text-align:center;">
                                    {{ \Carbon\Carbon::parse($jdw->date_start)->translatedFormat('d F Y') }}
                                </td>

                                <td style="text-align:center;">
                                    {{ $jdw->time_start }} - {{ $jdw->time_end }}
                                </td>

                                <td style="text-align:center;">
                                    Kelas {{ $jdw->class }}
                                </td>

                                <td style="text-align:center;">
                                    <form action="{{ route('teacher.schedules.destroy', $jdw->id) }}" 
                                    method="POST" 
                                    onsubmit="return confirm('Yakin mau hapus jadwal ini?')"
                                    style="display:inline;">
                                    @csrf
                                    @method('DELETE')

                                    <a href="#" 
                                    class="secondary-btn cancel"
                                    style="text-decoration: none !important;"
                                    onclick="event.preventDefault(); this.closest('form').submit();">
                                    Hapus
                                    </a>
                                </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="empty-state">Belum ada jadwal ujian.</td>
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
