<?php

namespace Database\Seeders;

use App\Models\FraudRule;
use Illuminate\Database\Seeder;

class FraudRuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        FraudRule::initializeDefaults();

        $this->command->info('Default fraud rules have been initialized.');
    }
}
