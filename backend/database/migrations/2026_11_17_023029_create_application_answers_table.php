
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('application_answers', function (Blueprint $table) {
            $table->id();

            $table->foreignId('application_id')
                ->constrained('applications')
                ->cascadeOnDelete();

            $table->foreignId('job_question_id')
                ->constrained('job_questions')
                ->restrictOnDelete();

            $table->text('answer');

            $table->timestamps();

            $table->unique(
                ['application_id', 'job_question_id'],
                'application_answers_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('application_answers');
    }
};
