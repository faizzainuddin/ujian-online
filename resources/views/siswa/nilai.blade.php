<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'TrustExam') }} — Nilai Siswa</title>
    <link rel="stylesheet" href="{{ asset('assets/css/student-dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/hasil-ujian.css') }}">
</head>

<body>
    <div class="dashboard">

        <header class="topbar">
          <div>

          </div>
            <div class="profile">
                <div class="profile-meta">
                    <span class="profile-name">{{ $student['name'] }}</span>
                    <span class="profile-role">({{ $student['role'] }})</span>
                </div>
                <span class="avatar-circle">{{ $student['initials'] }}</span>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="logout-button" title="Keluar">
                        <span>Keluar</span>
                    </button>
                </form>
            </div>
        </header>
        
        <div class="dashboard-container"> 
            
            <div class="main-content-area">
                
                <a href="{{ route('student.dashboard') }}" class="back-link">
                    &larr; Kembali ke Dashboard
                </a>

                <h2 class="announcment">Nilai Ujian</h2>

                <div class="semester-selector-wrapper">
                    <label for="semester-select">Pilih Semester:</label>
                    <select id="semester-select" onchange="window.location.href = '{{ route('student.nilai') }}/' + this.value;">
                        @foreach ($availableSemesters as $sem)
                            <option value="{{ $sem }}" @if ($activeSemester == $sem) selected @endif>
                                 {{ $sem }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                @if (!empty($results))
                    @foreach ($results as $examType => $grades)
                    <div class="exam-result-section">
                        
                        <h3>{{ $activeSemester }} - {{ $examType }}</h3>

                        <div class="result-table-wrapper">
                            <table class="result-table">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Mata Pelajaran</th>
                                        <th>Nilai</th>
                                        <th>KKM</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($grades as $grade)
                                    <tr>
                                        <td>{{ $grade['no'] }}</td>
                                        <td>{{ $grade['subject'] }}</td>
                                        <td>{{ $grade['nilai'] }}</td>
                                        <td>{{ $grade['kkm'] }}</td>
                                        <td>
                                            <span class="status-badge status-{{ strtolower(str_replace(' ', '-', $grade['status'])) }}">
                                                {{ $grade['status'] }}
                                            </span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @if (!$loop->last)
                        <hr class="section-separator">
                    @endif
                    @endforeach
                @else
                    <p>Tidak ada data nilai ujian untuk Semester {{ $activeSemester }}.</p>
                @endif
                </div> 
        </div>
    </div>
</body>
</html>