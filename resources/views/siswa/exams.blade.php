<!DOCTYPE html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ config('app.name', 'TrustExam') }} — Ujian</title>
    <link rel="stylesheet" href="{{ asset('assets/css/student-exams.css') }}" />
  </head>
  <body>
    <div class="exams-page">
      <header class="topbar">
        <div class="brand">
          <span class="brand-logo">
            <img src="{{ asset('assets/img/trustexam-illustration.svg') }}" alt="Logo TrustExam" />
          </span>
          TrustExam
        </div>
        <div class="student-info">
          <span class="name">{{ $student['name'] }}</span>
          <span class="role-tag">({{ $student['role'] }})</span>
          <span class="avatar-circle">{{ $student['initials'] }}</span>
          <form action="{{ route('logout') }}" method="post">
            @csrf
            <button type="submit" class="logout-button">&#10162;</button>
          </form>
        </div>
      </header>

      <main class="content">
        <div class="back-link">
          <a href="{{ route('student.dashboard') }}">← Kembali ke Dashboard</a>
        </div>

        <h2 class="page-title">Ujian</h2>

        <div class="exam-cards-grid">
          @foreach ($exams as $exam)
            <div class="exam-card">
              <h3 class="exam-subject">{{ $exam['subject'] }}</h3>
              <p class="exam-date">{{ $exam['day'] }}, {{ $exam['date'] }}</p>
              <p class="exam-time">{{ $exam['time'] }}</p>
              
              @if ($exam['canStart'])
                <button class="exam-button exam-button-active">Mulai Ujian</button>
              @else
                <button class="exam-button exam-button-disabled" disabled>Belum Bisa Dimulai</button>
              @endif
              
              <p class="exam-teacher">{{ $exam['teacher'] }}</p>
            </div>
          @endforeach
        </div>
      </main>
    </div>
  </body>
</html>
