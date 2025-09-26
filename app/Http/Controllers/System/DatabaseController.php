<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Config;

class DatabaseController extends Controller
{
    public function index()
    {
        $tables = $this->getTables();
        $migrations = $this->getMigrations();
        $seeders = $this->getSeeders();
        $dbConfig = $this->getDatabaseConfig();

        return view('system.database.index', compact('tables', 'migrations', 'seeders', 'dbConfig'));
    }

    public function tables()
    {
        $tables = $this->getTables();
        return view('system.database.tables', compact('tables'));
    }

    public function tableData(Request $request, $table)
    {
        try {
            $page = $request->get('page', 1);
            $perPage = $request->get('per_page', 10);
            $search = $request->get('search', '');
            $sortColumn = $request->get('sort', '');
            $sortDirection = $request->get('direction', 'asc');

            // Validate table name to prevent SQL injection
            if (!preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $table)) {
                return response()->json(['error' => 'Invalid table name'], 400);
            }

            // Check if table exists
            $tables = $this->getTables();
            $tableExists = collect($tables)->contains('name', $table);

            if (!$tableExists) {
                return response()->json(['error' => 'Table not found'], 404);
            }

            // Get table structure
            $columns = Schema::getColumnListing($table);
            $totalRows = DB::table($table)->count();

            // Build query
            $query = DB::table($table);

            // Apply search
            if ($search) {
                $query->where(function($q) use ($columns, $search) {
                    foreach ($columns as $column) {
                        $q->orWhere($column, 'like', "%{$search}%");
                    }
                });
            }

            // Apply sorting
            if ($sortColumn && in_array($sortColumn, $columns)) {
                $query->orderBy($sortColumn, $sortDirection);
            }

            // Get paginated data
            $data = $query->paginate($perPage, ['*'], 'page', $page);

            return response()->json([
                'data' => $data->items(),
                'columns' => $columns,
                'pagination' => [
                    'current_page' => $data->currentPage(),
                    'last_page' => $data->lastPage(),
                    'per_page' => $data->perPage(),
                    'total' => $data->total(),
                    'from' => $data->firstItem(),
                    'to' => $data->lastItem(),
                ],
                'table_name' => $table,
                'total_rows' => $totalRows
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch table data: ' . $e->getMessage()], 500);
        }
    }

    public function tableStructure(Request $request, $table)
    {
        try {
            // Validate table name
            if (!preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $table)) {
                return response()->json(['error' => 'Invalid table name'], 400);
            }

            // Check if table exists
            $tables = $this->getTables();
            $tableExists = collect($tables)->contains('name', $table);

            if (!$tableExists) {
                return response()->json(['error' => 'Table not found'], 404);
            }

            // Get detailed column information
            $columns = DB::select("DESCRIBE `{$table}`");

            // Get table indexes
            $indexes = DB::select("SHOW INDEX FROM `{$table}`");

            // Get table creation info
            $createTable = DB::select("SHOW CREATE TABLE `{$table}`");

            return response()->json([
                'table_name' => $table,
                'columns' => $columns,
                'indexes' => $indexes,
                'create_statement' => $createTable[0]->{'Create Table'} ?? null
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch table structure: ' . $e->getMessage()], 500);
        }
    }

    public function migrations()
    {
        $migrations = $this->getMigrations();
        return view('system.database.migrations', compact('migrations'));
    }

    public function seeders()
    {
        $seeders = $this->getSeeders();
        return view('system.database.seeders', compact('seeders'));
    }

    public function config()
    {
        $dbConfig = $this->getDatabaseConfig();
        $environments = ['local', 'production', 'staging', 'testing'];

        return view('system.database.config', compact('dbConfig', 'environments'));
    }

    public function updateConfig(Request $request)
    {
        $request->validate([
            'environment' => 'required|string',
            'db_connection' => 'required|string',
            'db_host' => 'required|string',
            'db_port' => 'required|string',
            'db_database' => 'required|string',
            'db_username' => 'required|string',
            'db_password' => 'nullable|string',
        ]);

        try {
            $envFile = base_path('.env');
            $envContent = File::get($envFile);

            $updates = [
                'APP_ENV' => $request->environment,
                'DB_CONNECTION' => $request->db_connection,
                'DB_HOST' => $request->db_host,
                'DB_PORT' => $request->db_port,
                'DB_DATABASE' => $request->db_database,
                'DB_USERNAME' => $request->db_username,
                'DB_PASSWORD' => $request->db_password,
            ];

            foreach ($updates as $key => $value) {
                $pattern = "/^{$key}=.*/m";
                $replacement = "{$key}={$value}";

                if (preg_match($pattern, $envContent)) {
                    $envContent = preg_replace($pattern, $replacement, $envContent);
                } else {
                    $envContent .= "\n{$replacement}";
                }
            }

            File::put($envFile, $envContent);

            return back()->with('success', 'Database configuration updated successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to update configuration: ' . $e->getMessage());
        }
    }

    public function migrate(Request $request)
    {
        try {
            $steps = $request->input('steps', 1);

            if ($request->has('fresh')) {
                Artisan::call('migrate:fresh', ['--force' => true]);
                $output = Artisan::output();
            } else {
                Artisan::call('migrate', ['--step' => $steps, '--force' => true]);
                $output = Artisan::output();
            }

            return back()->with('success', 'Migration completed successfully!')->with('output', $output);
        } catch (\Exception $e) {
            return back()->with('error', 'Migration failed: ' . $e->getMessage());
        }
    }

    public function rollback(Request $request)
    {
        try {
            $steps = $request->input('steps', 1);
            Artisan::call('migrate:rollback', ['--step' => $steps, '--force' => true]);
            $output = Artisan::output();

            return back()->with('success', 'Rollback completed successfully!')->with('output', $output);
        } catch (\Exception $e) {
            return back()->with('error', 'Rollback failed: ' . $e->getMessage());
        }
    }

    public function seed(Request $request)
    {
        try {
            $seeder = $request->input('seeder');

            if ($seeder) {
                Artisan::call('db:seed', ['--class' => $seeder, '--force' => true]);
            } else {
                Artisan::call('db:seed', ['--force' => true]);
            }

            $output = Artisan::output();
            return back()->with('success', 'Seeding completed successfully!')->with('output', $output);
        } catch (\Exception $e) {
            return back()->with('error', 'Seeding failed: ' . $e->getMessage());
        }
    }

    public function fresh(Request $request)
    {
        try {
            $withSeeding = $request->has('with_seeding');

            if ($withSeeding) {
                Artisan::call('migrate:fresh', ['--seed' => true, '--force' => true]);
            } else {
                Artisan::call('migrate:fresh', ['--force' => true]);
            }

            $output = Artisan::output();
            return back()->with('success', 'Fresh migration completed successfully!')->with('output', $output);
        } catch (\Exception $e) {
            return back()->with('error', 'Fresh migration failed: ' . $e->getMessage());
        }
    }

    public function migrateSelected(Request $request)
    {
        try {
            $migrations = $request->input('migrations', []);
            $withSeeding = $request->has('with_seeding');

            if (empty($migrations)) {
                return back()->with('error', 'No migrations selected!');
            }

            $output = '';
            $successCount = 0;

            foreach ($migrations as $migration) {
                try {
                    // Run specific migration
                    Artisan::call('migrate', [
                        '--path' => 'database/migrations/' . $migration,
                        '--force' => true
                    ]);
                    $output .= Artisan::output();
                    $successCount++;
                } catch (\Exception $e) {
                    $output .= "Error running {$migration}: " . $e->getMessage() . "\n";
                }
            }

            if ($withSeeding && $successCount > 0) {
                Artisan::call('db:seed', ['--force' => true]);
                $output .= "\n" . Artisan::output();
            }

            $message = "Successfully ran {$successCount} selected migration(s)!";
            if ($withSeeding) {
                $message .= " Seeding also completed.";
            }

            return back()->with('success', $message)->with('output', $output);
        } catch (\Exception $e) {
            return back()->with('error', 'Selected migration failed: ' . $e->getMessage());
        }
    }

    public function migrateSingle(Request $request)
    {
        try {
            $migration = $request->input('migration');

            if (!$migration) {
                return back()->with('error', 'No migration specified!');
            }

            Artisan::call('migrate', [
                '--path' => 'database/migrations/' . $migration,
                '--force' => true
            ]);

            $output = Artisan::output();
            return back()->with('success', "Migration {$migration} completed successfully!")->with('output', $output);
        } catch (\Exception $e) {
            return back()->with('error', 'Single migration failed: ' . $e->getMessage());
        }
    }

    public function rollbackSingle(Request $request)
    {
        try {
            $migration = $request->input('migration');

            if (!$migration) {
                return back()->with('error', 'No migration specified!');
            }

            // Get the migration batch number
            $migrationRecord = DB::table('migrations')
                ->where('migration', str_replace('.php', '', $migration))
                ->first();

            if (!$migrationRecord) {
                return back()->with('error', 'Migration not found in database!');
            }

            // Rollback the specific batch
            Artisan::call('migrate:rollback', [
                '--step' => 1,
                '--force' => true
            ]);

            $output = Artisan::output();
            return back()->with('success', "Migration {$migration} rolled back successfully!")->with('output', $output);
        } catch (\Exception $e) {
            return back()->with('error', 'Single rollback failed: ' . $e->getMessage());
        }
    }

    private function getTables()
    {
        try {
            $tables = DB::select('SHOW TABLES');
            $tableData = [];

            foreach ($tables as $table) {
                $tableName = array_values((array) $table)[0];
                $columns = Schema::getColumnListing($tableName);
                $rowCount = DB::table($tableName)->count();

                $tableData[] = [
                    'name' => $tableName,
                    'columns' => $columns,
                    'row_count' => $rowCount,
                    'size' => $this->getTableSize($tableName),
                ];
            }

            return $tableData;
        } catch (\Exception $e) {
            return [];
        }
    }

    private function getTableSize($tableName)
    {
        try {
            $result = DB::select("
                SELECT
                    ROUND(((data_length + index_length) / 1024 / 1024), 2) AS 'Size in MB'
                FROM information_schema.TABLES
                WHERE table_schema = DATABASE()
                AND table_name = ?
            ", [$tableName]);

            return $result[0]->{'Size in MB'} ?? '0.00';
        } catch (\Exception $e) {
            return '0.00';
        }
    }

    private function getMigrations()
    {
        try {
            $migrationFiles = File::files(database_path('migrations'));
            $migrations = [];

            foreach ($migrationFiles as $file) {
                $migrations[] = [
                    'filename' => $file->getFilename(),
                    'path' => $file->getPathname(),
                    'size' => $file->getSize(),
                    'modified' => date('Y-m-d H:i:s', $file->getMTime()),
                ];
            }

            // Check which migrations have been run
            $runMigrations = DB::table('migrations')->pluck('migration')->toArray();

            foreach ($migrations as &$migration) {
                $migrationName = str_replace('.php', '', $migration['filename']);
                $migration['status'] = in_array($migrationName, $runMigrations) ? 'run' : 'pending';
            }

            return $migrations;
        } catch (\Exception $e) {
            return [];
        }
    }

    private function getSeeders()
    {
        try {
            $seederFiles = File::files(database_path('seeders'));
            $seeders = [];

            foreach ($seederFiles as $file) {
                $seeders[] = [
                    'filename' => $file->getFilename(),
                    'class_name' => str_replace('.php', '', $file->getFilename()),
                    'path' => $file->getPathname(),
                    'size' => $file->getSize(),
                    'modified' => date('Y-m-d H:i:s', $file->getMTime()),
                ];
            }

            return $seeders;
        } catch (\Exception $e) {
            return [];
        }
    }

    private function getDatabaseConfig()
    {
        return [
            'connection' => config('database.default'),
            'host' => config('database.connections.' . config('database.default') . '.host'),
            'port' => config('database.connections.' . config('database.default') . '.port'),
            'database' => config('database.connections.' . config('database.default') . '.database'),
            'username' => config('database.connections.' . config('database.default') . '.username'),
            'environment' => config('app.env'),
        ];
    }
}
