<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'TrustExam') }} - Hasil Ujian</title>
    <link rel="stylesheet" href="{{ asset('assets/css/exam-taking.css') }}">
    <style>
        .result-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: calc(100vh - 100px);
            padding: 2rem;
        }
        .result-card {
            background: white;
            border-radius: 16px;
            padding: 3rem;
            text-align: center;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            max-width: 500px;
            width: 100%;
        }
        .result-icon {
            font-size: 4rem;
            margin-bottom: 1rem;
        }
        .result-title {
            font-size: 1.5rem;
            color: #2d3748;
            margin-bottom: 0.5rem;
        }
        .result-subject {
            color: #718096;
            margin-bottom: 2rem;
        }
        .score-circle {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            background: linear-gradient(135deg, #4a6fa5, #3d5a80);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            margin: 0 auto 2rem;
            color: white;
        }
        .score-value {
            font-size: 3rem;
            font-weight: 700;
        }
        .score-label {
            font-size: 0.9rem;
            opacity: 0.9;
        }
        .result-details {
            color: #4a5568;
            margin-bottom: 2rem;
        }
        .btn-back {
            display: inline-block;
            padding: 0.8rem 2rem;
            background: #4a6fa5;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            transition: background 0.2s;
        }
        .btn-back:hover {
            background: #3d5a80;
        }
    </style>
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

        <div class="result-container">
            <div class="result-card">
                <div class="result-icon">🎉</div>
                <h1 class="result-title">Ujian Selesai!</h1>
                <p class="result-subject">{{ $examTitle }}</p>
                
                <div class="score-circle">
                    <span class="score-value">{{ $score }}</span>
                    <span class="score-label">Nilai</span>
                </div>
                
                <p class="result-details">
                    Jawaban benar: {{ $correctAnswers }} dari {{ $totalQuestions }} soal
                </p>
                
                <a href="{{ route('student.exams') }}" class="btn-back">
                    Kembali ke Daftar Ujian
                </a>
            </div>
        </div>
    </div>
</body>
</html>
