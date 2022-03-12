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
        Schema::create('annotations', function (Blueprint $table) {
            $table->id();

            $table->string('status');

            $table->string('note')->nullable();

            $table->foreignIdFor(Url::class)
                ->constrained()
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

            $table->integer(column: 'message_id', unsigned: true)->nullable();

            $table->bigInteger(column: 'chat_id', unsigned: true)->nullable();

            $table->timestamps();

            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('annotations');
    }
};
