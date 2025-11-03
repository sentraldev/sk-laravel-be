<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class FilamentResourcePermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear cached permissions to ensure changes take effect immediately
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $guard = config('auth.defaults.guard', 'web');

        $resourcesPath = app_path('Filament/Resources');

        // Collect resource folder names (e.g., Products, Categories, Users, ...)
        $resourceFolders = collect(File::isDirectory($resourcesPath) ? File::directories($resourcesPath) : [])
            ->map(fn (string $path) => basename($path))
            ->filter()
            ->values();

        // If discovery fails for any reason, you can hardcode a fallback list here
        if ($resourceFolders->isEmpty()) {
            $resourceFolders = collect([
                'Banners', 'Blogs', 'Brands', 'Categories', 'Permissions', 'ProductDiscounts',
                'ProductReviews', 'Products', 'PromoVouchers', 'Promos', 'Roles', 'ShopLocations', 'SubCategories', 'Users',
            ]);
        }

        $createdPermissions = collect();

        foreach ($resourceFolders as $folder) {
            // Convert folder/class-style names to a readable label: "PromoVouchers" -> "promo vouchers"
            $label = Str::of($folder)->kebab()->replace('-', ' ')->lower();

            // Base permissions per resource
            $managePermission = (string) Str::of('manage ' . $label)->squish();
            $createPermission = (string) Str::of('create ' . $label)->squish();
            $deletePermission = (string) Str::of('delete ' . $label)->squish();

            $permManage = Permission::query()->firstOrCreate([
                'name' => $managePermission,
                'guard_name' => $guard,
            ]);
            $permCreate = Permission::query()->firstOrCreate([
                'name' => $createPermission,
                'guard_name' => $guard,
            ]);
            $permDelete = Permission::query()->firstOrCreate([
                'name' => $deletePermission,
                'guard_name' => $guard,
            ]);

            $createdPermissions->push($permManage, $permCreate, $permDelete);

            // Role per resource: "promo vouchers manager" gets all related permissions
            $roleName = (string) Str::of($label)->append(' manager');
        }

        // Ensure a global admin role exists and has all permissions
        $adminRole = Role::query()->firstOrCreate([
            'name' => 'admin',
            'guard_name' => $guard,
        ]);

        $allPermissionNames = Permission::query()->pluck('name')->all();
        $adminRole->syncPermissions($allPermissionNames);
    }
}
