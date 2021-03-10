<?php

namespace App\Http\Controllers;

use App\BankAccount;
use App\Confirmation;
use App\Http\Controllers\Auth\RegisterController;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Contact;
use App\Agreement;

class ApiController extends Controller
{
    public $loginAfterSignUp = false;

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function register(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|unique:users',
            'password' => 'required',
        ]);

        $hrm_user['candidate_email'] = DB::connection('searger')
            ->table('email_addresses')
            ->where('address', 'LIKE', $request->email)
            ->get()
            ->first();

        if (!empty($hrm_user['candidate_email']->id)) {

            // get all user info from candidates table
            $hrm_user['candidate_data'] = DB::connection('searger')
                ->table('candidates')
                ->where('id', '=', $hrm_user['candidate_email']->entity_id)
                ->get()
                ->first();

            // get address details from HRM
            $hrm_user['user_address'] = DB::connection('searger')
                ->table('addresses')
                ->where('addresses.id', $hrm_user['candidate_email']->entity_id)
                ->leftJoin('cities', 'cities.id', '=', 'addresses.city_id')
                ->leftJoin('cities_translations', 'cities.id', '=', 'cities_translations.city_id')
                ->where('cities_translations.locale', '=', 'pl')
                ->get();

            $user = new User();

            // data from HRM
            $user->name = $hrm_user['candidate_data']->forename;
            $user->surname = $hrm_user['candidate_data']->surname;
            $user->email = $hrm_user['candidate_email']->address;
            if (!empty($hrm_user['user_address'][0])) {
                $user->country = $hrm_user['user_address'][0]->country;
                $user->user_street = $hrm_user['user_address'][0]->street;
                $user->user_city = $hrm_user['user_address'][0]->name;
                $user->user_postal_code = $hrm_user['user_address'][0]->post_code;
            }
            $user->hrm_candidat_id = $hrm_user['candidate_email']->entity_id;
            $user->hourly_rate = $hrm_user['candidate_data']->hourly_rate;
            $user->hourly_currency = $hrm_user['candidate_data']->hourly_currency;
            $user->fixed_rate = $hrm_user['candidate_data']->annual_rate;
            $user->fixed_currency = $hrm_user['candidate_data']->annual_currency;
            $user->address_id = $hrm_user['candidate_data']->address_id;
            $user->notice_ending = $hrm_user['candidate_data']->notice_period;
            $user->type = $hrm_user['candidate_data']->type;
            $user->profile = $hrm_user['candidate_data']->profile;
            $user->invoice_company_name = $hrm_user['candidate_data']->invoice_company_name;
            $user->invoice_payment_currency = ($hrm_user['candidate_data']->hourly_currency !== null) ? $hrm_user['candidate_data']->hourly_currency : $hrm_user['candidate_data']->annual_currency;
            $user->invoice_address_id = $hrm_user['candidate_data']->invoice_address_id;
            $user->invoice_vat_no = $hrm_user['candidate_data']->invoice_vat_no;
            $user->invoice_krs_no = $hrm_user['candidate_data']->invoice_krs_no;
            $user->invoice_reference_no = $hrm_user['candidate_data']->invoice_reference_no;
            $user->invoice_bank_no = $hrm_user['candidate_data']->invoice_bank_no;
            $user->invoice_payment_deadline = $hrm_user['candidate_data']->invoice_payment_deadline;
            $user->company_nip = $hrm_user['candidate_data']->invoice_nip_no;
            $user->password = bcrypt($request->password);
            $user->user_phone = $request->phone;
            $user->role = 9;
            $user->save();

            $user->roles()->attach(\App\Role::where('name', 'user')->first());

            return response()->json([
                'success' => true,
                'data' => 'Account created!'
            ], 201);

        } else {

            return response()->json([
                'success' => false,
                'data' => 'User not found.'
            ], 404);

        }

    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $input = $request->only('email', 'password');
        $jwt_token = null;

        if (!$jwt_token = JWTAuth::attempt($input)) {

            return response()->json([
                'success' => false,
                'message' => 'Invalid Email or Password',
            ], 401);

        }

        $user = auth()->user();

        if ($user->can_login) {

            $user = auth()->user();
            $role = DB::table('role_user')->where('user_id', $user->id)->first();

            return response()->json([
                'success' => true,
                'token' => $jwt_token,
                'id' => $user->id,
                'email' => $user->email,
                'role' => (int)$user->role,
                'is_active' => ($user->is_active === 1) ? true : false
            ]);

        } else {

            return response()->json([
                'success' => false,
                'message' => "You can't login!"
            ], 403);

        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function logout(Request $request)
    {
        $this->validate($request, [
            'token' => 'required'
        ]);

        try {
            JWTAuth::invalidate($request->token);

            return response()->json([
                'success' => true,
                'message' => 'User logged out successfully'
            ]);

        } catch (JWTException $exception) {

            return response()->json([
                'success' => false,
                'message' => 'Sorry, the user cannot be logged out'
            ], 500);

        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAuthUser(Request $request)
    {
        $user = JWTAuth::authenticate($request->token);

        if ($user->hasRole('user')) {
            $user['_role'] = 'user';
        }
        if ($user->hasRole('superadmin')) {
            $user['_role'] = 'superadmin';
        }

        // get data from hrm
        if (!empty($user->hrm_candidat_id)) {
            $hrm_id = $user->hrm_candidat_id;

            // get data from searger
            $user_hrm_data = DB::connection('searger')
                ->table('candidates')
                ->where('candidates.id', $hrm_id)
                ->get();

            // get address
            $user_hrm_data[0]->address = DB::connection('searger')
                ->table('addresses')
                ->where('addresses.id', $hrm_id)
                ->leftJoin('cities', 'cities.id', '=', 'addresses.city_id')
                ->leftJoin('cities_translations', 'cities.id', '=', 'cities_translations.city_id')
                ->where('cities_translations.locale', '=', 'pl')
                ->get();

            // get email
            $user_hrm_data_email = DB::connection('searger')
                ->table('email_addresses')
                ->where('entity_id', '=', $hrm_id)
                ->get();

            // fix ID
            $user_hrm_data[0]->id = $hrm_id;

            // add contact data
            if (!empty($user_hrm_data_email)) {
                $user_hrm_data[0]->contact_details = $user_hrm_data_email[0];
            }
            $user['hrm_data'] = $user_hrm_data[0];
        }

        // devices
        $user['devices'] = DB::table('devices')
            ->select('id', 'device_name', 'device_id')
            ->where('user_id', $user->id)
            ->get();

        // bank
        $user['bank_accounts'] = DB::table('bank_accounts')
            ->where('resource_id', $user->id)
            ->get();

        // devices
        $user['confirmations'] = DB::table('confirmations')
            ->select('id', 'name', 'value')
            ->where('user_id', $user->id)
            ->get();

        // files
        $user['files'] = DB::table('fileuploads')
            ->where('source_id', $user->id)
            ->where('type', 5)
            ->get();

        $user = [
            'id' => $user->id,
            'user_residency' => $user->user_residency,
            'vat_value' => $user->vat_value,
            'cash_register' => $user->cash_register,
            'bank_name' => $user->bank_name,
            'invoice_bank_no' => $user->invoice_bank_no,
            'bank_accounts' => $user->bank_accounts,
            'bank_iban' => $user->bank_iban,
            'bank_swift_bic' => $user->bank_swift_bic,
            'invoice_payment_currency' => $user->invoice_payment_currency,
            'verified' => $user->verified,
            'files' => $user->files,
            'confirmations' => $user->confirmations,
            'devices' => $user->devices,
            'name' => $user->name,
            'surname' => $user->surname,
            'email' => $user->email,
            'hourly_rate' => $user->hourly_rate,
            'hourly_currency' => $user->hourly_currency,
            'invoice_company_name' => $user->invoice_company_name,
            'company_nip' => $user->company_nip,
            'company_city' => $user->company_city,
            'company_postal_code' => $user->company_postal_code,
            'company_street' => $user->company_street,
            'country' => $user->country,
            'user_street' => $user->user_street,
            'user_city' => $user->user_city,
            'user_postal_code' => $user->user_postal_code,
            'user_phone' => $user->user_phone,
            'overtime_hour_rate' => $user->overtime_rate,
            'company_type' => $user->company_type,
            'oncall_10' => $user->oncall_10,
            'oncall_30' => $user->oncall_30,
            'language' => $user->language,
        ];

        return response()->json([$user], 200);
    }

    /**
     * @param Request $request
     * @param RegisterController $registerController
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendSMS(Request $request, RegisterController $registerController)
    {
        $phone = $request->phone;
        $code = $registerController->sendSMS($phone);
//        $code = 2000;

        return response()->json([
            'code' => $code
        ], 200);
    }

    /**
     * @param Request $request
     * @param RegisterController $registerController
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkSMS(Request $request, RegisterController $registerController)
    {
        $phone = $request->phone;
        $code = $request->code;

        $sms_api_code = $registerController->verifySMSCode($phone, $code);
//        $sms_api_code = 204;

        return response()->json([
            'message' => 'Check status',
            'code' => $sms_api_code
        ], 200);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function checkEmailAddress(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|unique:users',
        ]);

        $hrm_user = DB::connection('searger')
            ->table('email_addresses')
            ->where('address', 'LIKE', $request->email)
            ->get()
            ->first();

        if ($hrm_user) {
            return response()->json([
                'success' => true
            ]);
        } else {
            return response()->json([
                'success' => false
            ], 404);
        }

    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCountries()
    {
        $countries = DB::connection('searger')
            ->table('countries')
            ->get();

        $countries->map(function ($value) {
            $value->id = $value->code;
        });

        return response()->json(['data' => $countries], 200);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUserDevices(Request $request)
    {
        $user = JWTAuth::authenticate($request->token);
        $devices = DB::table('devices')->where('user_id', $user->id)->get();

        return response()->json(['data' => $devices], 200);
    }

    /**
     * @param Request $request
     * @param User $user
     */
    public function selfUpdateUser(Request $request)
    {
        $user = JWTAuth::authenticate($request->token);
        $user->update($request->fields);

        // confirmations
        $this->handleConfirmations($request->confirmations, $user->id);

//        Auth::logout();
    }

    public function handleConfirmations($data, $id)
    {
        Confirmation::where('user_id', '=', $id)->delete();
        foreach ($data as $key => $val) {
            $confirmation = new Confirmation();
            $confirmation->user_id = $id;
            $confirmation->name = $key;
            $confirmation->value = $val;
            $confirmation->save();
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getContractorSuggestion(Request $request)
    {

        if (!empty($request->id)) {
            /**
             * by id
             */
            $res = DB::connection('searger')
                ->table('candidates')
                ->where('id', '=', $request->id)
                ->get();
            $hrm_data['data'] = $res;
        } elseif (!empty($request->forename) || !empty($request->surname)) {
            /**
             * by forename / surname
             */
            $hrm_data = DB::connection('searger')
                ->table('candidates')
                ->where('forename', 'LIKE', '%' . $request->forename . '%')
                ->where('surname', 'LIKE', '%' . $request->surname . '%')
                ->paginate(100);
        } elseif (!empty($request->email)) {
            /**
             * by email
             */
            $res = DB::connection('searger')
                ->table('email_addresses')
                ->where('address', 'like', '%' . $request->email . '%')
                ->join('candidates', 'candidates.id', '=', 'email_addresses.entity_id')
                ->get();
            $hrm_data['data'] = $res;
        }

        return response()->json($hrm_data, 200);
    }

    public function registerUserFromContractors(Request $request)
    {
        $response = [];
        foreach ($request->hrm_candidat_id as $item) {

            // check for existing users
            $check = DB::table('users')->where('hrm_candidat_id', '=', $item)->count();

            if ($check !== 0) {

                $response[$item] = false;

            } else {

                // find candidate email
                $hrm_user['candidate_email'] = DB::connection('searger')
                    ->table('email_addresses')
                    ->where('entity_id', 'LIKE', $item)
                    ->get()
                    ->first();

                // get all user info from candidates table
                $hrm_user['candidate_data'] = DB::connection('searger')
                    ->table('candidates')
                    ->where('id', '=', $item)
                    ->get()
                    ->first();

                // get address details from HRM
                $hrm_user['user_address'] = DB::connection('searger')
                    ->table('addresses')
                    ->where('addresses.id', $item)
                    ->leftJoin('cities', 'cities.id', '=', 'addresses.city_id')
                    ->leftJoin('cities_translations', 'cities.id', '=', 'cities_translations.city_id')
                    ->where('cities_translations.locale', '=', 'pl')
                    ->get()
                    ->first();

                $user = new User();

                // data from HRM
                $user->name = $hrm_user['candidate_data']->forename;
                $user->surname = $hrm_user['candidate_data']->surname;
                $user->email = $hrm_user['candidate_email'] ? $hrm_user['candidate_email']->address : Str::random(24) . '_random@seargin.pl';
                if (!empty($hrm_user['user_address'])) {
                    $user->country = $hrm_user['user_address']->country;
                    $user->user_street = $hrm_user['user_address']->street;
                    $user->user_city = $hrm_user['user_address']->name;
                    $user->user_postal_code = $hrm_user['user_address']->post_code;
                }
                $user->hrm_candidat_id = $item;
                $user->hourly_rate = $hrm_user['candidate_data']->hourly_rate;
                $user->hourly_currency = $hrm_user['candidate_data']->hourly_currency;
                $user->fixed_rate = $hrm_user['candidate_data']->annual_rate;
                $user->fixed_currency = $hrm_user['candidate_data']->annual_currency;
                $user->address_id = $hrm_user['candidate_data']->address_id;
                $user->notice_ending = $hrm_user['candidate_data']->notice_period;
                $user->type = $hrm_user['candidate_data']->type;
                $user->profile = $hrm_user['candidate_data']->profile;
                $user->invoice_company_name = $hrm_user['candidate_data']->invoice_company_name;
                $user->invoice_payment_currency = ($hrm_user['candidate_data']->hourly_currency !== null) ? $hrm_user['candidate_data']->hourly_currency : $hrm_user['candidate_data']->annual_currency;
                $user->invoice_address_id = $hrm_user['candidate_data']->invoice_address_id;
                $user->invoice_vat_no = $hrm_user['candidate_data']->invoice_vat_no;
                $user->invoice_krs_no = $hrm_user['candidate_data']->invoice_krs_no;
                $user->invoice_reference_no = $hrm_user['candidate_data']->invoice_reference_no;
                $user->invoice_bank_no = $hrm_user['candidate_data']->invoice_bank_no;
                $user->invoice_payment_deadline = $hrm_user['candidate_data']->invoice_payment_deadline;
                $user->company_nip = $hrm_user['candidate_data']->invoice_nip_no;
                $user->password = bcrypt($hrm_user['candidate_email']->address);
                $user->role = 9;
                $user->save();
//                $user->roles()->attach(\App\Role::where('name', 'user')->first());
                $response[$item] = true;
            }
        }
        return response()->json([
            'data' => $response
        ], 200);

    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function currencies()
    {
        $currencies = DB::connection('searger')
            ->table('currencies')->get();

        return response()->json([
            'success' => true,
            'data' => $currencies
        ], 200);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function timesheets(Request $request)
    {

        $user = JWTAuth::authenticate($request->token);

        $timesheets = DB::table('invoices')
            ->where('creator', '=', $user->id)
            ->join('fileuploads', 'invoices.id', '=', 'fileuploads.source_id')
            ->where('type', '=', 0)
            ->where('source', '=', 1)
//            ->get();
            ->get(['fileuploads.id', 'fileuploads.source_id', 'path', 'filename', 'original_name', 'fileuploads.updated_at']);

        return response()->json([
            'data' => $timesheets
        ], 200);
    }

    public function addBankAccount(Request $request)
    {
        $account_number = new BankAccount();
        $account_number->resource_id = $request->resource_id;
        $account_number->resource_type = $request->resource_type;
        $account_number->bank_name = $request->bank_name;
        $account_number->invoice_bank_no = $request->invoice_bank_no;
        $account_number->bank_iban = $request->bank_iban;
        $account_number->bank_swift_bic = $request->bank_swift_bic;
        $account_number->save();

        return response()->json(['data' => 'success'], 200);
    }

    public function addContact(Request $request)
    {
        $contact = new Contact();
        $contact->contact_name = $request->contact_name;
        $contact->position = $request->position;
        $contact->phone = $request->phone;
        $contact->mail = $request->mail;
        $contact->resource_id = $request->resource_id;
        $contact->save();

        return response()->json(['data' => 'success'], 200);
    }

    public function addAgreement(Request $request)
    {
        $agreement = new Agreement();
        $agreement->agree_from = $request->agree_from;
        $agreement->agree_to = $request->agree_to;
        $agreement->period = $request->period;
        $agreement->penalties = $request->penalties;
        $agreement->resource_id = $request->resource_id;
        $agreement->save();

        return response()->json(['data' => 'success'], 200);
    }

    public function getHrmUsersAndProjects()
    {
        $res = DB::connection('searger')
            ->table('candidate_assignment_statuses')
            ->where('status', '=', 'deal')
            ->where('employment_type', '=', 'contract')
//            ->leftJoin('projects', 'projects.id', '=', 'candidate_assignment_statuses.project_id')
            ->orderByDesc('created_at')
            ->paginate(100);
//            ->get();
//        return response()->json(array('data' => $res), 200);
        return response()->json($res, 200);
    }
}
