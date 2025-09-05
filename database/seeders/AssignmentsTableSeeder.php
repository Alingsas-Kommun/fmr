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
        // First, let's get some actual board and person IDs from WordPress
        $boards = DB::table('posts')
            ->where('post_type', 'board')
            ->get(['ID', 'post_title']);

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

        // Only proceed if we have both boards and persons
        if ($boards->isNotEmpty() && $persons->isNotEmpty()) {
            $assignments = [];
            
            // Create some example assignments
            foreach ($boards as $board) {
                // Assign up to 3 random persons to each board
                $numAssignments = min(3, $persons->count());
                $assignedPersons = $persons->random($numAssignments);
                
                foreach ($assignedPersons as $index => $person) {
                    $assignments[] = [
                        'board_id' => $board->ID,
                        'person_id' => $person->ID,
                        'role' => $roles[$index % count($roles)], // Cycle through roles
                        'period_start' => '2024-01-01',
                        'period_end' => '2026-12-31',
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