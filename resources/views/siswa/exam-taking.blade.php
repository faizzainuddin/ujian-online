<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'TrustExam') }} - Ujian</title>
    <link rel="stylesheet" href="{{ asset('assets/css/exam-taking.css') }}">
</head>
<body>
    <div class="exam-page">
        {{-- Header --}}
        <header class="topbar">
            <div class="brand">
                <span class="brand-logo">
                    <img src="{{ asset('assets/img/trustexam-illustration.svg') }}" alt="Logo TrustExam">
                </span>
                TrustExam
            </div>
            <div class="student-info">
                <span class="name">{{ $student['name'] }}</span>
                <span class="role-tag">({{ $student['role'] }})</span>
                <span class="avatar-circle">{{ $student['initials'] }}</span>
            </div>
        </header>

        <div class="exam-container">
            {{-- Sidebar Navigasi Soal --}}
            <aside class="question-nav">
                @for ($i = 1; $i <= $totalQuestions; $i++)
                    <button 
                        class="nav-btn {{ $i === $currentQuestion ? 'active' : '' }}" 
                        data-question="{{ $i }}"
                    >
                        {{ $i }}
                    </button>
                @endfor
            </aside>

            {{-- Konten Utama --}}
            <main class="exam-content">
                {{-- Timer --}}
                <div class="timer-box">
                    <span class="timer" id="timer">{{ $timeRemaining }}</span>
                    <span class="timer-label">Sisa Waktu</span>
                </div>

                {{-- Soal --}}
                <div class="question-card">
                    <h2 class="question-number">Soal {{ $currentQuestion }}</h2>
                    <p class="question-text">{{ $question['text'] }}</p>

                    <div class="options">
                        @foreach ($question['options'] as $key => $option)
                            <label class="option {{ $selectedAnswer === $key ? 'selected' : '' }}">
                                <input 
                                    type="radio" 
                                    name="answer" 
                                    value="{{ $key }}"
                                    {{ $selectedAnswer === $key ? 'checked' : '' }}
                                >
                                <span class="option-text">{{ $option }}</span>
                            </label>
                        @endforeach
                    </div>

                    {{-- Navigasi --}}
                    <div class="question-actions">
                        @if ($currentQuestion > 1)
                            <button class="btn btn-secondary" id="prevBtn">
                                ⬅ Sebelumnya
                            </button>
                        @endif

                        @if ($currentQuestion < $totalQuestions)
                            <button class="btn btn-primary" id="nextBtn">
                                Selanjutnya ➡
                            </button>
                        @else
                            <button class="btn btn-success" id="submitBtn">
                                Selesai ✓
                            </button>
                        @endif
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script>
        // Timer countdown
        document.addEventListener('DOMContentLoaded', function() {
            const timerEl = document.getElementById('timer');
            let [hours, minutes, seconds] = timerEl.textContent.split(':').map(Number);
            let totalSeconds = hours * 3600 + minutes * 60 + seconds;

            const countdown = setInterval(() => {
                if (totalSeconds <= 0) {
                    clearInterval(countdown);
                    alert('Waktu habis!');
                    return;
                }
                totalSeconds--;
                const h = Math.floor(totalSeconds / 3600).toString().padStart(2, '0');
                const m = Math.floor((totalSeconds % 3600) / 60).toString().padStart(2, '0');
                const s = (totalSeconds % 60).toString().padStart(2, '0');
                timerEl.textContent = `${h}:${m}:${s}`;
            }, 1000);

            // Pilihan jawaban
            document.querySelectorAll('.option').forEach(opt => {
                opt.addEventListener('click', function() {
                    document.querySelectorAll('.option').forEach(o => o.classList.remove('selected'));
                    this.classList.add('selected');
                    this.querySelector('input').checked = true;
                });
            });
        });
    </script>
</body>
</html>
