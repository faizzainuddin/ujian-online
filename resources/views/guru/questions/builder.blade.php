<!DOCTYPE html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ config('app.name', 'TrustExam') }} — {{ $mode === 'edit' ? 'Ubah' : 'Buat' }} Soal</title>
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
        <a href="{{ route('teacher.questions.index') }}" class="back-link">&#8592; Kembali ke Manajemen Soal</a>
        <h1 class="page-title">{{ $mode === 'edit' ? 'Ubah Soal' : 'Buat Soal' }}</h1>
        <div class="meta-bar">
          <span class="meta-badge">📘 {{ $meta['subject'] }}</span>
          <span class="meta-badge">🗓 {{ $meta['exam_type'] }}</span>
          <span class="meta-badge">🎓 {{ $meta['class_level'] }}</span>
          <span class="meta-badge">📚 {{ $meta['semester'] }}</span>
        </div>

        @if ($errors->any())
          <div class="alert-error" role="alert">
            <ul>
              @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        <form
          class="form-card"
          action="{{ $mode === 'edit' ? route('teacher.questions.update', $questionSet['id']) : route('teacher.questions.store') }}"
          method="post"
        >
          @csrf
          @if ($mode === 'edit')
            @method('PUT')
          @endif
          <input type="hidden" name="subject" value="{{ $meta['subject'] }}" />
          <input type="hidden" name="exam_type" value="{{ $meta['exam_type'] }}" />
          <input type="hidden" name="semester" value="{{ $meta['semester'] }}" />
          <input type="hidden" name="class_level" value="{{ $meta['class_level'] }}" />
          @php
            $questions = $questionSet['questions'] ?? [
                [
                    'prompt' => '',
                    'options' => ['', '', '', '', ''],
                    'answer' => 0,
                ],
            ];
          @endphp

          <div id="question-container">
            @foreach ($questions as $index => $question)
              <section class="question-block" data-question-index="{{ $loop->index }}">
                <h4>Soal {{ $loop->iteration }}</h4>
                <div class="form-group">
                  <label>Pertanyaan</label>
                  <textarea name="questions[{{ $loop->index }}][prompt]" placeholder="Masukkan pertanyaan">{{ $question['prompt'] }}</textarea>
                </div>
                <div class="form-group">
                  <label>Pilihan Jawaban (Pilih jawaban yang benar)</label>
                  <div class="answer-options">
                    @foreach ($question['options'] as $optionIndex => $option)
                      <label class="answer-option">
                        <input
                          type="radio"
                          name="questions[{{ $loop->parent->index }}][answer]"
                          value="{{ $optionIndex }}"
                          {{ $question['answer'] === $optionIndex ? 'checked' : '' }}
                        />
                        <input
                          type="text"
                          name="questions[{{ $loop->parent->index }}][options][]"
                          placeholder="Pilihan {{ chr(65 + $optionIndex) }}"
                          value="{{ $option }}"
                        />
                      </label>
                    @endforeach
                  </div>
                </div>
              </section>
            @endforeach
          </div>

          <div class="form-actions">
            <button type="button" class="secondary-btn add" onclick="addQuestion()">+ Tambah Soal</button>
            <button type="submit" class="secondary-btn save">Simpan Soal</button>
          </div>
        </form>
      </main>
    </div>

    <template id="question-template">
      <section class="question-block">
        <h4>Soal</h4>
        <div class="form-group">
          <label>Pertanyaan</label>
          <textarea placeholder="Masukkan pertanyaan"></textarea>
        </div>
        <div class="form-group">
          <label>Pilihan Jawaban (Pilih jawaban yang benar)</label>
          <div class="answer-options">
            @foreach (range(0, 4) as $optionIndex)
              <label class="answer-option">
                <input type="radio" />
                <input type="text" placeholder="Pilihan {{ chr(65 + $optionIndex) }}" />
              </label>
            @endforeach
          </div>
        </div>
      </section>
    </template>

    <script>
      function addQuestion() {
        const container = document.getElementById("question-container");
        const template = document.getElementById("question-template");
        const clone = template.content.cloneNode(true);
        const currentIndex = container.children.length;

        clone.querySelector("section").setAttribute("data-question-index", currentIndex);
        clone.querySelector("h4").textContent = `Soal ${currentIndex + 1}`;

        const textarea = clone.querySelector("textarea");
        textarea.name = `questions[${currentIndex}][prompt]`;
        textarea.value = "";

        clone.querySelectorAll("input[type='text']").forEach((input, optionIndex) => {
          input.name = `questions[${currentIndex}][options][]`;
          input.placeholder = `Pilihan ${String.fromCharCode(65 + optionIndex)}`;
          input.value = "";
        });

        clone.querySelectorAll("input[type='radio']").forEach((radio, optionIndex) => {
          radio.name = `questions[${currentIndex}][answer]`;
          radio.value = optionIndex;
          radio.checked = optionIndex === 0;
        });

        container.appendChild(clone);
      }
    </script>
  </body>
</html>
