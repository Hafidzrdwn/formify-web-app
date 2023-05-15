<?php

namespace App\Http\Controllers;

use App\Models\Form;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class QuestionController extends Controller
{
    public function addQuestion(Request $request, $slug)
    {
        $enum = ['short answer', 'paragraph', 'date', 'multiple choice', 'dropdown', 'checkboxes'];
        $validated = Validator::make($request->all(), [
            'name' => 'required',
            'choice_type' => [
                'required',
                Rule::in($enum)
            ],
            'choices' => [
                Rule::requiredIf(function () use ($request) {
                    return in_array($request->choice_type, ['multiple choice', 'dropdown', 'checkboxes']);
                })
            ]
        ]);
        if ($validated->fails()) {
            return response()->json([
                'message' => 'Invalid field',
                'errors' => $validated->errors()
            ], 422);
        }

        $form = Form::where('slug', $slug)->first();
        if (!$form) {
            return response()->json([
                "message" => "Form not found"
            ], 404);
        }

        if (auth()->user()->id != $form->creator_id) {
            return response()->json([
                "message" => "Forbidden access"
            ], 403);
        }

        $data = $request->all();
        if (array_key_exists('choices', $data) && is_array($data['choices'])) $data['choices'] = implode(',', $data['choices']);
        else $data['choices'] = null;
        $data['form_id'] = $form->id;

        $question = Question::create($data);

        return response()->json([
            'message' => 'Add question success',
            'question' => $question
        ], 200);
    }

    public function removeQuestion($slug, $id)
    {
        $form = Form::where('slug', $slug)->first();
        if (!$form) {
            return response()->json([
                "message" => "Form not found"
            ], 404);
        }

        $question = Question::find($id);
        if (!$question) {
            return response()->json([
                "message" => "Question not found"
            ], 404);
        }

        if (auth()->user()->id != $form->creator_id) {
            return response()->json([
                "message" => "Forbidden access"
            ], 403);
        }

        Question::destroy($id);

        return response()->json([
            'message' => 'Remove question success'
        ], 200);
    }
}
