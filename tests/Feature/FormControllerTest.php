<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class FormControllerTest extends TestCase
{
    function testLoginFailed() {
        $response = $this->post('/form/login', [
            'username' => '',
            'password' => '',
        ]);

        $response->assertStatus(400);
    }

    function testLoginSuccess() {
        $response = $this->post('/form/login', [
            'username' => 'ren',
            'password' => '123',
        ]);

        $response->assertStatus(200);
    }
}
