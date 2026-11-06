
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jobs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('company_id')
                ->constrained('companies');

            $table->foreignId('category_id')
                ->nullable()
                ->constrained('categories');

            $table->string('title');
            $table->longText('description');
            $table->longText('requirements')->nullable();
            $table->longText('responsibilities')->nullable();

            $table->enum('employment_type', [
                'full_time',
                'part_time',
                'contract',
                'internship',
            ]);

            $table->enum('workplace_type', [
                'onsite',
                'remote',
                'hybrid',
            ]);

            $table->string('location')->nullable();

            $table->decimal('salary_min', 12, 2)->nullable();
            $table->decimal('salary_max', 12, 2)->nullable();

            $table->integer('vacancies')->default(1);
            $table->date('deadline')->nullable();

            $table->enum('status', [
                'draft',
                'pending',
                'published',
                'rejected',
                'closed',
            ])->default('pending');

            $table->foreignId('approved_by')
                ->nullable()
                ->constrained('users');

            $table->timestamp('approved_at')->nullable();
            $table->text('rejection_reason')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jobs');
    }
};
