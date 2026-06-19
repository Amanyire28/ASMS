<?php

namespace Database\Seeders;

use App\Models\PaymentMethod;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PaymentMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $methods = [
            [
                'name' => 'Cash',
                'code' => 'CASH',
                'description' => 'Payment made in cash',
                'is_active' => true,
            ],
            [
                'name' => 'Check',
                'code' => 'CHECK',
                'description' => 'Payment made by check',
                'is_active' => true,
            ],
            [
                'name' => 'Bank Transfer',
                'code' => 'BANK_TRANSFER',
                'description' => 'Payment made via bank transfer',
                'is_active' => true,
            ],
            [
                'name' => 'Mobile Money',
                'code' => 'MOBILE_MONEY',
                'description' => 'Payment made via mobile money platform',
                'is_active' => true,
            ],
            [
                'name' => 'Online Payment',
                'code' => 'ONLINE',
                'description' => 'Payment made through online payment gateway',
                'is_active' => true,
            ],
        ];

        foreach ($methods as $method) {
            PaymentMethod::updateOrCreate(
                ['code' => $method['code']],
                $method
            );
        }
    }
}
