<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

class FormController extends Controller
{
    function submitForm(Request $request) {
        $data = $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);
        return response('OK', Response::HTTP_OK);
    }
    function login(Request $request)  {
        try {
            $data = $request->validate([
                'username' => 'required',
                'password' => 'required',
            ]);
            return response('OK', Response::HTTP_OK);
        } catch (\Illuminate\Validation\ValidationException $exception) {
            return response($exception->errors(), Response::HTTP_BAD_REQUEST);
        }
    }
}
