<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;

Route::get('/', function () {
    // return view('welcome');
    $apiUrl = 'https://pokeapi.co/api/v2/pokemon';
    $response = Http::withOptions([
        'verify' => false,
    ])->get($apiUrl, [
        'limit' => 10,
        'offset' => 0,
    ]);
    dd($response->json()['results']);
});
