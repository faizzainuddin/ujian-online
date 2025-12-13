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
                <button class="exam-button exam-button-active" onclick="showExamConfirmation('{{ $exam['subject'] }}', {{ $exam['id'] }})">Mulai Ujian</button>
              @else
                <button class="exam-button exam-button-disabled" disabled>Belum Bisa Dimulai</button>
              @endif
              
              <p class="exam-teacher">{{ $exam['teacher'] }}</p>
            </div>
          @endforeach
        </div>
      </main>
    </div>

    <!-- Pop-up Konfirmasi Ujian -->
    <div id="examModal" class="modal">
      <div class="modal-content">
        <h2 class="modal-title">Aturan Ujian</h2>
        <div class="modal-body">
          <ul class="rules-list">
            <li>Setiap soal memiliki batas waktu 30 detik.</li>
            <li>Selama ujian berlangsung, siswa dilarang melakukan kecurangan.</li>
            <li>Ujian akan otomatis berpindah ke soal berikutnya ketika waktu habis.</li>
            <li>Pastikan koneksi stabil sebelum memulai.</li>
          </ul>
          <div class="motivation">
            <strong>Selamat mengerjakan ujian!</strong><br>
            Tetap jujur dan percaya pada kemampuan sendiri.
          </div>
        </div>
        <div class="modal-footer">
          <button onclick="closeModal()" class="btn-cancel">Batal</button>
          <button onclick="startExam()" class="btn-start">Mulai Ujian</button>
        </div>
      </div>
    </div>

    <script>
      let selectedExamId = null;
      let selectedSubject = '';

      function showExamConfirmation(subject, examId) {
        selectedExamId = examId;
        selectedSubject = subject;
        document.getElementById('examModal').style.display = 'flex';
        document.body.style.overflow = 'hidden';
      }

      function closeModal() {
        document.getElementById('examModal').style.display = 'none';
        document.body.style.overflow = 'auto';
        selectedExamId = null;
        selectedSubject = '';
      }

      function startExam() {
        if (selectedExamId) {
          // Redirect ke halaman ujian dengan ID
          window.location.href = `/siswa/ujian/${selectedExamId}`;
        }
      }

      // Tutup modal jika klik di luar modal
      window.onclick = function(event) {
        const modal = document.getElementById('examModal');
        if (event.target === modal) {
          closeModal();
        }
      }
    </script>
  </body>
</html>
