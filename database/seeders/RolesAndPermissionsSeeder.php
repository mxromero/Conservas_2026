<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Limpia la caché de roles y permisos
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Crear permisos (ejemplo)
        \Spatie\Permission\Models\Permission::create(['name' => 'Reportes']);
        \Spatie\Permission\Models\Permission::create(['name' => 'Notificaciones']);
        \Spatie\Permission\Models\Permission::create(['name' => 'Vaciado']);
        \Spatie\Permission\Models\Permission::create(['name' => 'Administrar Sistema']);
        \Spatie\Permission\Models\Permission::create(['name' => 'Recuperacion Umas']);
        \Spatie\Permission\Models\Permission::create(['name' => 'Fuera Norma']);

        // Crear roles y asignar permisos
        $admin = Role::create(['name' => 'administrador']);
        $admin->givePermissionTo(Permission::all());

        Role::create(['name' => 'usuario_notificacion'])
            ->givePermissionTo(['Notificaciones'])
            ->givePermissionTo('Recuperacion Umas')
            ->givePermissionTo('Fuera Norma');

        Role::create(['name' => 'usuario_vaciado'])
            ->givePermissionTo(['Vaciado'])
            ->givePermissionTo('Recuperacion Umas')
            ->givePermissionTo('Fuera Norma');

        Role::create(['name' => 'reportes'])
            ->givePermissionTo(['Reportes']);
    }
}
