<?php

use Illuminate\Support\Facades\Route;
use App\Jobs\GampQueue;
use Illuminate\Http\Request;
use App\Jobs\InfluxQueue;

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

Route::get('/ip', function (Request $request) {
    $ip = $request->header('x-forwarded-for')??$request->ip();
    return [$ip];
});
// http://127.0.0.1:8000/redirect?target=https://*.com/@fwdforward/7XFVL5o.m4a?metric=connect%26category=601%26bot=4
// metric:默认是connect 收听/看/点击链接
// by：author 可选 %26author=@fwdforward
Route::get('/redirect', function (Request $request) {
    $url = $request->query('target');
    $status = 302;
    $headers = ['referer' => $url];
    $ip = $request->header('x-forwarded-for')??$request->ip();
    $parts = parse_url($url); //$parts['host']
    // $paths = pathinfo($url); //mp3
    $url = strtok($url, '?'); //remove ?q=xxx
    $target = basename($url); //cc201221.mp3
    
    $data = [];
    if(isset($parts['query'])) parse_str($parts['query'], $data);
    
    // measurement/metric
    if(!isset($data['metric'])) $data['metric'] = 'connect';
    $metric = $data['metric'].",";unset($data['metric']); // ly-wechat
    // $tags = http_build_query($data, '', ',');// category=603,bot=4
    $tags = $data;

    $fields = [];
    $fields['count'] = 1;
    $fields['host'] = $parts['host'];
    $fields['target'] = $target;
    $fields['ip'] = $ip;
    // $fields = http_build_query($fields, '', ',');// category=603,bot=4
    
    $protocolLine = [
        'name' => $metric,
        'tags' => $tags,
        'fields' => $fields
    ];
    // $protocolLine = $metric.$tags.' count=1i,target="'.$target.'",ip="'.$ip.'"';
    // ly-listen,category=603,bot=%E5%8F%8B4count=1i,target="ee230909.mp3"
    // TODO Statistics BY IP / BY target.
    // dd($protocolLine,$parts,$url,$ip);
    InfluxQueue::dispatchAfterResponse($protocolLine);
    return redirect()->away($url, $status, $headers);
});