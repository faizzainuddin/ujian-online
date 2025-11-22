<!DOCTYPE html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ config('app.name', 'TrustExam') }} — Dashboard Guru</title>
    <link rel="stylesheet" href="{{ asset('assets/css/teacher.css') }}" />
  </head>
  <body>
    @php
      $teacher = session('teacher', [
          'name' => 'Ibu Siti Nurhaliza',
          'role' => 'Teacher',
          'initials' => 'IS',
      ]);

      $features = [
          [
              'title' => 'Manajemen Soal',
              'icon' => asset('assets/img/icon-question.svg'),
              'url' => route('teacher.questions.index'),
          ],
          [
              'title' => 'Jadwalkan Ujian',
              'icon' => asset('assets/img/icon-calendar.svg'),
              'url' => '#',
          ],
          [
              'title' => 'Hasil Ujian',
              'icon' => asset('assets/img/icon-result.svg'),
              'url' => '#',
          ],
          [
              'title' => 'Data Siswa',
              'icon' => asset('assets/img/icon-student.svg'),
              'url' => route('teacher.students.index'),
          ],
      ];
    @endphp

    <div class="page">
      <header class="topbar">
        <div class="brand">
          <span class="brand-logo">
            <img src="{{ asset('assets/img/trustexam-illustration.svg') }}" alt="Logo TrustExam" />
          </span>
          TrustExam
        </div>
        <div class="teacher-info">
          <span class="name">{{ $teacher['name'] }}</span>
          <span class="role-tag">({{ $teacher['role'] }})</span>
          <span class="avatar-circle">{{ $teacher['initials'] }}</span>
          <form action="{{ route('logout') }}" method="post">
            @csrf
            <button type="submit" class="logout-button" title="Keluar dari sesi guru">&#10162;</button>
          </form>
        </div>
      </header>

      <main class="content">
        <h1 class="welcome">Selamat datang, {{ $teacher['name'] }}</h1>

        <div class="grid">
          <div class="panel-card">
            @foreach ($features as $feature)
              <a href="{{ $feature['url'] }}" class="feature-card">
                <span class="icon">
                  <img src="{{ $feature['icon'] }}" alt="{{ $feature['title'] }}" />
                </span>
                <p class="title">{{ $feature['title'] }}</p>
              </a>
            @endforeach
          </div>

          <section class="announcement-card">
            <div class="header">
              <span>📢</span>
              Pengumuman
            </div>
            <div class="body">
              <h3>Pengumuman Ujian Akhir Semester (UAS)</h3>
              <p>
                Kepada Yth. Bapak/Ibu Guru,<br />
                Diberitahukan bahwa pelaksanaan Ujian Akhir Semester (UAS) untuk kelas X, XI, dan XII akan dilaksanakan secara
                online melalui platform TrustExam pada tanggal 24 Juni 2025 s.d. Jumat, 28 Juni 2025.
              </p>
              <p>Hal-hal yang perlu diperhatikan:</p>
              <ul>
                <li>Mohon memastikan seluruh soal ujian telah diunggah paling lambat 23 Juni 2025 pukul 17.00 WIB.</li>
                <li>Setiap ujian akan berjalan sesuai jadwal dan sistem akan otomatis menutup sesi ujian.</li>
                <li>Sistem akan otomatis melaporkan hasil ujian.</li>
              </ul>
              <p>Atas perhatian Anda, kami ucapkan terima kasih.</p>
            </div>
          </section>
        </div>
      </main>

      <button type="button" class="floating-button">N</button>
    </div>
  </body>
</html>
