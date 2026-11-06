
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('actor_id')
                ->constrained('users');

            $table->string('action', 100);
            $table->text('description')->nullable();

            $table->string('subject_type')->nullable();
            $table->unsignedBigInteger('subject_id')->nullable();

            $table->string('ip_address', 45)->nullable();

            $table->timestamp('created_at')->useCurrent();

            $table->index(
                ['subject_type', 'subject_id'],
                'activity_logs_subject_index'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
