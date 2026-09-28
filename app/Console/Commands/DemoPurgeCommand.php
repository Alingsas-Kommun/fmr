<?php

namespace App\Console\Commands;

use App\Exceptions\DemoContentException;
use App\Services\DemoContentService;
use Illuminate\Console\Command;
use Throwable;

class DemoPurgeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fmr:demo-purge';

    public function __construct()
    {
        $this->description = __('Remove fictional demo content created by fmr:demo-seed.', 'fmr');

        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(DemoContentService $demo): int
    {
        try {
            $removed = $demo->purge();
        } catch (DemoContentException $exception) {
            $this->error($exception->getMessage());

            return self::FAILURE;
        } catch (Throwable $exception) {
            $this->error($exception->getMessage());

            return self::FAILURE;
        }

        if (! $removed) {
            $this->info(__('No demo content is installed.', 'fmr'));

            return self::SUCCESS;
        }

        $this->info(__('Demo content has been removed.', 'fmr'));

        return self::SUCCESS;
    }
}
