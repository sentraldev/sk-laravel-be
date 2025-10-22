<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1) Add a temporary string column to hold converted values
        Schema::table('shop_locations', function (Blueprint $table) {
            $table->string('opening_hours_tmp', 8)->nullable()->after('opening_hours');
        });

        // 2) Copy data from JSON column to the temp string column
        DB::table('shop_locations')
            ->orderBy('id')
            ->select('id', 'opening_hours')
            ->chunkById(500, function ($rows) {
                foreach ($rows as $row) {
                    $val = null;
                    if (!is_null($row->opening_hours)) {
                        $decoded = json_decode($row->opening_hours, true);
                        if (json_last_error() === JSON_ERROR_NONE) {
                            if (is_string($decoded)) {
                                $val = $decoded;
                            } elseif (is_array($decoded)) {
                                // try common keys if it was stored as an array/object
                                $val = $decoded['time'] ?? $decoded['value'] ?? $decoded['opening_hours'] ?? null;
                                if (is_array($val)) {
                                    $val = null;
                                }
                            } elseif (is_scalar($decoded)) {
                                $val = (string) $decoded;
                            }
                        } else {
                            // Not valid JSON, treat as plain string
                            $val = (string) $row->opening_hours;
                        }

                        if ($val !== null) {
                            // Truncate to HH:MM if needed
                            if (preg_match('/^(\d{1,2}):(\d{2})/', $val, $m)) {
                                $val = sprintf('%02d:%02d', (int) $m[1], (int) $m[2]);
                            } else {
                                // Fallback: leave as-is but cut to 8 chars
                                $val = substr($val, 0, 8);
                            }
                        }
                    }

                    DB::table('shop_locations')
                        ->where('id', $row->id)
                        ->update(['opening_hours_tmp' => $val]);
                }
            });

        // 3) Drop the old JSON column
        Schema::table('shop_locations', function (Blueprint $table) {
            $table->dropColumn('opening_hours');
        });

        // 4) Create the new string column with the original name
        Schema::table('shop_locations', function (Blueprint $table) {
            $table->string('opening_hours', 8)->nullable();
        });

        // 5) Copy back the data
        DB::table('shop_locations')->update([
            'opening_hours' => DB::raw('opening_hours_tmp'),
        ]);

        // 6) Drop the temp column
        Schema::table('shop_locations', function (Blueprint $table) {
            $table->dropColumn('opening_hours_tmp');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 1) Add a temp JSON column
        Schema::table('shop_locations', function (Blueprint $table) {
            $table->json('opening_hours_tmp')->nullable()->after('opening_hours');
        });

        // 2) Copy string to JSON (as a raw JSON string)
        DB::table('shop_locations')
            ->orderBy('id')
            ->select('id', 'opening_hours')
            ->chunkById(500, function ($rows) {
                foreach ($rows as $row) {
                    $jsonVal = $row->opening_hours !== null
                        ? json_encode((string) $row->opening_hours)
                        : null;

                    DB::table('shop_locations')
                        ->where('id', $row->id)
                        ->update(['opening_hours_tmp' => DB::raw($jsonVal === null ? 'NULL' : "'" . str_replace("'", "''", $jsonVal) . "'" )]);
                }
            });

        // 3) Drop the string column
        Schema::table('shop_locations', function (Blueprint $table) {
            $table->dropColumn('opening_hours');
        });

        // 4) Recreate JSON opening_hours
        Schema::table('shop_locations', function (Blueprint $table) {
            $table->json('opening_hours')->nullable();
        });

        // 5) Copy back from temp
        DB::table('shop_locations')->update([
            'opening_hours' => DB::raw('opening_hours_tmp'),
        ]);

        // 6) Drop temp
        Schema::table('shop_locations', function (Blueprint $table) {
            $table->dropColumn('opening_hours_tmp');
        });
    }
};
