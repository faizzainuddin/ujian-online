<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'TrustExam') }} - Hasil Ujian</title>
    <style>
        /* Clean & Simple Exam Result Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #e8f4fc;
            min-height: 100vh;
        }

        /* Header Styles */
        .topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #cfe4ff;
            padding: 20px 36px;
            box-shadow: 0 4px 18px rgba(57, 110, 206, 0.1);
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 14px;
            font-weight: 700;
            font-size: 22px;
            color: #2f5fd7;
        }

        .brand-logo {
            width: 52px;
            height: 52px;
            border-radius: 50%;
            background: #ffffff;
            display: grid;
            place-items: center;
            box-shadow: 0 12px 24px rgba(47, 95, 215, 0.12);
        }

        .brand-logo img {
            width: 34px;
            height: 34px;
        }

        .student-info {
            display: flex;
            align-items: center;
            gap: 14px;
            color: #5c6883;
        }

        .student-info .name {
            font-weight: 600;
            color: #1f2a44;
        }

        .student-info .role-tag {
            color: #2f5fd7;
            font-weight: 500;
        }

        .avatar-circle {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #2f5fd7;
            color: #fff;
            display: grid;
            place-items: center;
            font-weight: 600;
        }

        /* Main Content */
        .result-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: calc(100vh - 100px);
            padding: 20px;
        }

        .result-card {
            background: white;
            border-radius: 16px;
            padding: 40px 48px;
            text-align: center;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            max-width: 420px;
            width: 100%;
        }

        .result-title {
            font-size: 22px;
            font-weight: 700;
            color: #2563a8;
            margin-bottom: 12px;
        }

        .result-subtitle {
            font-size: 14px;
            color: #6b7280;
            margin-bottom: 4px;
        }

        .result-message {
            font-size: 14px;
            color: #6b7280;
            margin-bottom: 28px;
        }

        .score-label {
            font-size: 14px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 8px;
        }

        .score-display {
            font-size: 42px;
            font-weight: 700;
            color: #1e3a5f;
            margin-bottom: 8px;
        }

        .status-text {
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 28px;
        }

        .status-text.lulus {
            color: #059669;
        }

        .status-text.tidak-lulus {
            color: #dc2626;
        }

        .btn-primary {
            display: block;
            width: 100%;
            padding: 14px 24px;
            background: #3b5998;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 15px;
            transition: background 0.2s;
        }

        .btn-primary:hover {
            background: #2d4373;
        }

        @media (max-width: 480px) {
            .topbar {
                padding: 16px 20px;
                flex-direction: column;
                gap: 12px;
            }
            
            .result-card {
                padding: 32px 24px;
            }
            
            .score-display {
                font-size: 36px;
            }
        }
    </style>
</head>
<body>
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

    {{-- Main Content --}}
    <div class="result-container">
        <div class="result-card">
            <h1 class="result-title">Ujian Telah Dikumpulkan</h1>
            <p class="result-subtitle">Jawaban kamu berhasil disimpan.</p>
            <p class="result-message">Terima kasih telah mengikuti ujian dengan jujur.</p>
            
            <div class="score-label">Skor Ujian</div>
            <div class="score-display">{{ $score }}/100</div>
            <div class="status-text {{ $score >= 75 ? 'lulus' : 'tidak-lulus' }}">
                Status: {{ $score >= 75 ? 'Lulus' : 'Tidak Lulus' }}
            </div>
            
            <a href="{{ route('student.dashboard') }}" class="btn-primary">
                Kembali ke Beranda
            </a>
        </div>
    </div>
</body>
</html>
