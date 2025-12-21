<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'TrustExam') }} - {{ $examTitle ?? 'Ujian' }}</title>
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
                        type="button"
                        class="nav-btn indicator {{ $i === $currentQuestion ? 'active' : '' }} {{ session("exam_{$ujianId}_q{$i}") !== null ? 'answered' : '' }}"
                        disabled
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
                <form id="examForm" method="POST" action="{{ route('student.exam.answer', ['ujianId' => $ujianId]) }}" data-is-last="{{ $currentQuestion === $totalQuestions ? 'true' : 'false' }}">
                    @csrf
                    <input type="hidden" name="question_number" value="{{ $currentQuestion }}">
                    <input type="hidden" name="action" id="actionInput" value="next">
                    
                    <div class="question-card">
                        <h2 class="question-number">Soal {{ $currentQuestion }}</h2>
                        <p class="question-text">{!! nl2br(e($question['text'])) !!}</p>

                        <div class="options">
                            @foreach ($question['options'] as $index => $option)
                                <label class="option {{ $selectedAnswer == $index ? 'selected' : '' }}">
                                    <input 
                                        type="radio" 
                                        name="answer" 
                                        value="{{ $index }}"
                                        {{ $selectedAnswer == $index ? 'checked' : '' }}
                                    >
                                    <span class="option-text">{{ $option }}</span>
                                </label>
                            @endforeach
                        </div>

                        {{-- Navigasi --}}
                        <div class="question-actions">
                            @if ($currentQuestion < $totalQuestions)
                                <button type="submit" class="btn btn-primary" onclick="document.getElementById('actionInput').value='next';">
                                    Selanjutnya ➡
                                </button>
                            @else
                                <button type="submit" class="btn btn-success" onclick="document.getElementById('actionInput').value='finish';">
                                    Selesai ✓
                                </button>
                            @endif
                        </div>
                    </div>
                </form>
            </main>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Timer countdown per soal (30 detik default dari controller)
            const form = document.getElementById('examForm');
            const isLast = form.dataset.isLast === 'true';
            const timerEl = document.getElementById('timer');
            let [minutes, seconds] = timerEl.textContent.split(':').map(Number);
            let totalSeconds = minutes * 60 + seconds;

            const countdown = setInterval(() => {
                if (totalSeconds <= 0) {
                    clearInterval(countdown);
                    document.getElementById('actionInput').value = isLast ? 'finish' : 'next';
                    form.submit();
                    return;
                }
                totalSeconds--;
                const m = Math.floor(totalSeconds / 60).toString().padStart(2, '0');
                const s = (totalSeconds % 60).toString().padStart(2, '0');
                timerEl.textContent = `${m}:${s}`;
            }, 1000);

            // Pilihan jawaban - visual feedback
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
