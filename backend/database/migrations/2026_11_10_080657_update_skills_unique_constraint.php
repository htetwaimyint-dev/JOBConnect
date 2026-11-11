
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('skills', function (Blueprint $table) {
            $table->dropUnique(['name']);

            $table->unique(
                ['job_seeker_id', 'name'],
                'skills_job_seeker_id_name_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::table('skills', function (Blueprint $table) {
            $table->dropUnique(
                'skills_job_seeker_id_name_unique'
            );

            $table->unique('name');
        });
    }
};
