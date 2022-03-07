<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration {
    private const OLD_INDEX = ['host', 'path'];
    private const NEW_INDEX = ['host', 'path', 'query'];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('urls', function (Blueprint $table) {
            $table->dropUnique(self::OLD_INDEX);
            $table->unique(self::NEW_INDEX);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('urls', function (Blueprint $table) {
            $table->dropUnique(self::NEW_INDEX);
            $table->unique(self::OLD_INDEX);
        });
    }
};
