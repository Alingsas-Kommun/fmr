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
            $table->unsignedBigInteger('decision_authority_id')->comment('Reference to decision_authority.id');
            $table->unsignedBigInteger('person_id')->comment('Reference to wp_posts.ID for person post type');
            $table->unsignedBigInteger('role_term_id')->comment('Reference to wp_terms.term_id for role taxonomy');
            $table->date('period_start')->comment('Start date of the assignment');
            $table->date('period_end')->comment('End date of the assignment');
            $table->timestamps();
            
            // Add indexes for better query performance
            $table->index('decision_authority_id');
            $table->index('person_id');
            $table->index('role_term_id');
            $table->index(['period_start', 'period_end']);
            
            // Add foreign key constraints
            $table->foreign('decision_authority_id')
                ->references('id')
                ->on('decision_authority')
                ->onDelete('cascade');
                
            $table->foreign('person_id')
                ->references('ID')
                ->on('posts')
                ->onDelete('restrict');
                
            $table->foreign('role_term_id')
                ->references('term_id')
                ->on('terms')
                ->onDelete('restrict');
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
