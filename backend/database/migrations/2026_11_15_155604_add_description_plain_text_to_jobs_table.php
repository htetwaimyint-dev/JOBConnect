
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('jobs', function (Blueprint $table) {
            $table->longText('description_plain_text')
                ->nullable()
                ->after('description');
        });

        // Backfill existing jobs.
        DB::table('jobs')
            ->select('id', 'description')
            ->orderBy('id')
            ->chunkById(200, function ($jobs) {
                foreach ($jobs as $job) {
                    $html = $job->description ?? '';

                    // Preserve spaces between HTML blocks before removing tags.
                    $html = preg_replace(
                        '/<\/(p|div|li|h[1-6]|blockquote)>|<br\s*\/?>/i',
                        ' ',
                        $html
                    );

                    $plainText = strip_tags($html);

                    $plainText = html_entity_decode(
                        $plainText,
                        ENT_QUOTES | ENT_HTML5,
                        'UTF-8'
                    );

                    $plainText = preg_replace('/\s+/u', ' ', $plainText);

                    DB::table('jobs')
                        ->where('id', $job->id)
                        ->update([
                            'description_plain_text' => trim($plainText),
                        ]);
                }
            });
    }

    public function down(): void
    {
        Schema::table('jobs', function (Blueprint $table) {
            $table->dropColumn('description_plain_text');
        });
    }
};
