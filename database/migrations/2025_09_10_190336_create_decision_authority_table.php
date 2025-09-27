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
        Schema::create('decision_authority', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('board_id')->comment('Reference to wp_posts.ID for board post type');
            $table->string('title');
            $table->unsignedBigInteger('type_term_id')->comment('Reference to wp_terms.term_id for type taxonomy');
            $table->date('start_date');
            $table->date('end_date');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            // Add indexes for better query performance
            $table->index('board_id');
            $table->index('type_term_id');
            
            // Add foreign key constraints
            $table->foreign('board_id')
                ->references('ID')
                ->on('posts')
                ->onDelete('restrict');
                
            $table->foreign('type_term_id')
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
        Schema::dropIfExists('decision_authority');
    }
};
