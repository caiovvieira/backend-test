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
        Schema::create('redirect_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('redirect_id');
            $table->foreign('redirect_id')->references('id')->on('redirects');
            $table->ipAddress('request_ip');
            $table->text('request_user_agent');
            $table->text('request_header')->nullable(true);
            $table->json('request_query_params')->nullable(true);
            $table->text('date_time_acess');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('redirect_logs');
    }
};
