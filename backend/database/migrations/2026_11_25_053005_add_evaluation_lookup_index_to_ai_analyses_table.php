<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ai_analyses', function (Blueprint $table) {
            $table->index(
                [
                    'application_id',
                    'analysis_type',
                    'input_hash',
                    'status',
                ],
                'ai_analyses_evaluation_lookup_index'
            );
        });
    }

    public function down(): void
    {
        Schema::table('ai_analyses', function (Blueprint $table) {
            $table->dropIndex(
                'ai_analyses_evaluation_lookup_index'
            );
        });
    }
};
