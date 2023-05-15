<?php

namespace App\Http\Controllers;

use App\Models\AllowedDomain;
use App\Models\Form;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FormController extends Controller
{
    public function createForm(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'name' => 'required',
            'slug' => 'required|unique:forms|regex:/^[a-zA-Z0-9.-]+$/|not_regex:/\s/',
            'allowed_domains' => 'array'
        ]);

        if ($validated->fails()) {
            return response()->json([
                'message' => 'Invalid field',
                'errors' => $validated->errors()
            ], 422);
        }

        $data = $request->only(['name', 'slug', 'description', 'limit_one_response']);
        $data['creator_id'] = auth()->user()->id;
        $form = Form::create($data);

        for ($i = 0; $i < count($request->allowed_domains); $i++) {
            AllowedDomain::create([
                'form_id' => $form->id,
                'domain' => $request->allowed_domains[$i]
            ]);
        }

        return response()->json([
            'message' => 'Create form success',
            'form' => $form
        ], 200);
    }

    public function getForms()
    {
        $forms = Form::all();

        return response()->json([
            'message' => 'Get all forms success',
            'forms' => $forms
        ], 200);
    }

    public function detailForm($slug)
    {
        $form = Form::with(['allowed_domains', 'questions'])->where('slug', $slug)->first();

        // if form is not found
        if (!$form) {
            return response()->json([
                'message' => 'Form not found'
            ], 404);
        }

        $temp = [];
        foreach ($form['allowed_domains'] as $value) {
            $temp[] = $value['domain'];
        }
        unset($form['allowed_domains']);
        $form['allowed_domains'] = $temp;

        // if user email is not allowed
        if (!in_array(explode('@', auth()->user()->email)[1], $temp)) {
            return response()->json([
                'message' => 'Forbidden access'
            ], 403);
        }

        // success get form
        return response()->json([
            'message' => 'Get form success',
            'form' => $form
        ], 200);
    }
}
