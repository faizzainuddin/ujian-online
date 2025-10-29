<!DOCTYPE html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ config('app.name', 'TrustExam') }} — Ubah Data {{ $formRole }}</title>
    <link rel="stylesheet" href="{{ asset('assets/css/admin.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/users.css') }}" />
  </head>
  <body>
    @php
      $admin = session('admin', [
          'name' => 'Admin System',
          'role' => 'Admin',
          'initials' => 'AS',
      ]);

      $redirectTarget = $redirectTarget ?? 'index';
      $routeParams = ['role' => $formRole, 'id' => $entity->getKey()];
      $redirectParam = ['redirect' => $redirectTarget];
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
            <button type="submit" class="logout-button" title="Keluar dari sesi admin">&#10162;</button>
          </form>
        </div>
      </header>

      <main class="content">
        <div class="users-container">
          @php
            $backRoute = $redirectTarget === 'data' ? route('admin.users.data', ['role' => $formRole]) : route('admin.users.index', ['role' => $formRole]);
          @endphp
          <a class="back-link" href="{{ $backRoute }}">&#8592; Kembali</a>

          <div class="form-card">
            <h2 class="form-title">Ubah Data {{ $formRole }}</h2>

            <form action="{{ route('admin.users.update', $routeParams) }}" method="post" class="form-grid">
              @csrf
              @method('PUT')
              <input type="hidden" name="redirect" value="{{ $redirectTarget }}" />

              @if ($formRole === 'Guru')
                <div class="form-group">
                  <label for="nama_guru">Nama Lengkap <sup>*</sup></label>
                  <input type="text" id="nama_guru" name="nama_guru" value="{{ old('nama_guru', $entity->nama_guru) }}" placeholder="Nama lengkap" required />
                  @error('nama_guru')
                    <p class="form-error">{{ $message }}</p>
                  @enderror
                </div>

                <div class="form-group">
                  <label for="username">Username <sup>*</sup></label>
                  <input type="text" id="username" name="username" value="{{ old('username', $entity->username) }}" placeholder="Username" required />
                  @error('username')
                    <p class="form-error">{{ $message }}</p>
                  @enderror
                </div>

                <div class="form-group">
                  <label for="password">Password</label>
                  <input type="password" id="password" name="password" placeholder="Kosongkan jika tidak diubah" />
                  @error('password')
                    <p class="form-error">{{ $message }}</p>
                  @enderror
                </div>

                <div class="form-group form-group-full">
                  <label for="matapelajaran">Mata Pelajaran <sup>*</sup></label>
                  <input type="text" id="matapelajaran" name="matapelajaran" value="{{ old('matapelajaran', $entity->matapelajaran) }}" placeholder="Contoh: Matematika" required />
                  @error('matapelajaran')
                    <p class="form-error">{{ $message }}</p>
                  @enderror
                </div>
              @elseif ($formRole === 'Admin')
                <div class="form-group">
                  <label for="nama_admin">Nama Lengkap <sup>*</sup></label>
                  <input type="text" id="nama_admin" name="nama_admin" value="{{ old('nama_admin', $entity->nama_admin) }}" placeholder="Nama lengkap" required />
                  @error('nama_admin')
                    <p class="form-error">{{ $message }}</p>
                  @enderror
                </div>

                <div class="form-group">
                  <label for="username">Username <sup>*</sup></label>
                  <input type="text" id="username" name="username" value="{{ old('username', $entity->username) }}" placeholder="Username" required />
                  @error('username')
                    <p class="form-error">{{ $message }}</p>
                  @enderror
                </div>

                <div class="form-group">
                  <label for="password">Password</label>
                  <input type="password" id="password" name="password" placeholder="Kosongkan jika tidak diubah" />
                  @error('password')
                    <p class="form-error">{{ $message }}</p>
                  @enderror
                </div>
              @else
                <div class="form-group">
                  <label for="nama_siswa">Nama Lengkap <sup>*</sup></label>
                  <input type="text" id="nama_siswa" name="nama_siswa" value="{{ old('nama_siswa', $entity->nama_siswa) }}" placeholder="Nama lengkap" required />
                  @error('nama_siswa')
                    <p class="form-error">{{ $message }}</p>
                  @enderror
                </div>

                <div class="form-group">
                  <label for="nis">NIS <sup>*</sup></label>
                  <input type="text" id="nis" name="nis" value="{{ old('nis', $entity->nis) }}" placeholder="NIS" required />
                  @error('nis')
                    <p class="form-error">{{ $message }}</p>
                  @enderror
                </div>

                <div class="form-group">
                  <label for="username">Username <sup>*</sup></label>
                  <input type="text" id="username" name="username" value="{{ old('username', $entity->username) }}" placeholder="Username" required />
                  @error('username')
                    <p class="form-error">{{ $message }}</p>
                  @enderror
                </div>

                <div class="form-group">
                  <label for="password">Password</label>
                  <input type="password" id="password" name="password" placeholder="Kosongkan jika tidak diubah" />
                  @error('password')
                    <p class="form-error">{{ $message }}</p>
                  @enderror
                </div>

                <div class="form-group">
                  <label for="jenis_kelamin">Jenis Kelamin <sup>*</sup></label>
                  <select id="jenis_kelamin" name="jenis_kelamin" required>
                    <option value="">Pilih</option>
                    @foreach ($genders as $gender)
                      <option value="{{ $gender }}" {{ old('jenis_kelamin', $entity->jenis_kelamin) === $gender ? 'selected' : '' }}>{{ $gender }}</option>
                    @endforeach
                  </select>
                  @error('jenis_kelamin')
                    <p class="form-error">{{ $message }}</p>
                  @enderror
                </div>

                <div class="form-group">
                  <label for="kelas">Kelas <sup>*</sup></label>
                  <select id="kelas" name="kelas" required>
                    <option value="">Pilih Kelas</option>
                    @foreach ($kelasList as $kelas)
                      <option value="{{ $kelas }}" {{ old('kelas', $entity->kelas) === $kelas ? 'selected' : '' }}>{{ $kelas }}</option>
                    @endforeach
                  </select>
                  @error('kelas')
                    <p class="form-error">{{ $message }}</p>
                  @enderror
                </div>

                <div class="form-group">
                  <label for="tempat_lahir">Tempat Lahir <sup>*</sup></label>
                  <input type="text" id="tempat_lahir" name="tempat_lahir" value="{{ old('tempat_lahir', $entity->tempat_lahir) }}" placeholder="Tempat lahir" required />
                  @error('tempat_lahir')
                    <p class="form-error">{{ $message }}</p>
                  @enderror
                </div>

                <div class="form-group">
                  <label for="tanggal_lahir">Tanggal Lahir <sup>*</sup></label>
                  <input type="date" id="tanggal_lahir" name="tanggal_lahir" value="{{ old('tanggal_lahir', $entity->tanggal_lahir?->format('Y-m-d')) }}" required />
                  @error('tanggal_lahir')
                    <p class="form-error">{{ $message }}</p>
                  @enderror
                </div>

                <div class="form-group">
                  <label for="status">Status <sup>*</sup></label>
                  <select id="status" name="status" required>
                    @foreach ($statuses as $status)
                      <option value="{{ $status }}" {{ old('status', $entity->status) === $status ? 'selected' : '' }}>{{ $status }}</option>
                    @endforeach
                  </select>
                  @error('status')
                    <p class="form-error">{{ $message }}</p>
                  @enderror
                </div>

                <div class="form-group form-group-full">
                  <label for="alamat">Alamat <sup>*</sup></label>
                  <input type="text" id="alamat" name="alamat" value="{{ old('alamat', $entity->alamat) }}" placeholder="Masukkan alamat lengkap" required />
                  @error('alamat')
                    <p class="form-error">{{ $message }}</p>
                  @enderror
                </div>
              @endif

              <div class="form-actions">
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                <a href="{{ $backRoute }}" class="btn btn-secondary">Batal</a>
              </div>
            </form>
          </div>
        </div>
      </main>

      <button type="button" class="floating-button">N</button>
    </div>
  </body>
</html>
