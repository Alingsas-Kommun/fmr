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

        // Example decision authority types
        $types = [
            'Nämnd',
            'Styrelse',
            'Utskott',
            'Beredning',
            'Råd'
        ];

        // Only proceed if we have boards
        if ($boards->isNotEmpty()) {
            $authorities = [];
            
            foreach ($boards as $board) {
                // Create 1-2 decision authorities per board
                $numAuthorities = rand(1, 2);
                
                for ($i = 0; $i < $numAuthorities; $i++) {
                    $type = $types[array_rand($types)];
                    $title = $i === 0 ? 
                        "Ordinarie {$type}" : 
                        "Tillfällig {$type}";

                    // Set different date ranges for regular vs temporary authorities
                    $startDate = $i === 0 ? '2024-01-01' : '2024-06-01';
                    $endDate = $i === 0 ? '2026-12-31' : '2025-05-31';

                    $authorities[] = [
                        'board_id' => $board->ID,
                        'title' => $title,
                        'type' => $type,
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
            }
        } else {
            $this->command->warn('No boards found in the database. Please create some first.');
        }
    }
}