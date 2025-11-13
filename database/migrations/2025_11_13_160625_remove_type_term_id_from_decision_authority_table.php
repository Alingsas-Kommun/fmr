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
        Schema::table('decision_authority', function (Blueprint $table) {
            // Drop foreign key constraint first
            $table->dropForeign(['type_term_id']);
            
            // Drop index
            $table->dropIndex(['type_term_id']);
            
            // Drop column
            $table->dropColumn('type_term_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('decision_authority', function (Blueprint $table) {
            // Recreate column
            $table->unsignedBigInteger('type_term_id')->nullable()->comment('Reference to wp_terms.term_id for type taxonomy')->after('title');
            
            // Recreate index
            $table->index('type_term_id');
            
            // Recreate foreign key constraint
            $table->foreign('type_term_id')
                ->references('term_id')
                ->on('terms')
                ->onDelete('set null');
        });
    }
};

