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
        Schema::create('assignments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('board_id')->comment('Reference to wp_posts.ID for board post type');
            $table->unsignedBigInteger('person_id')->comment('Reference to wp_posts.ID for person post type');
            $table->string('role')->comment('The role of the person in the board (e.g. Ersättare, Ordförande)');
            $table->date('period_start')->comment('Start date of the assignment');
            $table->date('period_end')->comment('End date of the assignment');
            $table->timestamps();
            
            // Add indexes for better query performance
            $table->index('board_id');
            $table->index('person_id');
            $table->index(['period_start', 'period_end']);
            
            // Add foreign key constraints to wp_posts table
            $table->foreign('board_id')
                ->references('ID')
                ->on('posts')
                ->onDelete('cascade');
                
            $table->foreign('person_id')
                ->references('ID')
                ->on('posts')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assignments');
    }
};
