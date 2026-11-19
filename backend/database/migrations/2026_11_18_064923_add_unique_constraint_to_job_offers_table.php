<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('job_offers', function (Blueprint $table) {
            $table->unique(
                ['company_id', 'job_id', 'job_seeker_id'],
                'job_offers_company_job_seeker_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::table('job_offers', function (Blueprint $table) {
            $table->dropUnique(
                'job_offers_company_job_seeker_unique'
            );
        });
    }
};
