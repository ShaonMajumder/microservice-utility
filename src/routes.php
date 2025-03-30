<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use ShaonMajumder\MicroserviceUtility\Http\Middleware\ApiKeyAuth;
use ShaonMajumder\MicroserviceUtility\UninstallMicroserviceUtility;

Route::middleware([ApiKeyAuth::class])->prefix('system')->name('microservice-utility.system.')->group(function () {
    Route::get('/health', function () {
        return response()->json(['status' => 'ok', 'timestamp' => now()]);
    })->name('health');
    Route::get('/restart', function () {
        try {
            $exitCode = Artisan::call('restart:app');
            $output = Artisan::output();
            if ($exitCode !== 0) {
                Log::error("Failed to execute Artisan restart:app command:", ['exit_code' => $exitCode, 'output' => $output]);
            }

            return response()->json([
                'message' => $exitCode === 0 ? 'Restarted successfully.' : 'Failed to restart.',
                'output' => $output
            ], $exitCode === 0 ? 200 : 500);
        } catch (Exception $e) {
            Log::error("Exception in Artisan call", ['error' => $e->getMessage()]);
            return response()->json(['message' => "Failed to restart. Error: " . $e->getMessage()], 500);
        }
    })->name('restart');

});

Route::get('/test-cleanup', function () {
    UninstallMicroserviceUtility::cleanUp();
});

Route::fallback(function () {
    return response()->json(['success' => false, 'message' => 'API route not found'], 404);
});