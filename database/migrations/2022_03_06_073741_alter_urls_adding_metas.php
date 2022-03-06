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
        Schema::table('urls', function (Blueprint $table) {
            $table->string('title')->after('status')->nullable();

            $table->json('meta_html')->after('title')->nullable();

            $table->bigInteger('chat_id', false, true)->after('message_id')->nullable();

            $table->timestamp('read_at')->after('chat_id')->nullable();
        });

        Url::with('telegramUpdate')
            ->whereNull('chat_id')
            ->each(fn(Url $url) => $url->update(['chat_id' => $url->telegramUpdate->data('message.chat.id')]));
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('urls', function (Blueprint $table) {
            $table->dropColumn([
                'title',
                'meta_html',
                'read_at',
                'chat_id',
            ]);
        });
    }
};
