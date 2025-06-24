<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Material;
use App\Models\Progress;
use App\Models\Question;
use App\Models\Answer;
use App\Models\QuestionBankConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log; // Tambahkan ini

class MaterialQuestionController extends Controller
{
    // ...existing code...

    public function checkAnswer(
        Material $material,
        Question $question,
        Request $request
    ) {
        try {
            $difficulty = $request->input('difficulty', 'beginner');
            $userId = auth()->id() ?? session()->getId();
            $isGuest =
                !auth()->check() ||
                (auth()->check() && auth()->user()->role_id === 4);

            // Default messages
            $successMessage = 'Jawaban benar!';
            $selectedAnswerText = null;
            $correctAnswerText = null;
            $explanation = null;

            // Logic untuk mengecek jawaban
            $isCorrect = false;
            $questionType = $question->question_type;

            // Check jawaban berdasarkan tipe soal
            if ($questionType === 'multiple_choice') {
                $selectedAnswer = Answer::findOrFail($request->answer);
                $isCorrect = $selectedAnswer->is_correct;
                $selectedAnswerText = $selectedAnswer->answer_text ?? null;
                $correctAnswer = $question->answers()->where('is_correct', 1)->first();
                $correctAnswerText = $correctAnswer ? $correctAnswer->answer_text : null;
                $explanation = $question->explanation ?? null;
            } elseif ($questionType === 'fill_in_the_blank') {
                $answer = trim(strtolower($request->fill_in_the_blank_answer));
                $correctAnswer = trim(strtolower($question->correct_answer));
                $isCorrect = $answer === $correctAnswer;
                $selectedAnswerText = $answer;
                $correctAnswerText = $correctAnswer;
                $explanation = $question->explanation ?? null;
            } elseif ($questionType === 'true_false') {
                $selectedAnswer = $request->answer === 'true';
                $isCorrect = $selectedAnswer === $question->is_true;
                $selectedAnswerText = $selectedAnswer ? 'True' : 'False';
                $correctAnswerText = $question->is_true ? 'True' : 'False';
                $explanation = $question->explanation ?? null;
            }

            // Jika user auth, simpan progress ke database
            if (auth()->check() && auth()->user()->role_id !== 4) {
                $existingCorrectProgress = Progress::where([
                    'user_id' => $userId,
                    'material_id' => $material->id,
                    'question_id' => $question->id,
                    'is_correct' => true,
                ])->exists();

                if (!$existingCorrectProgress || !$isCorrect) {
                    $attemptsCount = Progress::where([
                        'user_id' => $userId,
                        'material_id' => $material->id,
                        'question_id' => $question->id,
                    ])->count();

                    $attemptNumber = $attemptsCount > 0 ? $attemptsCount + 1 : 1;

                    Progress::create([
                        'user_id' => $userId,
                        'material_id' => $material->id,
                        'question_id' => $question->id,
                        'is_correct' => $isCorrect,
                        'is_answered' => true,
                        'attempt_number' => $attemptNumber,
                    ]);

                    Cache::forget('leaderboard_data');
                }
            } else {
                $sessionKey = 'guest_progress';
                $guestProgress = session($sessionKey, []);
                $progressKey = $material->id . '_' . $question->id;
                $guestProgress[$progressKey] = [
                    'is_correct' => $isCorrect,
                    'attempt_number' => isset($guestProgress[$progressKey])
                        ? $guestProgress[$progressKey]['attempt_number'] + 1
                        : 1,
                ];
                session([$sessionKey => $guestProgress]);
            }

            if ($isCorrect) {
                return response()->json([
                    'status' => 'success',
                    'message' => $successMessage,
                    'selectedAnswerText' => $selectedAnswerText,
                    'correctAnswerText' => $correctAnswerText,
                    'explanation' => $explanation,
                    'hasNextQuestion' => false,
                    'levelUrl' => route(
                        'mahasiswa.materials.questions.levels',
                        [
                            'material' => $material->id,
                            'difficulty' => $request->input('difficulty'),
                        ]
                    ),
                ]);
            } else {
                $nextUrl = route('mahasiswa.materials.questions.show', [
                    'material' => $material->id,
                    'difficulty' => $difficulty,
                    'question' => $question->id,
                ]);
                $hasNextQuestion = true;
            }

            return response()->json([
                'status' => $isCorrect ? 'success' : 'error',
                'message' => $isCorrect
                    ? $successMessage
                    : 'Jawaban salah, coba lagi.',
                'hasNextQuestion' => $hasNextQuestion ?? false,
                'nextUrl' => $nextUrl ?? null,
            ]);
        } catch (\Exception $e) {
            return response()->json(
                [
                    'status' => 'error',
                    'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
                ],
                500
            );
        }
    }

    public function submitAnswer(
        Request $request,
        Material $material,
        Question $question
    ) {
        try {
            $difficulty = $request->input('difficulty', 'beginner');
            $userId = auth()->id() ?? session()->getId();
            $isGuest =
                !auth()->check() ||
                (auth()->check() && auth()->user()->role_id === 4);

            $successMessage = 'Jawaban benar!';
            $isCorrect = false;
            $questionType = $question->question_type;

            if ($questionType === 'multiple_choice') {
                $selectedAnswer = Answer::findOrFail($request->answer);
                $isCorrect = $selectedAnswer->is_correct;
            } elseif ($questionType === 'fill_in_the_blank') {
                $answer = trim(strtolower($request->fill_in_the_blank_answer));
                $correctAnswer = trim(strtolower($question->correct_answer));
                $isCorrect = $answer === $correctAnswer;
            } elseif ($questionType === 'true_false') {
                $selectedAnswer = $request->answer === 'true';
                $isCorrect = $selectedAnswer === $question->is_true;
            }

            if (auth()->check() && auth()->user()->role_id !== 4) {
                $existingCorrectProgress = Progress::where([
                    'user_id' => $userId,
                    'material_id' => $material->id,
                    'question_id' => $question->id,
                    'is_correct' => true,
                ])->exists();

                if (!$existingCorrectProgress || !$isCorrect) {
                    $attemptsCount = Progress::where([
                        'user_id' => $userId,
                        'material_id' => $material->id,
                        'question_id' => $question->id,
                    ])->count();

                    $attemptNumber = $attemptsCount > 0 ? $attemptsCount + 1 : 1;

                    Progress::create([
                        'user_id' => $userId,
                        'material_id' => $material->id,
                        'question_id' => $question->id,
                        'is_correct' => $isCorrect,
                        'is_answered' => true,
                        'attempt_number' => $attemptNumber,
                    ]);

                    Cache::forget('leaderboard_data');
                }
            }
            // Tambahkan response agar tidak error
            return response()->json([
                'status' => $isCorrect ? 'success' : 'error',
                'message' => $isCorrect ? $successMessage : 'Jawaban salah, coba lagi.',
            ]);
        } catch (\Exception $e) {
            Log::error('Error submit answer: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], 500);
        }
    }

    // ...existing code...
}