
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('email_logs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('recipient_id')
                ->constrained('users');

            $table->string('related_type')->nullable();
            $table->unsignedBigInteger('related_id')->nullable();

            $table->string('recipient_email');
            $table->string('subject');
            $table->string('email_type', 100);

            $table->enum('status', [
                'sent',
                'failed',
            ]);

            $table->text('error_message')->nullable();
            $table->timestamp('sent_at')->nullable();

            $table->timestamps();

            $table->index(
                ['related_type', 'related_id'],
                'email_logs_related_index'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('email_logs');
    }
};
