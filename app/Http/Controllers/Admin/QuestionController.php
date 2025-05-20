<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Models\Answer;
use App\Models\Material;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    public function index(Request $request, Material $material = null)
    {
        $user = auth()->user();
        $search = $request->input('search');

        $questions = Question::with(['createdBy', 'answers', 'material'])
            ->when($search, function ($query) use ($search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('question_text', 'like', "%{$search}%")
                        ->orWhere('question_type', 'like', "%{$search}%")
                        ->orWhereHas('createdBy', function ($userQuery) use ($search) {
                            $userQuery->where('name', 'like', "%{$search}%");
                        })
                        ->orWhereHas('material', function ($materialQuery) use ($search) {
                            $materialQuery->where('title', 'like', "%{$search}%");
                        });
                });
            })
            ->when($material, function ($query) use ($material) {
                return $query->where('material_id', $material->id);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        // Format question types for display
        $questions->transform(function ($question) {
            $question->formatted_type = match($question->question_type) {
                'radio_button' => 'Radio Button',
                'drag_and_drop' => 'Drag and Drop',
                'fill_in_the_blank' => 'Fill in the Blank',
                default => $question->question_type
            };
            return $question;
        });

        return view('admin.questions.index', [
            'questions' => $questions,
            'userName' => $user->name,
            'userRole' => $user->role->role_name,
            'material' => $material,
            'search' => $search // Pass the search term back to the view
        ]);
    }


    public function create(Material $material = null)
    {
        if ($material) {
            // If material is provided, only show that material
            $materials = collect([$material]);
            return view('admin.questions.create', compact('materials', 'material'));
        } else {
            // Otherwise show all materials (for the general create route)
            $materials = Material::all();
            return view('admin.questions.create', compact('materials'));
        }
    }

    public function store(Request $request, Material $material = null)
    {
        $request->validate([
            'material_id' => $material ? 'nullable' : 'required|exists:materials,id',
            'question_text' => 'required|string',
            'question_type' => 'required|in:radio_button,drag_and_drop,fill_in_the_blank',
            'answers' => 'required|array|min:1',
            'answers.*.answer_text' => 'required|string',
            'answers.*.is_correct' => 'required|boolean',
            'answers.*.explanation' => 'nullable|string|max:500'
        ]);

        $questionType = $request->question_type;

        if (in_array($request->question_type, ['radio_button', 'drag_and_drop'])) {
            $correctAnswersCount = collect($request->answers)->where('is_correct', true)->count();
            if ($correctAnswersCount !== 1) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->with('error', 'Soal ' . ucfirst(str_replace('_', ' ', $questionType)) . ' hanya boleh memiliki 1 jawaban benar.');
            }
        }
        

        $question = Question::create([
            'material_id' => $material ? $material->id : $request->material_id,
            'question_text' => $request->question_text,
            'question_type' => $request->question_type,
            'created_by' => auth()->id()
        ]);

        foreach ($request->answers as $answer) {
            Answer::create([
                'question_id' => $question->id,
                'answer_text' => $answer['answer_text'],
                'is_correct' => $answer['is_correct'],
                'explanation' => $answer['explanation'] ?? null,
                'drag_source' => $answer['drag_source'] ?? null,
                'drag_target' => $answer['drag_target'] ?? null,
                'blank_position' => $answer['blank_position'] ?? null
            ]);
        }
        // return redirect()->route($material ? 'materials.questions.index' : 'questions.index', $material ?? [])
        // ->with('success', 'Question created successfully.');
        if ($material) {
            return redirect()
                ->route('admin.materials.questions.index', $material)
                ->with('success', 'Soal berhasil ditambahkan.');
        }

        return redirect()
            ->route('admin.questions.index')
            ->with('success', 'Soal berhasil ditambahkan.');
    }

    public function edit(Material $material = null, Question $question)
    {
        $materials = Material::all();

        $material = $question->material; // Get the question's material
        return view('admin.questions.edit', compact('question', 'materials', 'material'));
        // $materials = Material::all();
        // return view('questions.edit', compact('question', 'materials', 'material'));
    }

    public function update(Request $request, Material $material = null, Question $question)
    {
        $request->validate([
            'material_id' => 'required|exists:materials,id',
            'question_text' => 'required|string',
            'question_type' => 'required|in:radio_button,drag_and_drop,fill_in_the_blank',
            'answers' => 'required|array|min:1',
            'answers.*.answer_text' => 'required|string',
            'answers.*.is_correct' => 'required|boolean',
            'answers.*.explanation' => 'nullable|string|max:500'
        ]);

        $questionType = $request->question_type;

        if (in_array($questionType, ['radio_button', 'fill_in_the_blank'])) {
            $correctAnswersCount = collect($request->answers)->where('is_correct', '1')->count();
            if ($correctAnswersCount !== 1) {
                return response()->json([
                    'status' => 'error',
                    'message' => ucfirst(str_replace('_', ' ', $questionType)) . ' Pertanyaan hanya boleh memliki 1 jawaban.'
                ], 422);
            }
        }
        // // Ensure only one correct answer for radio button type
        // if ($request->question_type === 'radio_button') {
        //     $correctAnswers = collect($request->answers)->where('is_correct', true)->count();
        //     if ($correctAnswers !== 1) {
        //         return response()->json([
        //             'status' => 'error',
        //             'message' => 'Radio button questions must have exactly one correct answer'
        //         ], 422);
        //     }
        // }

        $question->update([
            'material_id' => $material ? $material->id : $request->material_id,
            'question_text' => $request->question_text,
            'question_type' => $request->question_type
        ]);

        // Delete existing answers
        $question->answers()->delete();

        // Create new answers
        foreach ($request->answers as $answer) {
            Answer::create([
                'question_id' => $question->id,
                'answer_text' => $answer['answer_text'],
                'is_correct' => $answer['is_correct'],
                'explanation' => $answer['explanation'] ?? null,
                'drag_source' => $answer['drag_source'] ?? null,
                'drag_target' => $answer['drag_target'] ?? null,
                'blank_position' => $answer['blank_position'] ?? null
            ]);
        }

        $material = $question->material;
        
        // return redirect()->route($material ? 'materials.questions.index' : 'questions.index', ['material' => $material?->id])
        // ->with('success', 'Question updated successfully.');
        if ($material) {
            return redirect()
                ->route('admin.materials.questions.index', $material)
                ->with('success', 'Question updated successfully.');
        }

        return redirect()
            ->route('admin.questions.index')
            ->with('success', 'Question updated successfully.');
    }

    public function destroy(Material $material = null, Question $question)
    {
        $material_id = $question->material_id;
        $question->answers()->delete();
        $question->delete();

        if ($material) {
            return redirect()
                ->route('admin.materials.questions.index', $material)
                ->with('success', 'Soal berhasil dihapus.');
        }

        return redirect()
            ->route('admin.questions.index')
            ->with('success', 'Soal berhasil dihapus.');
    }
}