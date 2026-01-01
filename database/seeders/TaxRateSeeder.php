<?php

namespace Database\Seeders;

use App\Models\TaxRate;
use Illuminate\Database\Seeder;

class TaxRateSeeder extends Seeder
{
    /**
     * US State Sales Tax Rates (as of 2024)
     * Note: These are state-level rates. Local taxes may also apply.
     * States without sales tax: AK, DE, MT, NH, OR
     */
    protected array $taxRates = [
        'AL' => ['name' => 'Alabama', 'rate' => 4.00],
        'AK' => ['name' => 'Alaska', 'rate' => 0.00], // No state sales tax
        'AZ' => ['name' => 'Arizona', 'rate' => 5.60],
        'AR' => ['name' => 'Arkansas', 'rate' => 6.50],
        'CA' => ['name' => 'California', 'rate' => 7.25],
        'CO' => ['name' => 'Colorado', 'rate' => 2.90],
        'CT' => ['name' => 'Connecticut', 'rate' => 6.35],
        'DE' => ['name' => 'Delaware', 'rate' => 0.00], // No state sales tax
        'FL' => ['name' => 'Florida', 'rate' => 6.00],
        'GA' => ['name' => 'Georgia', 'rate' => 4.00],
        'HI' => ['name' => 'Hawaii', 'rate' => 4.00],
        'ID' => ['name' => 'Idaho', 'rate' => 6.00],
        'IL' => ['name' => 'Illinois', 'rate' => 6.25],
        'IN' => ['name' => 'Indiana', 'rate' => 7.00],
        'IA' => ['name' => 'Iowa', 'rate' => 6.00],
        'KS' => ['name' => 'Kansas', 'rate' => 6.50],
        'KY' => ['name' => 'Kentucky', 'rate' => 6.00],
        'LA' => ['name' => 'Louisiana', 'rate' => 4.45],
        'ME' => ['name' => 'Maine', 'rate' => 5.50],
        'MD' => ['name' => 'Maryland', 'rate' => 6.00],
        'MA' => ['name' => 'Massachusetts', 'rate' => 6.25],
        'MI' => ['name' => 'Michigan', 'rate' => 6.00],
        'MN' => ['name' => 'Minnesota', 'rate' => 6.875],
        'MS' => ['name' => 'Mississippi', 'rate' => 7.00],
        'MO' => ['name' => 'Missouri', 'rate' => 4.225],
        'MT' => ['name' => 'Montana', 'rate' => 0.00], // No state sales tax
        'NE' => ['name' => 'Nebraska', 'rate' => 5.50],
        'NV' => ['name' => 'Nevada', 'rate' => 6.85],
        'NH' => ['name' => 'New Hampshire', 'rate' => 0.00], // No state sales tax
        'NJ' => ['name' => 'New Jersey', 'rate' => 6.625],
        'NM' => ['name' => 'New Mexico', 'rate' => 4.875],
        'NY' => ['name' => 'New York', 'rate' => 4.00],
        'NC' => ['name' => 'North Carolina', 'rate' => 4.75],
        'ND' => ['name' => 'North Dakota', 'rate' => 5.00],
        'OH' => ['name' => 'Ohio', 'rate' => 5.75],
        'OK' => ['name' => 'Oklahoma', 'rate' => 4.50],
        'OR' => ['name' => 'Oregon', 'rate' => 0.00], // No state sales tax
        'PA' => ['name' => 'Pennsylvania', 'rate' => 6.00],
        'RI' => ['name' => 'Rhode Island', 'rate' => 7.00],
        'SC' => ['name' => 'South Carolina', 'rate' => 6.00],
        'SD' => ['name' => 'South Dakota', 'rate' => 4.50],
        'TN' => ['name' => 'Tennessee', 'rate' => 7.00],
        'TX' => ['name' => 'Texas', 'rate' => 6.25],
        'UT' => ['name' => 'Utah', 'rate' => 6.10],
        'VT' => ['name' => 'Vermont', 'rate' => 6.00],
        'VA' => ['name' => 'Virginia', 'rate' => 5.30],
        'WA' => ['name' => 'Washington', 'rate' => 6.50],
        'WV' => ['name' => 'West Virginia', 'rate' => 6.00],
        'WI' => ['name' => 'Wisconsin', 'rate' => 5.00],
        'WY' => ['name' => 'Wyoming', 'rate' => 4.00],
        'DC' => ['name' => 'District of Columbia', 'rate' => 6.00],
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ($this->taxRates as $stateCode => $data) {
            TaxRate::updateOrCreate(
                [
                    'country_code' => 'US',
                    'state_code' => $stateCode,
                ],
                [
                    'name' => $data['name'],
                    'rate' => $data['rate'],
                    'is_active' => true,
                ]
            );
        }

        $this->command->info('Tax rates seeded for ' . count($this->taxRates) . ' US states.');
    }
}
