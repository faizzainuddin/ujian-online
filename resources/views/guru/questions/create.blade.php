<!DOCTYPE html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ config('app.name', 'TrustExam') }} — Buat Soal</title>
    <link rel="stylesheet" href="{{ asset('assets/css/teacher-questions.css') }}" />
  </head>
  <body>
    <div class="page">
      <header class="topbar">
        <div class="brand">
          <span class="brand-logo">
            <img src="{{ asset('assets/img/trustexam-illustration.svg') }}" alt="Logo TrustExam" />
          </span>
          TrustExam
        </div>
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
        <a href="{{ route('teacher.questions.index') }}" class="back-link">&#8592; Kembali ke Manajemen Soal</a>
        <h1 class="page-title">Buat Soal</h1>

        <form class="form-card" action="{{ route('teacher.questions.builder') }}" method="get">
          <div class="form-group">
            <label for="subject">Pilih Mata Pelajaran</label>
            <select id="subject" name="subject" required>
              <option value="" disabled selected>Pilih Mata Pelajaran</option>
              @foreach ($subjects as $subject)
                <option value="{{ $subject }}">{{ $subject }}</option>
              @endforeach
            </select>
          </div>

          <div class="form-group">
            <label>Ujian</label>
            <div class="radio-group">
              <label><input type="radio" name="exam_type" value="UTS" required /> Ujian Tengah Semester</label>
              <label><input type="radio" name="exam_type" value="UAS" required /> Ujian Akhir Semester</label>
            </div>
          </div>

          <div class="form-group">
            <label for="semester">Semester</label>
            <select id="semester" name="semester" required>
              <option value="" disabled selected>Pilih Semester</option>
              @foreach ($semesters as $semester)
                <option value="{{ $semester }}">{{ $semester }}</option>
              @endforeach
            </select>
          </div>

          <div class="form-group">
            <label>Kelas</label>
            <div class="radio-group">
              <label><input type="radio" name="class_level" value="Kelas 10" required /> 10</label>
              <label><input type="radio" name="class_level" value="Kelas 11" required /> 11</label>
              <label><input type="radio" name="class_level" value="Kelas 12" required /> 12</label>
            </div>
          </div>

          <div class="form-actions">
            <button type="submit" class="secondary-btn add">Selanjutnya</button>
            <a href="{{ route('teacher.questions.index') }}" class="secondary-btn cancel" role="button">Batal</a>
          </div>
        </form>
      </main>
    </div>
  </body>
</html>
