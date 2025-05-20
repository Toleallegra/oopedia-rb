<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Models\Answer;
use App\Models\Progress;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    public function checkAnswer(Request $request)
    {
        $request->validate([
            'question_id' => 'required|exists:questions,id',
            'material_id' => 'required|exists:materials,id',
        ]);

        $question = Question::with('answers')->findOrFail($request->question_id);
        $isCorrect = false;
        $selectedAnswerText = null;
        $correctAnswerText = null;
        $explanation = null;

        // === DRAG & DROP ===
        if ($question->question_type === 'drag_and_drop') {
            $request->validate([
                'drag_and_drop_answers' => 'required|json',
            ]);

            $userAnswers = json_decode($request->drag_and_drop_answers, true);

            // Ambil jawaban yang benar dari DB
            $correctAnswers = $question->answers()
                ->where('is_correct', true)
                ->orderBy('id') // pastikan urutan konsisten
                ->pluck('answer_text')
                ->toArray();

            $userSubmittedAnswers = collect($userAnswers)
                ->sortBy('zone') // urutkan berdasarkan zona (1, 2, 3,...)
                ->pluck('answer')
                ->toArray();

            $isCorrect = ($userSubmittedAnswers === $correctAnswers);

            $selectedAnswerText = json_encode($userSubmittedAnswers);
            $correctAnswerText = json_encode($correctAnswers);

            // Ambil penjelasan dari salah satu jawaban
            $firstCorrect = $question->answers()->where('is_correct', true)->first();
            $explanation = $firstCorrect?->explanation;

        // === ISIAN ===
        } elseif ($question->question_type === 'fill_in_the_blank') {
            $request->validate([
                'fill_in_the_blank_answer' => 'required|string',
            ]);

            $correctAnswer = Answer::where('question_id', $question->id)
                                  ->where('is_correct', true)
                                  ->first();

            if ($correctAnswer) {
                $userAnswer = strtolower(trim($request->fill_in_the_blank_answer));
                $dbAnswer = strtolower(trim($correctAnswer->answer_text));
                
                $isCorrect = ($userAnswer === $dbAnswer);
                $selectedAnswerText = $request->fill_in_the_blank_answer;
                $correctAnswerText = $correctAnswer->answer_text;
                $explanation = $correctAnswer->explanation;
            }

        // === PILIHAN GANDA ===
        } else {
            $request->validate([
                'answer' => 'required|exists:answers,id',
            ]);

            $selectedAnswer = Answer::findOrFail($request->answer);
            $isCorrect = $selectedAnswer->is_correct;
            $selectedAnswerText = $selectedAnswer->answer_text;
            $explanation = $selectedAnswer->explanation;

            if (!$isCorrect) {
                $correctAnswer = Answer::where('question_id', $question->id)
                                       ->where('is_correct', true)
                                       ->first();
                $correctAnswerText = $correctAnswer->answer_text ?? null;
            }
        }

        // Simpan progress mahasiswa
        Progress::updateOrCreate(
            [
                'user_id' => auth()->id(),
                'material_id' => $request->material_id,
                'question_id' => $question->id
            ],
            [
                'is_correct' => $isCorrect,
                'is_answered' => true
            ]
        );

        // Ambil soal berikutnya yang belum dijawab dengan benar
        $answeredQuestionIds = Progress::where('user_id', auth()->id())
            ->where('material_id', $request->material_id)
            ->where('is_correct', true)
            ->pluck('question_id')
            ->toArray();

        $nextQuestion = Question::where('material_id', $request->material_id)
            ->whereNotIn('id', $answeredQuestionIds)
            ->first();

        return response()->json([
            'status' => $isCorrect ? 'success' : 'error',
            'message' => $isCorrect ? 'Jawaban Benar!' : 'Jawaban Salah!',
            'selectedAnswer' => $selectedAnswerText,
            'correctAnswer' => !$isCorrect ? $correctAnswerText : null,
            'explanation' => $explanation,
            'hasNextQuestion' => (bool) $nextQuestion,
            'nextUrl' => route('mahasiswa.materials.show', ['material' => $request->material_id])
        ]);
    }
}