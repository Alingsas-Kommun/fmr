<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AssignmentsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all decision authorities
        $authorities = DB::table('decision_authority')->get();
        
        // Get all persons from WordPress
        $persons = DB::table('posts')
            ->where('post_type', 'person')
            ->get(['ID', 'post_title']);

        // Get existing role terms from the role taxonomy
        $roleTerms = DB::table('terms')
            ->join('term_taxonomy', 'terms.term_id', '=', 'term_taxonomy.term_id')
            ->where('term_taxonomy.taxonomy', 'role')
            ->select('terms.term_id', 'terms.name')
            ->get();

        if ($roleTerms->isEmpty()) {
            $this->command->warn('No role terms found in the database. Please create some role terms first.');
            return;
        }

        // Convert to array for easier random selection
        $roleTermsArray = $roleTerms->toArray();

        // Only proceed if we have decision authorities, persons, and role terms
        if ($authorities->isNotEmpty() && $persons->isNotEmpty() && !empty($roleTermsArray)) {
            $assignments = [];
            
            // Create some example assignments
            foreach ($authorities as $authority) {
                // Convert persons collection to array and shuffle it
                $availablePersons = $persons->toArray();
                shuffle($availablePersons);
                
                // Take up to 5 persons
                $numAssignments = min(5, count($availablePersons));
                $assignedPersons = array_slice($availablePersons, 0, $numAssignments);
                
                foreach ($assignedPersons as $index => $person) {
                    // Randomly select a role term
                    $randomRoleTerm = $roleTermsArray[array_rand($roleTermsArray)];
                    $roleTermId = $randomRoleTerm->term_id;
                    
                    $assignments[] = [
                        'decision_authority_id' => $authority->id,
                        'person_id' => $person->ID,
                        'role_term_id' => $roleTermId,
                        'period_start' => $authority->start_date,
                        'period_end' => $authority->end_date,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }

            // Insert all assignments
            if (!empty($assignments)) {
                DB::table('assignments')->insert($assignments);
                $this->command->info('Assignments seeded successfully!');
                $this->command->info('Used ' . count($roleTerms) . ' existing role terms: ' . $roleTerms->pluck('name')->implode(', '));
            }
        } else {
            if ($authorities->isEmpty()) {
                $this->command->warn('No decision authorities found in the database. Please create some first.');
            }
            if ($persons->isEmpty()) {
                $this->command->warn('No persons found in the database. Please create some first.');
            }
            if (empty($roleTermsArray)) {
                $this->command->warn('No role terms found in the database.');
            }
        }
    }
}