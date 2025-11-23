<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>{{ config('app.name', 'TrustExam') }} — Buat Jadwal</title>
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
        <a href="{{ route('teacher.schedules.index') }}" class="back-link">&#8592; Kembali ke Jadwal Ujian</a>
        <h1 class="page-title">Buat Jadwal Ujian</h1>

        <form class="form-card" action="{{ route('teacher.schedules.store') }}" method="post">
            @csrf
              <div class="form-group">
                <label for="question_set_id">Pilih Soal Ujian</label>
                <select id="question_set_id" name="question_set_id" required>
                    <option value="">-- Pilih Soal --</option>
                    @foreach($sets as $set)
                        <option 
                            value="{{ $set->id }}"
                            data-class="{{ $set->class_level }}"
                        >
                            {{ $set->full_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="class">Pilih Kelas</label>
                <select id="class" name="class" required>
                    <option value="">-- Pilih Kelas --</option>
                </select>
            </div>

            <div class="form-group">
                <label>Tanggal Mulai</label>
                <input type="date" name="date_start" required />
            </div>

            <div class="form-group">
                <label>Waktu Mulai</label>
                <input type="time" name="time_start" required />
            </div>

            <div class="form-group">
                <label>Waktu Selesai</label>
                <input type="time" name="time_end" required />
            </div>

            <div class="form-actions">
                <button type="submit" class="secondary-btn add">Simpan Jadwal</button>
                <a href="{{ route('teacher.schedules.index') }}" class="secondary-btn cancel" role="button" style="text-decoration: none; cursor: pointer;">Batal</a>
            </div>
        </form>
    </main>
  </div>
  <script>
      const classGroups = {
          "Kelas 10": ["X IPA 1", "X IPA 2", "X IPS 1", "X IPS 2"],
          "Kelas 11": ["XI IPA 1", "XI IPA 2", "XI IPS 1", "XI IPS 2"],
          "Kelas 12": ["XII IPA 1", "XII IPA 2", "XII IPS 1", "XII IPS 2"]
      };

      document.getElementById('question_set_id').addEventListener('change', function() {
          const selectedClass = this.selectedOptions[0].getAttribute('data-class');
          const classSelect = document.getElementById('class');

          classSelect.innerHTML = '<option value="">-- Pilih Kelas --</option>';

          if (classGroups[selectedClass]) {
              classGroups[selectedClass].forEach(kls => {
                  classSelect.innerHTML += `<option value="${kls}">${kls}</option>`;
              });
          }
      });
  </script>
</body>
</html>
