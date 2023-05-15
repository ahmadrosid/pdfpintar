<?php

use App\Http\Controllers\DocumentController;
use Illuminate\Support\Facades\Route;

Route::resource('documents', DocumentController::class);
