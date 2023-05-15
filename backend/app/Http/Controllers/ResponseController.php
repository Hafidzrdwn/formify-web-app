<?php

namespace App\Http\Controllers;

use App\Models\Answer;
use App\Models\Form;
use App\Models\Question;
use App\Models\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;

class ResponseController extends Controller
{
    public function submit(Request $request, $slug)
    {
        $rule = [
            'answers' => 'array'
        ];

        foreach ((array)$request->answers as $key => $value) {
            $qst = Question::where('id', $value['question_id'])->first('is_required');

            $rule['answers.' . $key . '.value'] = ($qst && $qst->is_required) ? 'required' : '';
        }

        $validated = Validator::make($request->all(), $rule, [
            'answers.*.value.required' => 'The answers field is required.'
        ]);

        if ($validated->fails()) {

            $errors = $validated->errors();
            $errors->add('answers', 'The answers field is required.');

            $transformedErrors = [
                'answers' => $errors->get('answers'),
            ];

            return response()->json([
                'message' => 'Invalid field',
                'errors' => $transformedErrors
            ], 422);
        }

        $form = Form::with('allowed_domains')->where('slug', $slug)->first();
        $domain_temp = [];
        foreach ($form['allowed_domains'] as $key => $value) {
            $domain_temp[] = $value['domain'];
        }

        if (!in_array(explode('@', auth()->user()->email)[1], $domain_temp)) {
            return response()->json([
                "message" => "Forbidden access"
            ], 403);
        }

        $limit = $form['limit_one_response'];
        $res = Response::where(['form_id' => $form->id, 'user_id' => auth()->user()->id])->count();
        if ($limit == $res) {
            return response()->json([
                "message" => "You can not submit form twice"
            ], 422);
        }

        $response = Response::create([
            'form_id' => $form->id,
            'user_id' => auth()->user()->id,
            'date' => Carbon::now()
        ]);

        foreach ($request->answers as $key => $value) {
            Answer::create([
                'response_id' => $response->id,
                'question_id' => $value['question_id'],
                'value' => $value['value'],
            ]);
        }

        return response()->json([
            "message" => "Submit response success"
        ], 200);
    }

    public function getAll($slug)
    {
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

        $responses = Response::with(['user', 'answers'])->where('form_id', $form->id)->get();
        $qna = [];

        foreach ($responses as $k => $res) {
            foreach ($res['answers'] as $value) {
                $qst = Question::find($value['question_id'])['name'];
                $qna[$k][$qst] = $value['value'];
            }
            unset($responses[$k]['answers']);
            $responses[$k]['answers'] = $qna[$k];
        }


        return response()->json([
            "message" => "Get responses success",
            'responses' => $responses
        ], 200);
    }
}
