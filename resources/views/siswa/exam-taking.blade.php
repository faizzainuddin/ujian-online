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
                <span class="avatar-circle">{{ $student['initials'] }}</span>
                <div class="student-details">
                    <span class="name">{{ $student['name'] }}</span>
                    <span class="role-tag">({{ $student['role'] }})</span>
                </div>
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
                                <label class="option {{ $selectedAnswer !== null && $selectedAnswer === $index ? 'selected' : '' }}">
                                    <input 
                                        type="radio" 
                                        name="answer" 
                                        value="{{ $index }}"
                                        {{ $selectedAnswer !== null && $selectedAnswer === $index ? 'checked' : '' }}
                                    >
                                    <span class="option-text">{{ $option }}</span>
                                </label>
                            @endforeach
                        </div>

                        {{-- Navigasi --}}
                        <div class="question-actions">
                            @if ($currentQuestion < $totalQuestions)
                                <button type="button" class="btn btn-primary" id="nextBtn">
                                    Selanjutnya ➡
                                </button>
                            @else
                                <button type="button" class="btn btn-success" id="finishBtn">
                                    Selesai ✓
                                </button>
                            @endif
                        </div>
                    </div>
                </form>
            </main>
        </div>
    </div>

    {{-- Modal Konfirmasi --}}
    <div id="finishModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Konfirmasi Selesai Ujian</h3>
                <p class="modal-subtitle">Apakah Anda yakin ingin mengakhiri ujian?</p>
            </div>
            
            <div class="modal-actions">
                <button type="button" class="btn-modal btn-secondary" id="cancelFinish">
                    Batal
                </button>
                <button type="button" class="btn-modal btn-danger" id="confirmFinish">
                    Ya, Selesai Ujian
                </button>
            </div>
        </div>
    </div>

    <script>
        // SOLID Principle Implementation for Exam Taking System
        
        // Single Responsibility: Timer Management
        class ExamTimer {
            constructor(timerElement, initialTime) {
                this.timerElement = timerElement;
                this.totalSeconds = this.parseTime(initialTime);
                this.isRunning = false;
                this.interval = null;
                this.onTimeUp = null;
            }

            parseTime(timeString) {
                const [minutes, seconds] = timeString.split(':').map(Number);
                return minutes * 60 + seconds;
            }

            formatTime(seconds) {
                const m = Math.floor(seconds / 60).toString().padStart(2, '0');
                const s = (seconds % 60).toString().padStart(2, '0');
                return `${m}:${s}`;
            }

            start() {
                if (this.isRunning) return;
                
                this.isRunning = true;
                this.interval = setInterval(() => {
                    if (this.totalSeconds <= 0) {
                        this.stop();
                        if (this.onTimeUp) this.onTimeUp();
                        return;
                    }
                    this.totalSeconds--;
                    this.updateDisplay();
                }, 1000);
            }

            stop() {
                this.isRunning = false;
                if (this.interval) {
                    clearInterval(this.interval);
                    this.interval = null;
                }
            }

            getRemainingTime() {
                return this.formatTime(this.totalSeconds);
            }

            updateDisplay() {
                this.timerElement.textContent = this.getRemainingTime();
            }
        }

        // Single Responsibility: Modal Management
        class ConfirmationModal {
            constructor(modalId) {
                this.modal = document.getElementById(modalId);
                console.log('Modal element:', this.modal);
                this.onConfirm = null;
                this.onCancel = null;
                this.setupEventListeners();
            }

            setupEventListeners() {
                const cancelBtn = this.modal?.querySelector('#cancelFinish');
                const confirmBtn = this.modal?.querySelector('#confirmFinish');

                console.log('Cancel button:', cancelBtn);
                console.log('Confirm button:', confirmBtn);

                cancelBtn?.addEventListener('click', () => {
                    console.log('Cancel clicked');
                    this.hide();
                    if (this.onCancel) this.onCancel();
                });

                confirmBtn?.addEventListener('click', () => {
                    console.log('Confirm clicked');
                    this.hide();
                    if (this.onConfirm) this.onConfirm();
                });

                // Close modal when clicking outside modal content
                this.modal?.addEventListener('click', (e) => {
                    if (e.target === this.modal) {
                        console.log('Modal overlay clicked - closing');
                        this.hide();
                        if (this.onCancel) this.onCancel();
                    }
                });

                // Close modal with ESC key
                document.addEventListener('keydown', (e) => {
                    if (e.key === 'Escape' && this.modal?.classList.contains('show')) {
                        console.log('ESC pressed - closing modal');
                        this.hide();
                        if (this.onCancel) this.onCancel();
                    }
                });
            }

            show(remainingTime) {
                console.log('Modal show called with time:', remainingTime);
                const timeDisplay = this.modal?.querySelector('#modalTimeRemaining');
                if (timeDisplay) {
                    timeDisplay.textContent = remainingTime;
                }
                if (this.modal) {
                    this.modal.classList.add('show');
                    console.log('Modal classes after show:', this.modal.className);
                } else {
                    console.error('Modal element not found!');
                }
            }

            hide() {
                console.log('Modal hide called');
                if (this.modal) {
                    this.modal.classList.remove('show');
                }
            }
        }

        // Single Responsibility: Exam Navigation
        class ExamNavigator {
            constructor(form, isLastQuestion) {
                this.form = form;
                this.isLastQuestion = isLastQuestion;
                this.actionInput = document.getElementById('actionInput');
            }

            submitNext() {
                this.actionInput.value = 'next';
                this.form.submit();
            }

            submitFinish() {
                this.actionInput.value = 'finish';
                this.form.submit();
            }
        }

        // Single Responsibility: Answer Selection
        class AnswerSelector {
            constructor() {
                this.setupEventListeners();
            }

            setupEventListeners() {
                document.querySelectorAll('.option').forEach(option => {
                    option.addEventListener('click', () => {
                        this.selectOption(option);
                    });
                });
            }

            selectOption(selectedOption) {
                document.querySelectorAll('.option').forEach(opt => {
                    opt.classList.remove('selected');
                });
                selectedOption.classList.add('selected');
                selectedOption.querySelector('input').checked = true;
            }
        }

        // Main Controller - Dependency Inversion Principle
        class ExamController {
            constructor() {
                this.form = document.getElementById('examForm');
                this.isLastQuestion = this.form.dataset.isLast === 'true';
                
                // Initialize components
                const timerEl = document.getElementById('timer');
                this.timer = new ExamTimer(timerEl, timerEl.textContent);
                this.modal = new ConfirmationModal('finishModal');
                this.navigator = new ExamNavigator(this.form, this.isLastQuestion);
                this.answerSelector = new AnswerSelector();
                
                this.setupEventHandlers();
                this.timer.start();
            }

            setupEventHandlers() {
                // Timer event - when time runs out
                this.timer.onTimeUp = () => {
                    if (this.isLastQuestion) {
                        // If it's the last question and time is up, directly finish without popup
                        this.navigator.submitFinish();
                    } else {
                        // If not last question, proceed to next
                        this.navigator.submitNext();
                    }
                };

                // Modal events
                this.modal.onConfirm = () => {
                    this.timer.stop();
                    this.navigator.submitFinish();
                };

                this.modal.onCancel = () => {
                    // Continue with exam - timer keeps running
                };

                // Button events
                const nextBtn = document.getElementById('nextBtn');
                const finishBtn = document.getElementById('finishBtn');

                nextBtn?.addEventListener('click', () => {
                    this.timer.stop();
                    this.navigator.submitNext();
                });

                finishBtn?.addEventListener('click', () => {
                    console.log('Finish button clicked');
                    console.log('Is last question:', this.isLastQuestion);
                    
                    const remainingTime = this.timer.getRemainingTime();
                    const [minutes, seconds] = remainingTime.split(':').map(Number);
                    const totalSeconds = minutes * 60 + seconds;
                    
                    console.log('Remaining time:', remainingTime);
                    console.log('Total seconds:', totalSeconds);
                    
                    // Show confirmation popup if:
                    // 1. This is the last question 
                    // 2. There's any time left (more than 10 seconds to be safe)
                    if (this.isLastQuestion && totalSeconds > 10) {
                        console.log('Showing modal confirmation');
                        this.modal.show(remainingTime);
                    } else {
                        console.log('Direct submit - no modal');
                        // Either not last question or time is very low - directly submit
                        this.timer.stop();
                        if (this.isLastQuestion) {
                            this.navigator.submitFinish();
                        } else {
                            this.navigator.submitNext();
                        }
                    }
                });
            }
        }

        // Initialize when DOM is ready
        document.addEventListener('DOMContentLoaded', () => {
            new ExamController();
        });
    </script>
</body>
</html>
