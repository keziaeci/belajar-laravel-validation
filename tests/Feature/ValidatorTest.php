<?php

namespace Tests\Feature;

use App\Rules\RegistrationRule;
use App\Rules\Uppercase;
use Closure;
use Illuminate\Validation\Rules\In;
use Tests\TestCase;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Validator as ValidationValidator;

use function PHPUnit\Framework\assertTrue;

use function PHPUnit\Framework\assertFalse;
use function PHPUnit\Framework\assertNotNull;

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
        App::setLocale("id");
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

    function testValidationInlineMessage() {
        App::setLocale("id");
        $data = [
            'username' => 'rena',
            'password' => '213'
        ];
        
        $rules = [
            'username' => 'required|email|max:100',
            'password' => 'required|min:6|max:20'
        ];

        $message = [
            'required' => ':attribute harus diisi',
            'email' => ':attribute harus berupa email',
            'min' => ':attribute minimal :min karakter',
            'max' => ':attribute maximal :max karakter',
        ];

        $validator = Validator::make($data,$rules,$message);
        assertNotNull($validator);

        assertFalse($validator->passes());
        assertTrue($validator->fails());

        $msg = $validator->getMessageBag();
        Log::error($msg->toJson(JSON_PRETTY_PRINT));
        // dd($keys);
    }

    function testValidationAdditionalValidation() {
        $data = [
            'username' => 'rena@gmail.com',
            'password' => 'rena@gmail.com'
        ];
        
        $rules = [
            'username' => 'required|email|max:100',
            'password' => 'required|min:6|max:20'
        ];

        $validator = Validator::make($data,$rules);
        /* berfungsi untuk menambah rules baru setelah menjalankan validasi dengan rules diatas.
        melakukan dobel pengecekan dengan rules tambahan */
        $validator->after(function (ValidationValidator $validator){
            $data = $validator->getData();
            if ($data['username'] == $data['password']) {
                $validator->errors()->add('password','Password tidak boleh sama dengan username');
            }
        });
        assertNotNull($validator);

        assertFalse($validator->passes());
        assertTrue($validator->fails());

        $msg = $validator->getMessageBag();
        Log::error($msg->toJson(JSON_PRETTY_PRINT));
    }

    function testValidationCustomRules() {
        $data = [
            'username' => 'rena@gmail.com',
            'password' => 'rena@gmail.com'
        ];
        
        $rules = [
            // 'username' => ['required','email','max:100', 'uppercase'],
            'username' => ['required','email','max:100', new Uppercase()],
            'password' => ['required','min:6','max:20', new RegistrationRule()],
            // 'password' => 'required|min:6|max:20'
        ];

        $validator = Validator::make($data,$rules);
        assertNotNull($validator);

        assertFalse($validator->passes());
        assertTrue($validator->fails());

        $msg = $validator->getMessageBag();
        Log::error($msg->toJson(JSON_PRETTY_PRINT));
    }
    
    function testValidationCustomFunctionRule() {
        $data = [
            'username' => 'rena@gmail.com',
            'password' => 'rena@gmail.com'
        ];
        
        $rules = [
            // jika custom rule tidak perlu untuk reuse maka gunakan ini daripada membuat class untuk rule spt di atas
            'username' => ['required','email','max:100', function ($attributes,$value, Closure $fail) {
                if ($value != strtoupper($value)) {
                    $fail("The $attributes must be uppercase");
                }
            }],
            'password' => ['required','min:6','max:20', new RegistrationRule()],
        ];

        $validator = Validator::make($data,$rules);
        assertNotNull($validator);

        assertFalse($validator->passes());
        assertTrue($validator->fails());
        
        $msg = $validator->getMessageBag();
        Log::error($msg->toJson(JSON_PRETTY_PRINT));
    }

    function testValidationRuleClasses() {
        $data = [
            'username' => 'Rena',
            'password' => 'rena123@gmail.com'
        ];
        
        $rules = [
            'username' => ['required', new In(["Rena", "Maria","Putri"])],
            // 'username' => ['required','email','max:100', new Uppercase()],
            'password' => ['required', Password::min(6)->letters()->symbols()->numbers()],
        ];
    
        $validator = Validator::make($data,$rules);
        assertNotNull($validator);
    
        assertTrue($validator->passes());
        assertFalse($validator->fails());
    
        // $msg = $validator->getMessageBag();
        // Log::error($msg->toJson(JSON_PRETTY_PRINT));
    }
    
    function testValidationNestedArray() {
        $data = [
            'name' => [
                'first' => 'Maria',
                // 'last' => 'Regina',
            ],
            'address' => [
                'street' => 'Jalan yang jauh',
                'city' => 'Seemarank',
                'country' => 'Lawaknesia',
            ]
        ];
        
        $rules = [
            'name.first' => ['required','max:100'],
            'name.last' => ['max:100'],
            'address.street' => ['max:200'],
            'address.city' => ['required','max:200'],
            'address.country' => ['required','max:200'],
        ];
    
        $validator = Validator::make($data,$rules);
        assertNotNull($validator);
    
        assertTrue($validator->passes());
        assertFalse($validator->fails());
    
        // $msg = $validator->getMessageBag();
        // Log::error($msg->toJson(JSON_PRETTY_PRINT));
    }

    function testValidationNestedIndexedArray() {
        $data = [
            'name' => [
                'first' => 'Maria',
                // 'last' => 'Regina',
            ],
            'address' => [
                [
                    'street' => 'Jalan yang jauh',
                    'city' => 'Seemarank',
                    'country' => 'Lawaknesia',
                ],
                [
                    'street' => 'Jalan panjang',
                    'city' => 'Seemarank',
                    'country' => 'Lawaknesia',
                ],
            ]
        ];
        // ternyata * digunakan untuk akses data di indexed array
        $rules = [
            'name.first' => ['required','max:100'],
            'name.last' => ['max:100'],
            'address.*.street' => ['max:200'],
            'address.*.city' => ['required','max:200'],
            'address.*.country' => ['required','max:200'],
        ];
    
        $validator = Validator::make($data,$rules);
        assertNotNull($validator);
    
        assertTrue($validator->passes());
        assertFalse($validator->fails());
    }


}