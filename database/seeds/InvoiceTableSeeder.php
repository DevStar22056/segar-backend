<?php

use Faker\Generator as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InvoiceTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(Faker $faker)
    {
        for ($i = 0; $i < 5; $i++) {
            DB::table('invoices')->insert([
                'creator' => $faker->numberBetween(1, 5),
                'user_id' => $faker->numberBetween(1, 5),
                'approval' => null,
                'issue_date' => $faker->dateTime('now'),
                'completion_date' => $faker->dateTime('now +30 days'),
                'description' => $faker->text($maxNbChars = 50),
                'hours_value' => $faker->numberBetween(10, 200),
                'hours_value_netto' => $faker->numberBetween(1000, 10000),
                'hours_value_gross' => $faker->numberBetween(1000, 10000),
                'hours_value_vat' => $faker->numberBetween(1000, 10000),
                'fixed_price' => null,
                'fixed_price_gross' => null,
                'fixed_price_vat' => null,
                'overtime_value' => $faker->numberBetween(1000, 10000),
                'overtime_value_netto' => $faker->numberBetween(1000, 10000),
                'overtime_value_gross' => $faker->numberBetween(1000, 10000),
                'overtime_value_vat' => $faker->numberBetween(1000, 10000),
                'is_accepted' => false,
                'status' => $faker->numberBetween(0, 4),
                'payment_date' => $faker->dateTimeBetween('now', '+30 days'),
            ]);
        }
    }
}
