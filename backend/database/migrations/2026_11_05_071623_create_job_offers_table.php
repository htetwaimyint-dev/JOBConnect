
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_offers', function (Blueprint $table) {
            $table->id();

            $table->foreignId('job_id')
                ->constrained('jobs');

            $table->foreignId('job_seeker_id')
                ->constrained('users');

            $table->foreignId('company_id')
                ->constrained('companies');

            $table->decimal('salary_offered', 12, 2)->nullable();
            $table->text('offer_details')->nullable();
            $table->timestamp('expires_at')->nullable();

            $table->enum('status', [
                'pending',
                'accepted',
                'rejected',
                'expired',
                'withdrawn',
            ])->default('pending');

            $table->timestamp('responded_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_offers');
    }
};
