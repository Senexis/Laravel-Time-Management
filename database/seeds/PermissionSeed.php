<?php

use App\User;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PermissionSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Reset cached roles and permissions.
        app()['cache']->forget('spatie.permission.cache');

        // Create permissions
        Permission::findOrCreate('create.locations');
        Permission::findOrCreate('create.projects');
        Permission::findOrCreate('create.roles');
        Permission::findOrCreate('create.time_entries');
        Permission::findOrCreate('create.users');
        Permission::findOrCreate('create.work_types');

        // Delete permissions
        Permission::findOrCreate('delete.locations.others');
        Permission::findOrCreate('delete.locations.self');
        Permission::findOrCreate('delete.projects');
        Permission::findOrCreate('delete.roles');
        Permission::findOrCreate('delete.time_entries.others');
        Permission::findOrCreate('delete.time_entries.self');
        Permission::findOrCreate('delete.users');
        Permission::findOrCreate('delete.work_types.others');
        Permission::findOrCreate('delete.work_types.self');

        // Edit permissions
        Permission::findOrCreate('edit.locations.others');
        Permission::findOrCreate('edit.locations.self');
        Permission::findOrCreate('edit.projects');
        Permission::findOrCreate('edit.roles');
        Permission::findOrCreate('edit.time_entries.others');
        Permission::findOrCreate('edit.time_entries.self');
        Permission::findOrCreate('edit.users');
        Permission::findOrCreate('edit.work_types.others');
        Permission::findOrCreate('edit.work_types.self');

        // List permissions
        Permission::findOrCreate('list.locations.others');
        Permission::findOrCreate('list.locations.self');
        Permission::findOrCreate('list.projects');
        Permission::findOrCreate('list.roles');
        Permission::findOrCreate('list.time_entries.others');
        Permission::findOrCreate('list.time_entries.self');
        Permission::findOrCreate('list.users');
        Permission::findOrCreate('list.work_types.others');
        Permission::findOrCreate('list.work_types.self');

        // Show permissions
        Permission::findOrCreate('show.locations.others');
        Permission::findOrCreate('show.locations.self');
        Permission::findOrCreate('show.projects');
        Permission::findOrCreate('show.roles');
        Permission::findOrCreate('show.time_entries.others');
        Permission::findOrCreate('show.time_entries.self');
        Permission::findOrCreate('show.users');
        Permission::findOrCreate('show.work_types.others');
        Permission::findOrCreate('show.work_types.self');

        Permission::findOrCreate('show.reports.others');
        Permission::findOrCreate('show.reports.self');
        Permission::findOrCreate('show.user_actions');

        // Other permissions
        Permission::findOrCreate('lock.time_entries.batch');
        Permission::findOrCreate('lock.time_entries.others');
        Permission::findOrCreate('lock.time_entries.self');
        Permission::findOrCreate('login_as.users');
        Permission::findOrCreate('send.feedback');
        Permission::findOrCreate('show.money');
        Permission::findOrCreate('start.time_entries');
        Permission::findOrCreate('stop.time_entries.others');
        Permission::findOrCreate('stop.time_entries.self');
        Permission::findOrCreate('unlock.time_entries.batch');
        Permission::findOrCreate('unlock.time_entries.others');
        Permission::findOrCreate('unlock.time_entries.self');

        // Only create roles if there are none.
        if (Role::count() < 1) {
            // Create roles
            $adm = Role::create(['name' => 'Administrator']);
            $usr = Role::create(['name' => 'User']);

            // Assign existing permissions
            $adm->givePermissionTo(Permission::all());
            $usr->givePermissionTo(Permission::where('name', 'LIKE', '%.self')->get());

            // Assign existing users to roles
            if (User::count() > 0) {
                // Assign users to roles.
                $adm = User::where('id', 1)->first();
                $usr = User::where('id', '!=', 1)->get();

                // Assign the very first user to admin.
                $adm->assignRole('Administrator');

                // Assign the rest user roles.
                foreach ($usr as $i) {
                    $i->assignRole('User');
                }
            }
        }
    }
}
