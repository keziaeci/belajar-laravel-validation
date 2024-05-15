<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Log;

class FormController extends Controller
{
    function submitForm(LoginRequest $request) {
        // $data = $request->validate([
        //     'username' => 'required',
        //     'password' => 'required',
        // ]);
        $data = $request->validated();
        Log::info(json_encode($request->all(),JSON_PRETTY_PRINT));
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
