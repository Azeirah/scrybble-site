<?php

namespace Laura\PureFn;

use Closure;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use ReflectionClass;

class PureFnServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Register SQLite connection if not exists
        if (!Config::has('database.connections.purefn')) {
            Config::set('database.connections.purefn', [
                'driver' => 'sqlite',
                'database' => storage_path('purefn.sqlite'),
                'prefix' => '',
                'foreign_key_constraints' => true,
            ]);
        }
    }

    public function boot(): void
    {
        $this->setupDB();

        // Extend Laravel's container to wrap classes with PureFn attributes
        $this->app->beforeResolving(function ($abstract, $parameters, $app) {
            // Skip if it's a primitive type or closure
            if (!is_string($abstract) || !class_exists($abstract)) {
                return;
            }

            $reflection = new ReflectionClass($abstract);

            // Check if any methods have the PureFn attribute
            $hasPureFn = false;
            foreach ($reflection->getMethods() as $method) {
                if (!empty($method->getAttributes(PureFn::class))) {
                    $hasPureFn = true;
                    break;
                }
            }

            if ($hasPureFn) {
                $this->app->extend($abstract, function ($instance, $app) use ($reflection) {
                    foreach ($reflection->getMethods() as $method) {
                        $attributes = $method->getAttributes(PureFn::class);
                        if (empty($attributes)) {
                            continue;
                        }
                        Log::info($attributes[0]->getName());

                        $methodName = $method->getName();
                        $pureFn = $attributes[0]->newInstance();

                        // Store original implementation
                        $originalMethod = Closure::fromCallable([$instance, $methodName]);

                        // Bind new implementation that directly uses the stored original
                        $instance->$methodName = function (...$args) use ($originalMethod, $pureFn) {
                            return $pureFn->wrap(
                                $originalMethod,  // Pass the original directly
                                $args
                            );
                        };
                    }

                    return $instance;
                });
            }
        });
    }

    /**
     * @return void
     */
    public function setupDB(): void
    {
        $dbPath = storage_path('purefn.sqlite');

        // Create database file if it doesn't exist
        if (!file_exists($dbPath)) {
            touch($dbPath);

            // Create tables directly using raw SQL
            DB::connection('purefn')->getPdo()->exec(<<<SQL
                CREATE TABLE IF NOT EXISTS functions (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    class TEXT,
                    method TEXT,
                    file TEXT,
                    line INTEGER,
                    UNIQUE(class, method)
                );

                CREATE INDEX IF NOT EXISTS idx_functions_location
                ON functions(file, line);

                CREATE TABLE IF NOT EXISTS invocations (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    function_id INTEGER,
                    timestamp DATETIME,
                    input_hash TEXT,
                    output_hash TEXT,
                    FOREIGN KEY(function_id) REFERENCES functions(id) ON DELETE CASCADE
                );

                CREATE INDEX IF NOT EXISTS idx_invocations_hashes
                ON invocations(function_id, input_hash, output_hash);

                CREATE TABLE IF NOT EXISTS values (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    invocation_id INTEGER,
                    is_input INTEGER,
                    value TEXT,
                    type TEXT,
                    FOREIGN KEY(invocation_id) REFERENCES invocations(id) ON DELETE CASCADE
                );

                CREATE INDEX IF NOT EXISTS idx_values_invocation
                ON values(invocation_id, is_input);
            SQL
            );
        }
    }
}
