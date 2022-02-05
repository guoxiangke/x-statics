<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Jobs\GampQueue;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
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