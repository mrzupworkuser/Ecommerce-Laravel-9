<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public const GUARD = 'api';

    protected array $roles = [
        'superadmin' => ['*'],
        'admin' => ['*'],
        'staff' => [
            'customer', 'inventory.product', 'inventory.category', 'transaction', 'calendar', 'inventory.promocode',
            'order', 'report'
        ],
    ];

    protected array $permissions = [
        'billing',
        'customer',
        'settings',
        'domain',
        'domain.create',
        'user',
        'user.invite',
        'inventory.product',
        'inventory.promocode',
        'inventory.asset.create',
        'inventory.asset.update',
        'inventory.asset.delete',
        'inventory.asset.view',
        'inventory.promocode.create',
        'inventory.promocode.update',
        'inventory.promocode.delete',
        'inventory.promocode.view',
        'inventory.category',
        'inventory.deductible',
        'transaction',
        'order',
        'calendar',
        'session',
        'report',
        'team',
        'apikey'
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (app()->environment('production') && Role::count() > 0) {
            Log::error('Cannot run RoleSeeder in production environment.');
            return;
        }

        $this
            ->createPermissions()
            ->createRoles()
            ->each(function (Role $role) {
                $permissions = $this->roles[$role->name][0] === '*' ? $this->permissions : $this->roles[$role->name];
                $role->syncPermissions(
                    Permission::select('id')->whereIn('name', $permissions)->get()->pluck('id')
                );
            });
    }

    public function createPermissions(): RoleSeeder
    {
        DB::table('permissions')->insert(
            collect($this->permissions)->map(fn($permission) => [
                'name' => $permission, 'guard_name' => self::GUARD
            ])->toArray()
        );
        return $this;
    }

    public function createRoles(): Collection
    {
        DB::table('roles')->insert(
            collect($this->roles)->keys()->map(fn($role) => ['name' => $role, 'guard_name' => self::GUARD])->toArray()
        );
        return Role::whereIn('name', array_keys($this->roles))->get();
    }
}
