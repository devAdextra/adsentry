<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('downloads', function (Blueprint $table) {
            $table->id();
            $table->string('filename');
            $table->string('path');
            $table->string('status')->default('processing'); // processing, completed, failed
            $table->json('filters')->nullable();
            $table->string('user')->nullable(); // placeholder per ora
            $table->integer('total_records')->default(0);
            $table->timestamp('expires_at');
            $table->timestamps();
            
            $table->index('status');
            $table->index('created_at');
            $table->index('expires_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('downloads');
    }
}; 