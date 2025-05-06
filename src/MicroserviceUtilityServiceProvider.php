<?php

namespace ShaonMajumder\MicroserviceUtility;

use Illuminate\Support\ServiceProvider;
use ShaonMajumder\MicroserviceUtility\Console\Commands\RestartApp;
use ShaonMajumder\MicroserviceUtility\Http\Middleware\ApiKeyAuth;
use ShaonMajumder\MicroserviceUtility\Services\HealthCheckService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class MicroserviceUtilityServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->commands([
            RestartApp::class,
        ]);
        $this->publishApiKey();

        $this->app->singleton(HealthCheckService::class, function () {
            return new HealthCheckService();
        });
    }

    public function boot()
    {
        $this->app['router']->aliasMiddleware('microservice-utility.api.key', ApiKeyAuth::class);
        $this->loadRoutesFrom(__DIR__ . '/routes.php');
    }

    protected function publishApiKey()
    {
        $envFile = base_path('.env');
        $apiKey = env('MICROSERVICE_API_KEY');
        if (!$apiKey) {
            $newApiKey = Str::random(32);
            file_put_contents($envFile, "\nMICROSERVICE_API_KEY={$newApiKey}", FILE_APPEND);
            Log::info("MicroserviceUtility Package Installed. API Key: {$newApiKey}");
            echo "\033[32mMicroserviceUtility Package Installed. Your API Key: {$newApiKey}\033[0m\n";
        }
    }
}
