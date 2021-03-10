<?php

use Faker\Generator as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\User;
use App\Role;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(Faker $faker)
    {
        $admin = new User;
        $admin->name = 'searger';
        $admin->surname = 'searger';
        $admin->email = 'superuser@searger.pl';
        //$admin->password = bcrypt('demo');
        $admin->password = bcrypt('superuser@searger.pl');
        $admin->can_login = 1;
        $admin->is_active = 1;
        $admin->verified = 1;
        $admin->role = "1";
        $admin->vat_value = "23";
        $admin->hourly_rate = "23";
        $admin->internal_hour_rate = "23";
        $admin->oncall_10 = "1";
        $admin->oncall_30 = "1";
        $admin->save();
    }
}
