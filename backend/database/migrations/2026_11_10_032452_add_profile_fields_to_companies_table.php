
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->string('cover_image')->nullable()->after('logo');
            $table->string('tagline')->nullable()->after('name');
            $table->unsignedSmallInteger('founded_year')->nullable();
            $table->string('employee_count')->nullable();
            $table->json('gallery')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn([
                'cover_image',
                'tagline',
                'founded_year',
                'employee_count',
                'gallery',
            ]);
        });
    }
};
