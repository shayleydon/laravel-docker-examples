<?php

// use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
// use Illuminate\Support\Facades\Redis;

Route::get('/', function () {
    Log::info('Welcome page visited');
    return view('welcome');
});

Route::get('/info', function () {
    Log::info('Phpinfo page visited');
    return phpinfo();
});

Route::get('/health', function () {
    $status = [];

    // Check Database Connection
    try {
        DB::connection()->getPdo();
        // Optionally, run a simple query
        DB::select('SELECT 1');
        $status['database'] = 'OK';
    } catch (\Exception $e) {
        $status['database'] = 'Error';
    }

/*
    var_dump(Cache::store('redis')->getPrefix());
    // Store data in the cache
    Cache::store('redis')->put('key', 'value', 3600);
    // Retrieve data from the cache
    $value = Cache::store('redis')->get('key');
    var_dump($value);
    exit;
*/

/*
    // use Illuminate\Support\Facades\Redis;
    $keys = Redis::connection()->keys('*');
    var_dump($keys);
    exit;
*/

/*
    // Check Redis Connection using Predis 
    // Make sure you have installed Predis via Composer
    // composer require predis/predis
    // If you are using Laravel, you can use the built-in Redis facade
    // Prepend a base path if Predis is not available in your "include_path".

    require '../vendor/laravel/framework/src/Illuminate/Redis/vendor/predis/predis/autoload.php';
    Predis\Autoloader::register();

    // require 'redis/Autoload.php';
    // Predis\Autoloader::register();
    // $client = new Predis\Client();
    $client = new Predis\Client([
        'scheme' => 'tcp',
        // 'host'   => '172.18.0.2',
        'host'   => 'redis',
        'port'   => 6379,
    ]);
    // var_dump($client);
  
    $client->set('foo', 'bar');
    $value = $client->get('foo');
    echo $value;
    exit;
    
    try {
        // Check Redis Connection
        // $redis = new Redis();
        // $objRedis = new Redis();
        $client->connect( '172.18.0.2', 6379 );
        // $redis->connect('127.0.0.1', 6379);
        $status['redis'] = 'OK';
    } catch (\Exception $e) {
        $status['redis'] = 'Error';
        var_dump($e->getMessage());
        exit;
    }

    var_dump($status['redis']);
        exit;
*/
    
/*
    try {
        // Check Redis Connection
        Cache::store('redis')->connection()->ping();
        $status['redis'] = 'OK';
    } catch (\Exception $e) {
        $status['redis'] = 'Error';
        var_dump($e->getMessage());
        exit;
    }
    var_dump($status['redis']);
    exit;
*/
/*
    $ip[] = gethostbyname('redis');
    $ip[] = gethostbyname('web');
    $ip[] = gethostbyname('postgres');
    $ip[] = gethostbyname('php-fpm');
    $ip[] = gethostbyname('test');

    var_dump($ip);
    exit;
*/
    // Check Redis Connection
    try {

        // var_dump(Cache::store('redis')->getStore());
        // var_dump(Cache::store('redis')->getPrefix());
        Cache::store('redis')->put('aaa', 'aaa1', 10);
        Cache::store('redis')->put('bbb', 'bbb1', 10);
        Cache::store('redis')->put('ccc', 'ccc1', 10);
        $keys = Cache::store('redis')->connection()->keys('*');
        //original
        Cache::store('redis')->put('health_check', 'OK', 10);
        $value = Cache::store('redis')->get('health_check');
        if ($value === 'OK') {
            $status['redis'] = 'OK';
        } else {
            $status['redis'] = 'Error';
        }
    } catch (\Exception $e) {
        $status['redis'] = 'Exception';
        var_dump($e->getMessage());
    }

    // Check Storage Access
    try {
        $testFile = 'health_check.txt';
        Storage::put($testFile, 'OK');
        $content = Storage::get($testFile);
        Storage::delete($testFile);

        if ($content === 'OK') {
            $status['storage'] = 'OK';
        } else {
            $status['storage'] = 'Error';
        }
    } catch (\Exception $e) {
        $status['storage'] = 'Error';
    }

    // Determine overall health status
    $isHealthy = collect($status)->every(function ($value) {
        return $value === 'OK';
    });

    $httpStatus = $isHealthy ? 200 : 503;

    return response()->json($status, $httpStatus);
});
