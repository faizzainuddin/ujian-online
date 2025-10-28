<!DOCTYPE html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ config('app.name', 'TrustExam') }} — Manajemen Soal</title>
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
        <a href="{{ route('teacher.dashboard') }}" class="back-link">&#8592; Kembali ke Dashboard</a>

        <div class="header-actions">
          <h1 class="page-title">Manajemen Soal</h1>
          <a href="{{ route('teacher.questions.create') }}" class="primary-btn">+ Buat Soal</a>
        </div>

        @if (session('status'))
          <div class="alert-success">{{ session('status') }}</div>
        @endif

        @if ($errors->any())
          <div class="alert-error">
            <ul>
              @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        @if ($questionSets->isEmpty())
          <div class="empty-state-card">
            <img src="{{ asset('assets/img/icon-question.svg') }}" alt="Kosong" />
            <p>Belum ada set soal. Buat soal baru untuk mulai menambahkan.</p>
          </div>
        @else
          <div class="card-grid">
            @foreach ($questionSets as $set)
              <article class="question-card">
                <h3>{{ $set['subject'] }}</h3>
                <ul>
                  <li>{{ $set['exam_type'] }}</li>
                  <li>{{ $set['semester'] }}</li>
                  <li>{{ $set['class_level'] }}</li>
                  <li>{{ $set['author'] ?? $teacher['name'] }}</li>
                </ul>
                <div class="card-actions">
                  <a class="btn btn-edit" href="{{ route('teacher.questions.edit', ['id' => $set['id']]) }}">Ubah</a>
                  <form action="{{ route('teacher.questions.destroy', $set['id']) }}" method="post" onsubmit="return confirm('Yakin ingin menghapus set soal ini?');">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-delete" type="submit">Hapus</button>
                  </form>
                </div>
              </article>
            @endforeach
          </div>
        @endif
      </main>
    </div>
  </body>
</html>
