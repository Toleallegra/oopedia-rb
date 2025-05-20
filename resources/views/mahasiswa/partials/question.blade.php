<div class="materi-card">
    <div class="materi-card-body">
        <div id="questionContainer">
            <form id="questionForm" action="{{ route('mahasiswa.questions.check-answer') }}" method="POST">
                @csrf
                <input type="hidden" name="question_id" value="{{ $currentQuestion->id }}">
                <input type="hidden" name="material_id" value="{{ $material->id }}">

                <div class="question-header">
                    <span class="badge bg-gradient-primary">
                        <i class="fas fa-question-circle me-2"></i>
                        Soal {{ $currentQuestionNumber }} dari {{ $material->questions->count() }}
                    </span>
                </div>

                @if ($currentQuestion->question_type === 'drag_and_drop')
                    {{-- Drag and Drop --}}
                    <div class="question-content">
                        <h5 class="mb-3"><i class="fas fa-question me-2"></i>Pertanyaan</h5>
                        <div class="answers-container">
                            <div class="question-html"
                                style="font-family: monospace; background: #f8f9fa; padding: 10px; border-radius: 5px;">
                                @php
                                    $escapedText = str_replace(
                                        ['<', '>'],
                                        ['&lt;', '&gt;'],
                                        $currentQuestion->question_text,
                                    );
                                    // Replace multiple [zone] with multiple drop zones
                                    $zoneCount = substr_count($escapedText, '[zone]');
                                    for ($i = 1; $i <= $zoneCount; $i++) {
                                        $escapedText = preg_replace(
                                            '/\[zone\]/',
                                            '<span class="drop-zone" id="dropZone' .
                                                $i .
                                                '" data-zone="' .
                                                $i .
                                                '" data-user-answer=""></span>',
                                            $escapedText,
                                            1,
                                        );
                                    }
                                @endphp
                                {!! $escapedText !!}
                            </div>

                            <input type="hidden" name="drag_and_drop_answers" id="dragAndDropAnswers">

                            <h5 class="mt-4 mb-3"><i class="fas fa-list-ul me-2"></i>Pilihan Jawaban</h5>
                            <div class="drag-items d-flex flex-wrap gap-2 mt-2">
                                @foreach ($currentQuestion->answers as $answer)
                                    <div class="draggable btn btn-outline-primary" draggable="true"
                                        data-value="{{ $answer->answer_text }}">
                                        {{ $answer->answer_text }}
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @else
                    {{-- Handle other question types --}}
                    <div class="question-content">
                        <h5 class="mb-3"><i class="fas fa-question me-2"></i>Pertanyaan</h5>
                        <div class="question-text">
                            {{ $currentQuestion->question_text }}
                        </div>

                        <h5 class="mt-4 mb-3"><i class="fas fa-list-ul me-2"></i>Pilihan Jawaban</h5>
                        <div class="answers-container">
                            {{-- Multiple Choice --}}
                            @foreach ($currentQuestion->answers as $answer)
                                <div class="form-check answer-option">
                                    <input class="form-check-input" type="radio" name="answer"
                                        id="answer{{ $answer->id }}" value="{{ $answer->id }}" required>
                                    <label class="form-check-label w-100" for="answer{{ $answer->id }}">
                                        {{ $answer->answer_text }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
        </div>

        <button type="submit" class="btn btn-check-answer w-100" id="checkAnswerBtn">
            <i class="fas fa-check-circle me-2"></i>Periksa Jawaban
        </button>
    </div>
    </form>
</div>

@include('mahasiswa.partials.exercise-feedback')

</div>

<style>
    .drop-zone {
        min-width: 130px;
        min-height: 40px;
        border: 2px dashed #6c757d;
        background: linear-gradient(145deg, #f1f3f5, #e9ecef);
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 8px;
        margin: 4px;
        vertical-align: middle;
        text-align: center;
        transition: all 0.3s ease;
        font-weight: 500;
        color: #333;
    }

    .drop-zone:hover {
        background: linear-gradient(145deg, #e2e6ea, #dee2e6);
        border-color: #007bff;
        box-shadow: 0 0 0 2px rgba(0, 123, 255, 0.3);
    }

    .draggable {
        cursor: grab;
        user-select: none;
        background-color: #ffffff;
        border: 1px solid #007bff;
        border-radius: 6px;
        padding: 8px 12px;
        font-size: 14px;
        font-weight: 500;
        color: #007bff;
        box-shadow: 0 2px 4px rgba(0, 123, 255, 0.1);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .draggable:hover {
        transform: scale(1.05);
        box-shadow: 0 4px 8px rgba(0, 123, 255, 0.2);
    }

    .draggable:active {
        cursor: grabbing;
        transform: scale(0.95);
        opacity: 0.9;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const draggables = document.querySelectorAll('.draggable');
        const dropZones = document.querySelectorAll('.drop-zone');
        const answerInput = document.getElementById('dragAndDropAnswers');

        draggables.forEach(draggable => {
            draggable.addEventListener('dragstart', e => {
                e.dataTransfer.setData('text/plain', draggable.getAttribute('data-value'));
                e.dataTransfer.effectAllowed = 'move';
            });
        });

        dropZones.forEach(zone => {
            zone.addEventListener('dragover', e => {
                e.preventDefault();
                zone.style.border = '2px dashed #007bff';
            });

            zone.addEventListener('dragleave', e => {
                zone.style.border = 'none';
            });

            zone.addEventListener('drop', e => {
                e.preventDefault();
                zone.style.border = 'none';
                const value = e.dataTransfer.getData('text/plain');
                zone.textContent = value;
                zone.setAttribute('data-user-answer', value);

                // Update hidden input with user answers
                const answers = Array.from(dropZones).map(z => ({
                    zone: z.getAttribute('data-zone'),
                    answer: z.getAttribute('data-user-answer')
                }));
                answerInput.value = JSON.stringify(answers);
            });
        });

        // Form validation before submission
        document.getElementById('questionForm').addEventListener('submit', function(e) {
            const dragAndDropAnswers = document.getElementById('dragAndDropAnswers').value;
            if (!dragAndDropAnswers || dragAndDropAnswers === '[]') {
                e.preventDefault(); // Mencegah submit form jika tidak ada jawaban
                alert("Harap isi semua zona jawaban!");
            }
        });
    });
</script>