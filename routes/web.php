<?php

use App\Http\Controllers\Api\V1\PrintController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    // Redis::connection('default')->publish('pa.public.patients',json_encode(['ping'=>'pong']));
    $mappedData['before'] = null;
        $mappedData['after'] = $data;
        $mappedData['source'] = $data;
        $mappedData['source']['db'] = 'pa';
        $mappedData['source']['schema'] = 'public';
        $mappedData['source']['table'] = 'public';
    return view('welcome');
});

Route::get('subscribe',function(){

    Redis::subscribe('pa.public.patients',function($message){
        info($message);
    });

    return 'ok';
});

Route::get('generateBill/{billno}', [PrintController::class, 'generateBill']);

Route::get('barcode', function () {
  
    $generatorPNG = new Picqer\Barcode\BarcodeGeneratorPNG();
    $image = $generatorPNG->getBarcode('000005263635', $generatorPNG::TYPE_CODE_128);

    return response($image)->header('Content-type','image/png');
});