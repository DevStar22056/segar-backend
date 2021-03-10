<?php

use Illuminate\Http\Request;

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

/*
 * development routes
 * TODO: remove later
 */
Route::get('verify', 'DashboardController@verify');
Route::get('verify/{email}', 'DashboardController@verify');
Route::post('verify', 'DashboardController@verify');

/*
 * API
 */
Route::post('login', 'ApiController@login');

Route::post('register', 'ApiController@register');
Route::post('register/email', 'ApiController@checkEmailAddress');
Route::post('register/sms', 'ApiController@sendSMS');
Route::post('register/validatesms', 'ApiController@checkSMS');
//Route::get('token', 'ApiController@token');
/*
 * Only after auth with jwt
 */



Route::get('invoice/{id}/{type?}/{lang?}', 'InvoiceController@show');
Route::group(['middleware' => ['jwt.verify', 'auth.jwt']], function () {
    Route::post('user-form/{id}', 'ApiController@selfUpdateUser');
    Route::get('countries', 'ApiController@getCountries');
    Route::get('auth-user', 'ApiController@getAuthUser');
    Route::get('logout', 'ApiController@logout');
    Route::get('currencies', 'ApiController@currencies');
    Route::get('timesheets', 'ApiController@timesheets');
    Route::apiResource('user', 'UserController');
    Route::resource('fileupload', 'FileuploadController');
    Route::apiResource('invoice', 'InvoiceController');
    Route::apiResource('faq', 'FaqController');

    Route::post('invoice/draft', 'InvoiceController@storeDraft');
    Route::post('invoice/correction', 'InvoiceController@storeCorrection');
    Route::post('candidate-update/{id?}', 'UserController@candidateChangeRequest');
    Route::post('add-account', 'ApiController@addBankAccount');
    Route::post('add-contact', 'ApiController@addContact');
    Route::post('add-agreement', 'ApiController@addAgreement');
});

Route::group(['middleware' => ['jwt.verify', 'auth.jwt', 'checkRole']], function () {
    Route::get('all-invoices', 'InvoiceController@getAllInvoices');
    Route::delete('candidate-update/{id?}', 'UserController@candidateChangeRequestDelete');
    Route::patch('candidate-update/{id?}', 'UserController@candidateChangeRequestUpdate');
    Route::post('hrm-candidates', 'ApiController@getContractorSuggestion');
    Route::post('register-contractor', 'ApiController@registerUserFromContractors');
    Route::get('clients', 'InvoiceController@getAllClients');
    Route::get('candidates', 'UserController@getAllCandidates');
    Route::get('contractor', 'UserController@getAllContractors');
    Route::get('device', 'ApiController@getUserDevices');
    Route::get('permy', 'ApiController@getHrmUsersAndProjects');
    Route::apiResource('cost', 'InvoiceCostController');
    Route::apiResource('project', 'ProjectController');
    Route::apiResource('device', 'DeviceController');
    Route::apiResource('invoice-contractor', 'InvoiceContractorController');
    Route::post('nip', 'GusController@index');
    Route::post('email/{id}', 'InvoiceController@sendEmail');
    Route::resource('purchasers', 'ExternalPersonaController');
    Route::resource('sellers', 'SellerController');
    Route::resource('contractors', 'ContractorController');
});

/**
 * Api routes for invoices
 */

Route::fallback(function () {
    return response()->json([
        'message' => 'Page Not Found.'
    ], 404);
});
