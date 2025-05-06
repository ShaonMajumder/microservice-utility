<?php

namespace ShaonMajumder\MicroserviceUtility\Services;

class HealthCheckService
{
    protected array $checks = [];

    public function register(string $name, callable $callback): void
    {
        $this->checks[$name] = $callback;
    }

    public function run(): array
    {
        $results = [];
        foreach ($this->checks as $name => $callback) {
            try {
                $result = $callback();
                $results[$name] = [
                    'status' => $result ? 'up' : 'down',
                    'message' => null
                ];
            } catch (\Throwable $e) {
                $results[$name] = [
                    'status' => 'error',
                    'message' => $e->getMessage()
                ];
            }
        }
        return $results;
    }
}