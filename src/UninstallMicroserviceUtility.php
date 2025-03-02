<?php

namespace ShaonMajumder\MicroserviceUtility;

use Illuminate\Support\Facades\File;

class UninstallMicroserviceUtility
{
    public static function cleanUp()
    {
        $envFile = base_path('.env');

        if (File::exists($envFile)) {
            $envContents = File::get($envFile);

            // Remove MICROSERVICE_API_KEY from .env
            $envContents = preg_replace("/\n?MICROSERVICE_API_KEY=.*/", '', $envContents);
            File::put($envFile, $envContents);
        }

        // Remove published config file if it exists
        $configPath = config_path('microservice-utility.php');
        if (File::exists($configPath)) {
            File::delete($configPath);
        }

        echo "\n✅ Microservice Utility package uninstalled. Cleanup complete.\n";
    }
}
