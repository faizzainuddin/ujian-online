<!DOCTYPE html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ config('app.name', 'TrustExam') }} &mdash; Login</title>
    <link rel="stylesheet" href="{{ asset('assets/css/login.css') }}" />
  </head>
  <body>
    <div class="page">
      <section class="brand-panel">
        <div class="brand-content">
          <p>Selamat datang di</p>
          <h1>Trust<span>Exam</span></h1>
          <img
            class="brand-illustration"
            src="{{ asset('assets/img/trustexam-illustration.svg') }}"
            alt="Ilustrasi topi wisuda dan ijazah"
            loading="lazy"
          />
        </div>
      </section>
      <section class="form-panel">
        <div class="form-wrapper">
          <h2>Silahkan Login</h2>
          @if (session('status'))
            <div class="alert alert-status">
              {{ session('status') }}
            </div>
          @endif
          @if ($errors->any())
            <div class="alert alert-error" role="alert">
              <ul>
                @foreach ($errors->all() as $error)
                  <li>{{ $error }}</li>
                @endforeach
              </ul>
            </div>
          @endif
          <form action="{{ route('login.submit') }}" method="post">
            @csrf
            <div class="input-group">
              <label for="username">Username ID</label>
              <input
                type="text"
                id="username"
                name="username"
                class="input-field"
                placeholder="Username id"
                value="{{ old('username') }}"
                autocomplete="username"
                required
              />
            </div>
            <div class="input-group">
              <label for="password">Password</label>
              <input
                type="password"
                id="password"
                name="password"
                class="input-field"
                placeholder="Password"
                autocomplete="current-password"
                required
              />
              <button type="button" class="toggle-password" aria-label="Tampilkan atau sembunyikan password">
                &#128065;
              </button>
            </div>
            <div class="captcha-display" aria-hidden="true">vm9fe</div>
            <div class="input-group">
              <label for="captcha">Kode Keamanan</label>
              <input
                type="text"
                id="captcha"
                name="captcha"
                class="input-field"
                placeholder="Masukkan Kode Diatas"
                inputmode="text"
                autocomplete="off"
                required
              />
            </div>
            <button type="submit" class="btn-submit">Login</button>
            <button type="button" class="btn-submit btn-outline">Lupa Password?</button>
            <p class="helper-text">Hubungi Admin jika mengalami kendala login.</p>
          </form>
        </div>
      </section>
    </div>
    <script>
      const togglePasswordBtn = document.querySelector(".toggle-password");
      const passwordField = document.getElementById("password");

      togglePasswordBtn?.addEventListener("click", () => {
        const isPassword = passwordField.type === "password";
        passwordField.type = isPassword ? "text" : "password";
        togglePasswordBtn.setAttribute(
          "aria-pressed",
          isPassword ? "true" : "false"
        );
      });
    </script>
  </body>
</html>
