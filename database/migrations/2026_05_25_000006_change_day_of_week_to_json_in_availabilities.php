<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add a plain therapist_id index so the FK constraint is satisfied
        // after we drop the compound index (may already exist from a partial run)
        if (! $this->indexExists('availabilities', 'availabilities_therapist_id_simple')) {
            Schema::table('availabilities', function (Blueprint $table) {
                $table->index('therapist_id', 'availabilities_therapist_id_simple');
            });
        }

        // Drop the unique constraint if it exists
        if ($this->indexExists('availabilities', 'availability_unique_range')) {
            Schema::table('availabilities', function (Blueprint $table) {
                $table->dropUnique('availability_unique_range');
            });
        }

        // Drop the compound index (FK is now covered by the simple index above)
        if ($this->indexExists('availabilities', 'availabilities_therapist_id_day_of_week_index')) {
            Schema::table('availabilities', function (Blueprint $table) {
                $table->dropIndex('availabilities_therapist_id_day_of_week_index');
            });
        }

        // Add a temporary JSON column alongside the old one
        Schema::table('availabilities', function (Blueprint $table) {
            $table->json('day_of_week_json')->nullable()->after('day_of_week');
        });

        // Migrate existing single-day integers into one-element JSON arrays
        DB::statement('UPDATE availabilities SET day_of_week_json = JSON_ARRAY(day_of_week)');

        // Drop the old integer column and rename the new one
        Schema::table('availabilities', function (Blueprint $table) {
            $table->dropColumn('day_of_week');
        });

        Schema::table('availabilities', function (Blueprint $table) {
            $table->renameColumn('day_of_week_json', 'day_of_week');
        });
    }

    public function down(): void
    {
        Schema::table('availabilities', function (Blueprint $table) {
            $table->unsignedTinyInteger('day_of_week_int')->nullable()->after('day_of_week');
        });

        DB::statement("UPDATE availabilities SET day_of_week_int = JSON_UNQUOTE(JSON_EXTRACT(day_of_week, '$[0]'))");

        Schema::table('availabilities', function (Blueprint $table) {
            $table->dropColumn('day_of_week');
        });

        Schema::table('availabilities', function (Blueprint $table) {
            $table->renameColumn('day_of_week_int', 'day_of_week');
        });

        Schema::table('availabilities', function (Blueprint $table) {
            $table->index(['therapist_id', 'day_of_week']);
        });

        if ($this->indexExists('availabilities', 'availabilities_therapist_id_simple')) {
            Schema::table('availabilities', function (Blueprint $table) {
                $table->dropIndex('availabilities_therapist_id_simple');
            });
        }
    }

    private function indexExists(string $table, string $indexName): bool
    {
        $indexes = DB::select("SHOW INDEX FROM `{$table}` WHERE Key_name = ?", [$indexName]);
        return count($indexes) > 0;
    }
};
