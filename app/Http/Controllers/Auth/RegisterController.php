<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    public function sendSMS($phoneNumber)
    {
        $url = 'https://api.smsapi.pl/mfa/codes';
        $ch = curl_init($url);
        $params = array(
            'phone_number' => $phoneNumber
        );
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer ' . env('SMSAPI_ACCESS_TOKEN')));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $result = curl_exec($ch); // example response - {"id":"5ADEF4DC3738305BEED02B0C","code":"123456","phone_number":"48500500500"}
        $headerCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        return $headerCode; // 201 - ok / 400 - nook
    }

    /**
     * Check sms code
     *
     * @param string $phoneNumber
     * @param string $code
     * @return integer
     */
    public function verifySMSCode($phoneNumber, $code)
    {
        $url = 'https://api.smsapi.pl/mfa/codes/verifications';
        $ch = curl_init($url);
        $params = array(
            'phone_number' => $phoneNumber,
            'code' => $code
        );
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer ' . env('SMSAPI_ACCESS_TOKEN')));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $result = curl_exec($ch);
        $headerCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        return $headerCode;

        /*
         * example response - HTTP/1.1 204
         * 204 - OK
         * 404 - wrong code
         * 408 - expired code
         */
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param array $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param array $data
     * @return \App\User
     */
    protected function create(array $data)
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        return $user;
    }
}
