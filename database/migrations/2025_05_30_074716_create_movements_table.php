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
        Schema::create('movements', function (Blueprint $table) {
            $table->id();
            $table->string('db');
            $table->unsignedBigInteger('user_id');
            $table->string('macro');
            $table->string('micro');
            $table->string('nano');
            $table->string('extra')->nullable();
            $table->timestamp('timeActionOpen')->nullable();
            $table->timestamp('timeActionClick')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movements');
    }
};
