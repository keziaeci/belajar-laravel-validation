<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
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
            'password' => ''
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
    }   
}
