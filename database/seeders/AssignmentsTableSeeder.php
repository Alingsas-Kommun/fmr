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

        // Example roles
        $roles = [
            'Ordförande',
            'Vice ordförande',
            'Ledamot',
            'Ersättare',
            'Sekreterare'
        ];

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
                    $assignments[] = [
                        'decision_authority_id' => $authority->id,
                        'person_id' => $person->ID,
                        'role' => $roles[$index % count($roles)], // Cycle through roles
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