<!DOCTYPE html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ config('app.name', 'TrustExam') }} - Hasil Ujian</title>
    <link rel="stylesheet" href="{{ asset('assets/css/admin.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/users.css') }}">
  </head>

  <body>
    @php
      $teacher = session('teacher', [
          'name' => 'Ibu Siti Nurhaliza',
          'role' => 'Teacher',
          'initials' => 'IS',
      ]);

      if (! isset($semesterList) || count($semesterList) === 0) {
          $semesterList = \App\Models\QuestionSet::select('semester')->distinct()->pluck('semester');
      }
    @endphp

    <div class="page">
      <header class="topbar">
        <div class="brand">
          <span class="brand-logo">
            <img src="{{ asset('assets/img/trustexam-illustration.svg') }}" alt="Logo" />
          </span>
          TrustExam
        </div>

        <div class="user-menu">
          <span class="name">{{ $teacher['name'] }}</span>
          <span class="role-tag">(Guru)</span>
          <span class="avatar-circle">{{ $teacher['initials'] }}</span>
        </div>
      </header>

      <main class="content">
        <a href="{{ route('teacher.dashboard') }}" class="back-link">&#8592; Kembali ke Dashboard</a>
        <h2 style="margin-bottom: 20px;">Hasil Ujian Siswa</h2>

        <form method="GET" action="{{ route('teacher.results') }}" style="display: flex; gap: 200px; align-items: flex-end; margin-bottom: 20px;">
          <div class="container-filter-hasilujian-teacher">
            <div class="position">
              <div>
                <label for="mata_pelajaran">Mata Pelajaran:</label>
                <select name="mata_pelajaran" id="mata_pelajaran" class="ddown">
                  <option value="">-- Semua --</option>
                  @foreach($mataPelajaranList as $mp)
                    <option value="{{ $mp }}" {{ request('mata_pelajaran') == $mp ? 'selected' : '' }}>{{ $mp }}</option>
                  @endforeach
                </select>
              </div>

              <div>
                <label for="semester">Semester:</label>
                <select name="semester" id="semester" class="ddown">
                  <option value="">-- Semua --</option>
                  @foreach($semesterList as $s)
                    <option value="{{ $s }}" {{ request('semester') == $s ? 'selected' : '' }}>{{ $s }}</option>
                  @endforeach
                </select>
              </div>

              <div>
                <label for="kelas">Kelas:</label>
                <select name="kelas" id="kelas" class="ddown">
                  <option value="">-- Semua --</option>
                  @foreach($kelasList as $k)
                    <option value="{{ $k }}" {{ request('kelas') == $k ? 'selected' : '' }}>{{ $k }}</option>
                  @endforeach
                </select>
              </div>
            </div>

            <div>
              <button class="btn btn-primary" type="submit">Tampilkan</button>
            </div>
          </div>
        </form>

        <div class="table-wrapper">
          <table class="users-table">
            <thead>
              <tr style="background: #f3f3f3;">
                <th>No</th>
                <th>Nama Siswa</th>
                <th>Kelas</th>
                <th>Nilai UAS</th>
                <th>Keterangan</th>
              </tr>
            </thead>

            <tbody>
              @forelse ($results as $i => $r)
                <tr>
                  <td>{{ $i + 1 }}</td>
                  <td>{{ $r->nama_siswa }}</td>
                  <td>{{ $r->kelas ?? '-' }}</td>
                  <td>{{ $r->nilai }}</td>
                  <td>
                    @if ($r->status == 'belum')
                      <span style="color: gray;">Belum Mengerjakan</span>
                    @elseif ($r->nilai >= 75)
                      <span style="color: green;">Lulus</span>
                    @elseif ($r->nilai >= 1 && $r->nilai < 75)
                      <span style="color: orange;">Remedial</span>
                    @elseif ($r->nilai == 0)
                      <span style="color: red;">Tidak Lulus</span>
                    @else
                      <span>-</span>
                    @endif
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="5" style="text-align:center;">Belum ada hasil ujian.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </main>
    </div>
  </body>
</html>
