<?php

declare(strict_types=1);

namespace Rboschin\SimpleTenant\Console;

use Illuminate\Console\Command;

class PublishSeedsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'simpletenant:publish-seeds';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publish seeders from the SimpleTenant package';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->components->info('Publishing SimpleTenant seeders...');

        $this->call('vendor:publish', [
            '--tag' => 'simpletenant-seeders',
            '--force' => true,
        ]);

        $this->components->info('Seeders published successfully.');
        $this->components->info('You can now run: php artisan db:seed --class=Database\\Seeders\\TenantSeeder');

        return 0;
    }
}