<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DecisionAuthorityTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all boards from WordPress
        $boards = DB::table('posts')
            ->where('post_type', 'board')
            ->get(['ID', 'post_title']);

        // Get existing type terms from the type taxonomy
        $typeTerms = DB::table('terms')
            ->join('term_taxonomy', 'terms.term_id', '=', 'term_taxonomy.term_id')
            ->where('term_taxonomy.taxonomy', 'type')
            ->select('terms.term_id', 'terms.name')
            ->get();

        if ($typeTerms->isEmpty()) {
            $this->command->warn('No type terms found in the database. Please create some type terms first.');
            return;
        }

        // Convert to array for easier random selection
        $typeTermsArray = $typeTerms->toArray();

        // Only proceed if we have boards and type terms
        if ($boards->isNotEmpty() && !empty($typeTermsArray)) {
            $authorities = [];
            
            foreach ($boards as $board) {
                // Create 1-2 decision authorities per board
                $numAuthorities = rand(1, 2);
                
                for ($i = 0; $i < $numAuthorities; $i++) {
                    // Randomly select a type term
                    $randomTypeTerm = $typeTermsArray[array_rand($typeTermsArray)];
                    $typeName = $randomTypeTerm->name;
                    $typeTermId = $randomTypeTerm->term_id;
                    
                    $title = $i === 0 ? 
                        "Ordinarie {$typeName}" : 
                        "Tillfällig {$typeName}";

                    // Set different date ranges for regular vs temporary authorities
                    $startDate = $i === 0 ? '2024-01-01' : '2024-06-01';
                    $endDate = $i === 0 ? '2026-12-31' : '2025-05-31';

                    $authorities[] = [
                        'board_id' => $board->ID,
                        'title' => $title,
                        'type_term_id' => $typeTermId,
                        'start_date' => $startDate,
                        'end_date' => $endDate,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }

            // Insert all decision authorities
            if (!empty($authorities)) {
                DB::table('decision_authority')->insert($authorities);
                $this->command->info('Decision authorities seeded successfully!');
                $this->command->info('Used ' . count($typeTerms) . ' existing type terms: ' . $typeTerms->pluck('name')->implode(', '));
            }
        } else {
            if ($boards->isEmpty()) {
                $this->command->warn('No boards found in the database. Please create some first.');
            }
            if (empty($typeTermsArray)) {
                $this->command->warn('No type terms found in the database.');
            }
        }
    }
}