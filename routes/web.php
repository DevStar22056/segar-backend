<?php
/*
 * Just disable root route
 */
//Route::get('/', function () {
//    abort(404);
//});

// Route::get('/', function () {
//     return view('welcome');
// });

//Route::fallback(function () {
//    return response()->json([
//        'message' => 'No access.'
//    ], 404);
//});

Auth::routes(['register' => false, 'login' => false]);

//Route::get('/home', 'HomeController@index')->name('home');
