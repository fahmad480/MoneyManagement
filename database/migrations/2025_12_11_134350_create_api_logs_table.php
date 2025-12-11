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
        Schema::create('api_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('api_key_id')->constrained()->onDelete('cascade');
            $table->string('endpoint');
            $table->string('method', 10);
            $table->ipAddress('ip_address');
            $table->integer('status_code');
            $table->text('request_body')->nullable();
            $table->text('response_body')->nullable();
            $table->integer('response_time')->comment('in milliseconds');
            $table->timestamp('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('api_logs');
    }
};
