<?php

namespace ShaonMajumder\MicroserviceUtility\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Illuminate\Support\Facades\Log;

class RestartApp extends Command
{
    protected $signature = 'restart:app';
    protected $description = 'Restart the service';

    public function handle()
    {
        $commands = [
            ['php', 'artisan', 'down'],
            ['php', 'artisan', 'view:clear'],
            ['php', 'artisan', 'config:clear'],
            ['php', 'artisan', 'cache:clear'],
            ['php', 'artisan', 'route:clear'],
            ['php', 'artisan', 'event:clear'],
            ['php', 'artisan', 'optimize:clear'],
            ['php', 'artisan', 'clear-compiled'],
            ['php', 'artisan', 'up'],
        ];

        foreach ($commands as $command) {
            $process = Process::fromShellCommandline( implode(' ',$command), base_path());
            $process->run();

            if (!$process->isSuccessful()) {
                $this->error("❌ Failed: " . implode(' ', $command));
                Log::error("Failed Artisan command", [
                    'command' => implode(' ', $command),
                    'error' => $process->getErrorOutput()
                ]);
                throw new ProcessFailedException($process);
            } else {
                $this->info("✅ Success: " . implode(' ', $command));
            }
        }

        return 0;
    }
}
