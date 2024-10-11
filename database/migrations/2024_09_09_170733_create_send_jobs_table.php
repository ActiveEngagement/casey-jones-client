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
        Schema::create('send_jobs', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid');
            $table->index('uuid');
            $table->bigInteger('send_id')->unsigned();
            $table->foreign('send_id')->references('id')->on('sends')->cascadeOnDelete()->cascadeOnUpdate();
            $table->integer('status_code')->nullable();
            $table->boolean('failed')->nullable();
            $table->integer('mailingid')->nullable();
            $table->mediumText('response')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('send_requests');
    }
};
