<div class="review-container">
    <div class="review-header d-flex justify-content-between align-items-center">
        <h3 class="review-title">
            <i class="fas fa-clipboard-check me-2"></i>Review Semua Soal
            @if(auth()->user()->role_id === 4)
                <small class="text-muted">(Tamu)</small>
            @endif
        </h3>
        @if(auth()->user()->role_id !== 4)
            <div class="action-buttons">
                <form action="{{ route('mahasiswa.materials.reset', $material->id) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-redo me-2"></i>Kerjakan Ulang
                    </button>
                </form>
            </div>
        @endif
    </div>

    @foreach($material->questions as $index => $question)
        <div class="question-review">
            <div class="question-header">
                <span class="question-number">
                    <i class="fas fa-question-circle"></i>
                    Soal {{ $index + 1 }} dari {{ $material->questions->count() }}
                </span>
            </div>
            
            <div class="question-content">
                <h5 class="mb-3"><i class="fas fa-question me-2"></i>Pertanyaan</h5>
                <div class="question-text">
                    {{ $question->question_text }}
                </div>

                <h5 class="mt-4 mb-3"><i class="fas fa-list-ul me-2"></i>Pilihan Jawaban</h5>
                <div class="answers-container">
                    @foreach($question->answers as $answer)
                        <div class="answer-option {{ $answer->is_correct ? 'correct-answer' : '' }}">
                            <div class="answer-text">
                                @if($answer->is_correct)
                                    <i class="fas fa-check-circle text-success me-2"></i>
                                @endif
                                {{ $answer->answer_text }}
                            </div>
                            @if($answer->explanation)
                                <div class="answer-explanation">
                                    <i class="fas fa-info-circle"></i>
                                    {{ $answer->explanation }}
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endforeach

    <div class="navigation-buttons">
        <a href="{{ route('mahasiswa.materials.index') }}" class="btn btn-primary me-2">
            <i class="fas fa-book me-2"></i>Kembali ke Daftar Materi
        </a>
        @if(auth()->user()->role_id !== 4)
            <a href="{{ route('mahasiswa.dashboard') }}" class="btn btn-info">
                <i class="fas fa-home me-2"></i>Dashboard
            </a>
        @endif
    </div>
</div> 