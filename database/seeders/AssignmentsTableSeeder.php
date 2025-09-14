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

        // Create role terms in the role taxonomy
        $roleNames = [
            'Ordförande',
            'Vice ordförande',
            'Ledamot',
            'Ersättare',
            'Sekreterare'
        ];

        $roleTermIds = [];
        foreach ($roleNames as $roleName) {
            // Check if term already exists by joining with term_taxonomy
            $existingTerm = DB::table('terms')
                ->join('term_taxonomy', 'terms.term_id', '=', 'term_taxonomy.term_id')
                ->where('terms.name', $roleName)
                ->where('term_taxonomy.taxonomy', 'role')
                ->select('terms.term_id')
                ->first();

            if ($existingTerm) {
                $roleTermIds[$roleName] = $existingTerm->term_id;
            } else {
                // Create new term
                $termId = DB::table('terms')->insertGetId([
                    'name' => $roleName,
                    'slug' => sanitize_title($roleName),
                    'term_group' => 0,
                ]);

                // Create term taxonomy entry
                DB::table('term_taxonomy')->insert([
                    'term_id' => $termId,
                    'taxonomy' => 'role',
                    'description' => '',
                    'parent' => 0,
                    'count' => 0,
                ]);

                $roleTermIds[$roleName] = $termId;
            }
        }

        // Only proceed if we have both decision authorities and persons
        if ($authorities->isNotEmpty() && $persons->isNotEmpty()) {
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
                    $roleName = $roleNames[$index % count($roleNames)]; // Cycle through role names
                    $assignments[] = [
                        'decision_authority_id' => $authority->id,
                        'person_id' => $person->ID,
                        'role_term_id' => $roleTermIds[$roleName], // Use term_id instead of role string
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
            }
        } else {
            $this->command->warn('No boards or persons found in the database. Please create some first.');
        }
    }
}