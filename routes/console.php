<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\XMLHandler;
use App\Http\Controllers\CacheController;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
* Artisan command to call XMLHandler Controller
*/
Artisan::command('XMLtoDataBase', function() {
    XMLHandler::dump();
})->purpose('Dump XML file to database');

/*
* Artisan command to generate cache
*/
Artisan::command('cache:generate', function() {
    CacheController::generate();
})->purpose('Generate cache of API Gas');