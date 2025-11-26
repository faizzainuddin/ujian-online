<!DOCTYPE html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ config('app.name', 'TrustExam') }} — Dashboard Siswa</title>
    <link rel="stylesheet" href="{{ asset('assets/css/student-dashboard.css') }}" />
  </head>
  <body>
    <div class="dashboard">
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
        <h1 class="welcome">Selamat datang, {{ $student['name'] }}</h1>
        <section class="quick-links">
          @foreach ($quickLinks as $link)
            <a class="quick-card" href="{{ $link['href'] }}">
              <span class="icon">
                <img src="{{ $link['icon'] }}" alt="" />
              </span>
              <h3>{{ $link['label'] }}</h3>
              <p>{{ $link['description'] }}</p>
            </a>
          @endforeach
        </section>

        <section class="announcement">
          <header>
            <span class="bell">🔔</span>
            <h2>Pengumuman</h2>
          </header>
          <article>
            <h3>📣 {{ $announcement['title'] }}</h3>
            <p>{{ $announcement['body'] }}</p>
            <h4>Ketentuan Ujian:</h4>
            <ul>
              @foreach ($announcement['guidelines'] as $guideline)
                <li>{{ $guideline }}</li>
              @endforeach
            </ul>
            <p class="footer">{{ $announcement['footer'] }}</p>
          </article>
        </section>
      </main>
    </div>
  </body>
</html>
