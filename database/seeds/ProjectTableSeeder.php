<?php

use Faker\Generator as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProjectTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(Faker $faker)
    {
        for ($i = 0; $i < 5; $i++) {
            DB::table('projects')->insert([
                'name' => $faker->text($maxNbChars = 10),
                'description' => $faker->text($maxNbChars = 15),
                'company_name' => $faker->text($maxNbChars = 30),
                'user_id' => '2',
                'client_id' => $faker->numberBetween(1, 500)
            ]);
        }
    }
}
