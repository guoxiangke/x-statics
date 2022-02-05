<?php

use Illuminate\Support\Facades\Route;
use App\Jobs\GampQueue;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

// https://laravel.com/api/8.x/Illuminate/Routing/Redirector.html#method_away
    // Redirect::away(
Route::get('/redirect', function (Request $request) {
    $target = $request->query('target');
    $status = 302;
    $headers = ['referer' => $target];
    // https://divinglaravel.com/running-a-task-after-the-response-is-sent
    // https://dev.to/webong/execute-an-action-after-laravel-returns-response-4pjc
    $ip = $request->header('x-forwarded-for')??$request->ip();
    $basename = basename($target); //cc201221.mp3
    $parts = parse_url($target); //$parts['host']
    GampQueue::dispatchAfterResponse($ip, $parts['host'], $basename, 'redirect');
    return redirect()->away($target, $status, $headers);
});