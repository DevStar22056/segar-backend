<?php

use App\Role;
use Illuminate\Database\Seeder;

class RoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $role_SuperUser_user = new Role;
        $role_SuperUser_user->id = 1;
        $role_SuperUser_user->name = 'superuser';
        $role_SuperUser_user->description = 'SuperUser...';
        $role_SuperUser_user->save();

        $role_Admin_user = new Role;
        $role_Admin_user->id = 7;
        $role_Admin_user->name = 'admin';
        $role_Admin_user->description = 'Admin...';
        $role_Admin_user->save();

        $role_BusinessSupport_user = new Role;
        $role_BusinessSupport_user->id = 5;
        $role_BusinessSupport_user->name = 'businesssupport';
        $role_BusinessSupport_user->description = 'BusinessSupport...';
        $role_BusinessSupport_user->save();

        $role_Finance_user = new Role;
        $role_Finance_user->id = 8;
        $role_Finance_user->name = 'finance';
        $role_Finance_user->description = 'Finance...';
        $role_Finance_user->save();

        $role_TechnicalSupport_user = new Role;
        $role_TechnicalSupport_user->id = 6;
        $role_TechnicalSupport_user->name = 'technicalsupport';
        $role_TechnicalSupport_user->description = 'TechnicalSupport...';
        $role_TechnicalSupport_user->save();

        $role_user = new Role;
        $role_user->name = 'user';
        $role_user->description = 'User...';
        $role_user->save();

        $role_internal_guest_user = new Role;
        $role_internal_guest_user->name = 'guest';
        $role_internal_guest_user->description = 'User guest...';
        $role_internal_guest_user->save();
    }
}
