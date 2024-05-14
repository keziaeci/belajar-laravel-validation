<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

use function PHPUnit\Framework\assertFalse;
use function PHPUnit\Framework\assertNotNull;
use function PHPUnit\Framework\assertTrue;

class ValidatorTest extends TestCase
{
    function testValidator() {
        $data = [
            'username' => 'admin',
            'password' => '123'
        ];
        
        $rules = [
            'username' => 'required',
            'password' => 'required'
        ];

        $validator = Validator::make($data,$rules);
        // dd($validator);
        assertNotNull($validator);
        
        // cek apakah volidasi diatas berhasil
        assertTrue($validator->passes());
        assertFalse($validator->fails());
    }   
    function testValidatorInvalid() {
        $data = [
            'username' => '',
            'password' => ''
        ];
        
        $rules = [
            'username' => 'required',
            'password' => 'required'
        ];

        $validator = Validator::make($data,$rules);
        assertNotNull($validator);

        assertFalse($validator->passes());
        assertTrue($validator->fails());

        $msg = $validator->getMessageBag();
        $keys = $msg->keys();
        $msg->get('username');
        
        Log::info($msg->toJson(JSON_PRETTY_PRINT));
        // dd($keys);
    }
    
    function testValidationException() {
        $data = [
            'username' => '',
            'password' => ''
        ];
        
        $rules = [
            'username' => 'required',
            'password' => 'required'
        ];

        
        $validator = Validator::make($data,$rules);
        assertNotNull($validator);

        try {
            $validator->validate();
            self::fail('ValidationException not thrown');
            // dd($validator->validate());
        } catch (\Illuminate\Validation\ValidationException $exception) {
            // dd($exception->validator);
            assertNotNull($exception->validator);
            // dd($exception->validator->errors());
            Log::error($exception->validator->errors()->toJson(JSON_PRETTY_PRINT));
        }
    }

    function testValidationMultipleRules() {
        $data = [
            'username' => 'rena',
            'password' => '213'
        ];
        
        $rules = [
            'username' => 'required|email|max:100',
            'password' => 'required|min:6|max:20'
        ];

        $validator = Validator::make($data,$rules);
        assertNotNull($validator);

        assertFalse($validator->passes());
        assertTrue($validator->fails());

        $msg = $validator->getMessageBag();
        Log::error($msg->toJson(JSON_PRETTY_PRINT));
        // dd($keys);
    }

    function testValidationValidData() {
        $data = [
            'username' => 'ren@gmail.com',
            'password' => 'rahasia',
            'admin' => true 
        ];
        
        $rules = [
            'username' => 'required|email|max:100',
            'password' => 'required|min:6|max:20'
        ];

        
        $validator = Validator::make($data,$rules);
        assertNotNull($validator);

        try {
            // validate akan mengambil data yang valid yang ada di rules saja, data lain tidak eg: admin
            $valid = $validator->validate();
            Log::info(json_encode($valid,JSON_PRETTY_PRINT));
        } catch (\Illuminate\Validation\ValidationException $exception) {
            assertNotNull($exception->validator);
            Log::error($exception->validator->errors()->toJson(JSON_PRETTY_PRINT));
        }
    }
}