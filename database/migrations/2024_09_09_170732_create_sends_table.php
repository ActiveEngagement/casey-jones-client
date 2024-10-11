<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sends', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid');
            $table->index('uuid');
            $table->bigInteger('app_id')->unsigned();
            $table->bigInteger('instance_id')->unsigned();
            $table->integer('campaign_id')->unsigned();
            $table->integer('template_id')->unsigned()->nullable();
            $table->string('name');
            $table->enum('status', ['draft', 'scheduled', 'queued', 'active', 'delivered', 'failed']);
            $table->string('subject')->nullable();
            $table->mediumText('html')->nullable();
            $table->mediumText('text')->nullable();
            $table->json('folder')->nullable();
            $table->string('from_address')->nullable();
            $table->string('from_name')->nullable();
            $table->string('reply_to_address')->nullable();
            $table->string('reply_to_name')->nullable();
            $table->json('data_variables')->nullable();
            $table->json('meta')->nullable();
            $table->integer('mailingid')->unsigned()->nullable();
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('failed_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sends');
    }
};
