<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();



Route::group(["middlware"=>"auth"],function(){
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
});
