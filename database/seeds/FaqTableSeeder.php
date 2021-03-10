<?php

use Faker\Generator as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FaqTableSeeder extends Seeder
{

    /**
     * @param Faker $faker
     */
    public function run(Faker $faker)
    {
        for ($i = 0; $i < 10; $i++) {
            DB::table('faqs')->insert([
                'title' => $faker->text($maxNbChars = 10),
                'description' => $faker->text($maxNbChars = 60)
            ]);
        }
    }
}
