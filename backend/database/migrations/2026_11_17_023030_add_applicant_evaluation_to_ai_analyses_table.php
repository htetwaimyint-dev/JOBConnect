
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{

    public function up(): void
    {
        Schema::table('ai_analyses', function (Blueprint $table) {
            $table->foreignId('application_id')
                ->nullable()
                ->after('job_id')
                ->constrained('applications')
                ->restrictOnDelete();

            $table->foreignId('requested_by')
                ->nullable()
                ->after('application_id')
                ->constrained('users')
                ->restrictOnDelete();
        });

        DB::statement("
        ALTER TABLE ai_analyses
        MODIFY analysis_type ENUM(
            'job_matching',
            'job_description',
            'applicant_evaluation'
        ) NOT NULL
    ");
    }

    public function down(): void
    {
        DB::statement("
        ALTER TABLE ai_analyses
        MODIFY analysis_type ENUM(
            'job_matching',
            'cover_letter',
            'job_description',
            'applicant_evaluation'
        ) NOT NULL
    ");

        Schema::table('ai_analyses', function (Blueprint $table) {
            $table->dropConstrainedForeignId('requested_by');
            $table->dropConstrainedForeignId('application_id');
        });
    }
};
