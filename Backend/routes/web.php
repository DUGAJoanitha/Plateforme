<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'message' => 'PISE-PP API is running',
        'version' => '1.0.0',
        'documentation' => '/api/docs',
    ]);
});
