<?php

namespace App\Console\Commands;

use App\Exceptions\DemoContentException;
use App\Services\DemoContentService;
use Illuminate\Console\Command;
use Throwable;

class DemoSeedCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fmr:demo-seed {--force : Seed even when the site already has register data}';

    public function __construct()
    {
        $this->description = __('Create fictional demo content for FMR Demo.', 'fmr');

        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(DemoContentService $demo): int
    {
        try {
            $counts = $demo->seed((bool) $this->option('force'));
        } catch (DemoContentException $exception) {
            $this->error($exception->getMessage());

            return self::FAILURE;
        } catch (Throwable $exception) {
            $this->error($exception->getMessage());

            return self::FAILURE;
        }

        $this->info(__('Demo content for FMR Demo has been created.', 'fmr'));
        $this->line(sprintf(
            /* translators: 1: persons, 2: parties, 3: boards, 4: decision authorities, 5: assignments */
            __('Created %1$d persons, %2$d parties, %3$d boards, %4$d decision authorities and %5$d assignments.', 'fmr'),
            $counts['persons'],
            $counts['parties'],
            $counts['boards'],
            $counts['decision_authorities'],
            $counts['assignments']
        ));

        return self::SUCCESS;
    }
}
