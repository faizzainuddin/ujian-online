<!DOCTYPE html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ config('app.name', 'TrustExam') }} &mdash; Dashboard Admin</title>
    <link rel="stylesheet" href="{{ asset('assets/css/admin.css') }}" />
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
            <button type="submit" class="logout-button" title="Keluar dari sesi admin">
              &#10162;
            </button>
          </form>
        </div>
      </header>
      <main class="content">
        <h1 class="welcome">
          Selamat datang, <span>{{ $admin['name'] }}</span>
        </h1>
        <div class="card-grid">
          <a href="{{ route('admin.users.index') }}" class="card">
            <div class="card-icon">
              <svg viewBox="0 0 24 24" aria-hidden="true">
                <path d="M12 12a5 5 0 1 0-5-5 5.006 5.006 0 0 0 5 5Zm0-8a3 3 0 1 1-3 3 3.004 3.004 0 0 1 3-3Zm6.5 10H17a5.975 5.975 0 0 0-10 0H5.5A3.5 3.5 0 0 0 2 17.5v1A2.5 2.5 0 0 0 4.5 21h15A2.5 2.5 0 0 0 22 18.5v-1A3.5 3.5 0 0 0 18.5 14Zm1.5 4.5a.5.5 0 0 1-.5.5h-15a.5.5 0 0 1-.5-.5v-1A1.5 1.5 0 0 1 5.5 16h1.247A3.976 3.976 0 0 1 12 14a3.976 3.976 0 0 1 5.253 2H18.5a1.5 1.5 0 0 1 1.5 1.5Z" />
              </svg>
              <span class="badge">2</span>
            </div>
            <p class="card-title">Kelola Akun Pengguna</p>
          </a>
          <a href="{{ route('admin.users.data') }}" class="card">
            <div class="card-icon">
              <svg viewBox="0 0 24 24" aria-hidden="true">
                <path d="M6 3h9a5 5 0 0 1 5 5v2h-2V8a3 3 0 0 0-3-3H6a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h11v2H6a3 3 0 0 1-3-3V6a3 3 0 0 1 3-3Zm5 6h10v2H11Zm0 4h10v2H11Zm0 4h10v2H11Z" />
              </svg>
            </div>
            <p class="card-title">Kelola Data Pengguna</p>
          </a>
        </div>
      </main>
      <button type="button" class="floating-button">N</button>
    </div>
    <script>
      (function () {
        const retainCurrent = () => {
          history.replaceState(null, document.title, location.href);
          history.pushState(null, document.title, location.href);
        };

        window.addEventListener("popstate", () => {
          retainCurrent();
          history.go(1);
          window.location.reload();
        });

        retainCurrent();
      })();
    </script>
  </body>
</html>
