<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateNewsletterSubscriptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('newsletter_subscriptions', function (Blueprint $table) {
            $table->json('options')->nullable();
            $table->string('token')->nullable();
            $table->dropForeign(['story_id']);
            $table->dropColumn('story_id');
            $table->dropColumn('email_verified_at');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('newsletter_subscriptions', function (Blueprint $table) {
            $table->dropColumn('options');
            $table->dropColumn('token');
        });
    }
}
