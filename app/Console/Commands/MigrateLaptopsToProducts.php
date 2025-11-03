<?php

namespace App\Console\Commands;

use Database\Seeders\MigrateLaptopsToProductsSeeder;
use Illuminate\Console\Command;

class MigrateLaptopsToProducts extends Command
{
    protected $signature = 'products:migrate-laptops {--fresh : Run migrations before seeding}';
    protected $description = 'Migrate Laptop rows into Products.details JSON and discounted_price column';

    public function handle(): int
    {
        if ($this->option('fresh')) {
            $this->call('migrate');
        }

        $this->info('Migrating laptops into products...');

        // Run the dedicated seeder
        $this->call('db:seed', [
            '--class' => MigrateLaptopsToProductsSeeder::class,
        ]);

        $this->info('Done. You may now disable the Laptops Filament resource via env DISABLE_LAPTOPS_RESOURCE=true');
        return self::SUCCESS;
    }
}
