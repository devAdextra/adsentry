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
        Schema::table('movements', function (Blueprint $table) {
            // Indice composito per le colonne più utilizzate nelle query
            $table->index(['db', 'user_id', 'macro', 'micro', 'nano', 'extra'], 'idx_filters');
            
            // Indice per timeActionOpen e timeActionClick
            $table->index(['timeActionOpen', 'timeActionClick'], 'idx_user_action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('movements', function (Blueprint $table) {
            $table->dropIndex('idx_filters');
            $table->dropIndex('idx_user_action');
        });
    }
};
