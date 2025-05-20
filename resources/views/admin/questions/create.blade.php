<x-layout bodyClass="g-sidenav-show bg-gray-200">
    <x-navbars.sidebar activePage="questions" :userName="auth()->user()->name" :userRole="auth()->user()->role->role_name" />
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <x-navbars.navs.auth titlePage="Tambah Soal" />
        <div class="container-fluid py-4">
            <div class="row">
                <div class="col-12">
                    <div class="card my-4">
                    <br><br>

                        <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                            <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                                <h6 class="text-white text-capitalize ps-3">Tambah Soal Baru</h6>
                            </div>
                        </div>
                        <div class="card-body px-0 pb-2">
                            @if(isset($material))
                                <form method="POST" action="{{ route('admin.materials.questions.store', $material) }}" class="p-4">
                            @else
                                <form method="POST" action="{{ route('admin.questions.store') }}" class="p-4">
                            @endif
                                @csrf
                                
                                @if($errors->any())
                                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                                        @foreach($errors->all() as $error)
                                            {{ $error }}<br>
                                        @endforeach
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </div>
                                @endif

                                @if(session('warning'))
                                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                                        {{ session('warning') }}
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </div>
                                @endif

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label class="form-label">Material</label>
                                            <div class="input-group input-group-outline">
                                                @if(isset($material))
                                                    <input type="hidden" name="material_id" value="{{ $material->id }}">
                                                    <input type="text" class="form-control" value="{{ $material->title }}" disabled>
                                                @else
                                                    <select name="material_id" id="material_id" class="form-control" required>
                                                        <option value="">Pilih Material</option>
                                                        @foreach($materials as $material)
                                                            <option value="{{ $material->id }}">{{ $material->title }}</option>
                                                        @endforeach
                                                    </select>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label class="form-label">Pertanyaan</label>
                                            <div class="input-group input-group-outline">
                                                <textarea name="question_text" class="form-control" rows="3" required></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label class="form-label">Tipe Soal</label>
                                            <div class="input-group input-group-outline">
                                                <select name="question_type" class="form-control" required>
                                                    <option value="radio_button">Radio Button</option>
                                                    <option value="drag_and_drop">Drag and Drop</option>
                                                    <option value="fill_in_the_blank">Fill in the Blank</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div id="answers-container">
                                    <h6 class="mb-3">Jawaban</h6>
                                    <div class="answer-entry mb-3">
                                        <div class="row">
                                            <div class="col-md-8">
                                                <div class="input-group input-group-outline">
                                                    <input type="text" name="answers[0][answer_text]" class="form-control" placeholder="Jawaban" required>
                                                </div>
                                                <div class="input-group input-group-outline mt-2">
                                                    <textarea name="answers[0][explanation]" class="form-control" placeholder="Penjelasan Jawaban" rows="2"></textarea>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="correct_answer" value="0">
                                                    <label class="form-check-label">Jawaban Benar</label>
                                                    <input type="hidden" name="answers[0][is_correct]" value="0">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <button type="button" class="btn btn-outline-primary btn-sm mb-3" onclick="addAnswer()">
                                    Tambah Jawaban
                                </button>

                                <div class="row">
                                    <div class="col-12">
                                        <button type="submit" class="btn btn-primary">Simpan Soal</button>
                                        @if(isset($material))
                                            <a href="{{ route('admin.materials.questions.index', $material) }}" class="btn btn-outline-secondary">Batal</a>
                                        @else
                                            <a href="{{ route('admin.questions.index') }}" class="btn btn-outline-secondary">Batal</a>
                                        @endif
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    @push('js')
    <script>
        let answerCount = 1;

        function handleQuestionTypeChange() {
            const questionType = document.querySelector('[name="question_type"]').value;
            const container = document.getElementById('answers-container');
            const existingAnswers = container.getElementsByClassName('answer-entry');
            
            // Reset semua jawaban benar
            Array.from(existingAnswers).forEach((answerEntry, index) => {
                const formCheck = answerEntry.querySelector('.form-check');
                
                if (questionType === 'radio_button' || questionType === 'drag_and_drop') {
                    const isCorrect = formCheck.querySelector('input[name$="[is_correct]"]')?.value === '1';
                    formCheck.innerHTML = `
                        <input class="form-check-input" type="radio" name="correct_answer" value="${index}" ${isCorrect ? 'checked' : ''}>
                        <label class="form-check-label">Jawaban Benar</label>
                        <input type="hidden" name="answers[${index}][is_correct]" value="${isCorrect ? '1' : '0'}">
                    `;
                } else {
                    const isCorrect = formCheck.querySelector('input[name$="[is_correct]"]')?.value === '1';
                    formCheck.innerHTML = `
                        <input class="form-check-input" type="checkbox" name="answers[${index}][is_correct]" value="1" ${isCorrect ? 'checked' : ''}>
                        <label class="form-check-label">Jawaban Benar</label>
                    `;
                }
            });
        }

        function addAnswer() {
            const container = document.getElementById('answers-container');
            const answerCount = container.getElementsByClassName('answer-entry').length;
            
            const newAnswer = document.createElement('div');
            newAnswer.className = 'answer-entry mb-3';
            newAnswer.innerHTML = `
                <div class="row">
                    <div class="col-md-8">
                        <div class="input-group input-group-outline">
                            <input type="text" name="answers[${answerCount}][answer_text]" class="form-control" placeholder="Jawaban" required>
                        </div>
                        <div class="input-group input-group-outline mt-2">
                            <textarea name="answers[${answerCount}][explanation]" class="form-control" placeholder="Penjelasan Jawaban" rows="2"></textarea>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="correct_answer" value="${answerCount}">
                            <label class="form-check-label">Jawaban Benar</label>
                            <input type="hidden" name="answers[${answerCount}][is_correct]" value="0">
                        </div>
                    </div>
                </div>
            `;
            
            container.appendChild(newAnswer);
        }

        document.addEventListener('DOMContentLoaded', function() {
            const questionTypeSelect = document.querySelector('[name="question_type"]');
            const form = document.querySelector('form');
            
            // Event listener untuk perubahan tipe soal
            questionTypeSelect.addEventListener('change', handleQuestionTypeChange);
            
            // Event listener untuk perubahan jawaban benar
            document.addEventListener('change', function(e) {
                if (e.target.type === 'radio' && e.target.name === 'correct_answer') {
                    const container = document.getElementById('answers-container');
                    const answers = container.getElementsByClassName('answer-entry');
                    
                    Array.from(answers).forEach((answer, index) => {
                        const hiddenInput = answer.querySelector('input[name$="[is_correct]"]');
                        if (hiddenInput) {
                            hiddenInput.value = (index.toString() === e.target.value) ? '1' : '0';
                        }
                    });
                }
            });

            // Validasi form sebelum submit
            form.addEventListener('submit', function(e) {
                const questionType = questionTypeSelect.value;
                if (questionType === 'radio_button' || questionType === 'drag_and_drop') {
                    const selectedRadio = document.querySelector('input[name="correct_answer"]:checked');
                    if (!selectedRadio) {
                        e.preventDefault();
                        alert('Pilih satu jawaban yang benar untuk tipe soal Radio Button');
                        return;
                    }

                    const correctAnswers = document.querySelectorAll('input[name$="[is_correct]"][value="1"]');
                    if (correctAnswers.length !== 1) {
                        e.preventDefault();
                        alert('Harus ada tepat satu jawaban yang benar untuk tipe soal Radio Button');
                    }
                }
            });

            // Inisialisasi awal
            handleQuestionTypeChange();
        });
    </script>
    @endpush
</x-layout>