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
        Url::where('status->read', 1)->each(
            function (Url $url) {
                $status = $url->status;
                $status['not_bookmarked'] = 1;
                $url->status = $status;
                $url->save();
            }
        );

        Url::where('status->bookmarked', 1)->each(
            function (Url $url) {
                $status = $url->status;
                $status['read'] = 1;
                $url->status = $status;
                $url->save();
            }
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Url::where('status->not_bookmarked', 1)->each(
            function (Url $url) {
                $status = $url->status;
                unset($status['not_bookmarked']);
                $url->status = $status;
                $url->save();
            }
        );

        Url::where('status->bookmarked', 1)->each(
            function (Url $url) {
                $status = $url->status;
                unset($status['read']);
                $url->status = $status;
                $url->save();
            }
        );
    }
};
