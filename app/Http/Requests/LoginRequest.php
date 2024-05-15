<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rules\Password;
use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    // protected $stopOnFirstFailure = true;
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(){
        $this->merge([
            'username' => strtolower($this->input('username'))
        ]);
    }


    function passedValidation() {
        $this->merge([
            'password' => bcrypt($this->input('password'))
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'username' => ['required','email','max:100'],
            'password' => ['required', Password::min(6)->letters()->symbols()->numbers()],
        ];
    }
}
