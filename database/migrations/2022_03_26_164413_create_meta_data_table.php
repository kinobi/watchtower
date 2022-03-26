<?php

use App\Models\Url;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('meta_data', function (Blueprint $table) {
            $table->id();

            $table->string('provider');

            $table->json('meta')->nullable();

            $table->foreignIdFor(Url::class)
                ->unique()
                ->constrained()
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

            $table->timestamps();
        });

        Url::whereNotNull('meta_html')
            ->get(['id', 'meta_html'])
            ->each(fn(Url $url) => $url->metaData()->create(['provider' => 'raindrop', 'meta' => $url->meta_html]));
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('meta_data');
    }
};
