
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_recommendations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained('users');

            $table->foreignId('job_id')
                ->constrained('jobs');

            $table->foreignId('analysis_id')
                ->nullable()
                ->constrained('ai_analyses');

            $table->decimal('match_score', 5, 2);

            $table->text('reason')->nullable();

            $table->boolean('is_viewed')->default(false);

            $table->timestamps();

            $table->unique(
                ['user_id', 'job_id'],
                'ai_recommendations_user_job_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_recommendations');
    }
};
