<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // @todo status !
        Schema::create('urls', function (Blueprint $table) {
            $table->id();

            $table->string('scheme'); // : "https"

            $table->string('user_info'); // : "user:password"

            $table->string('host'); // : "arcolinux.com"

            $table->integer(column: 'port', unsigned: true)->nullable(); // : 80

            $table->string('path'); // : "/use-our-knowledge/"

            $table->string('query'); // : "key=value"

            $table->string('fragment'); // : "part2"

            $table->timestamps();

            $table->unique(['host', 'path']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('urls');
    }
};
