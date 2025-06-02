<?php
namespace Database\Seeders;

use App\Domain\Acl\Models\Permission;
use App\Domain\Acl\Models\Role;
use App\Domain\Admin\Models\Admin;
use Illuminate\Database\Seeder;
use Spatie\Permission\PermissionRegistrar;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            [
                'name' => 'taxonomies.view',
                'en' => ['display_name' => 'View Taxonomy'],
                'vi' => ['display_name' => 'Xem loại danh mục'],
            ],
            [
                'name' => 'taxonomies.create',
                'en' => ['display_name' => 'Create Taxonomy'],
                'vi' => ['display_name' => 'Thêm loại danh mục'],
            ],
            [
                'name' => 'taxonomies.update',
                'en' => ['display_name' => 'Update Taxonomy'],
                'vi' => ['display_name' => 'Cập nhật loại danh mục'],
            ],
            [
                'name' => 'taxonomies.delete',
                'en' => ['display_name' => 'Delete Taxonomy'],
                'vi' => ['display_name' => 'Xóa loại danh mục'],
            ],
            [
                'name' => 'taxons.view',
                'en' => ['display_name' => 'View Taxon'],
                'vi' => ['display_name' => 'Xem danh mục'],
            ],
            [
                'name' => 'taxons.create',
                'en' => ['display_name' => 'Create Taxon'],
                'vi' => ['display_name' => 'Thêm danh mục'],
            ],
            [
                'name' => 'taxons.update',
                'en' => ['display_name' => 'Update Taxon'],
                'vi' => ['display_name' => 'Cập nhật danh mục'],
            ],
            [
                'name' => 'taxons.delete',
                'en' => ['display_name' => 'Delete Taxon'],
                'vi' => ['display_name' => 'Xóa danh mục'],
            ],
            [
                'name' => 'option-types.view',
                'en' => ['display_name' => 'View Option'],
                'vi' => ['display_name' => 'Xem tùy chọn'],
            ],
            [
                'name' => 'option-types.create',
                'en' => ['display_name' => 'Create Option'],
                'vi' => ['display_name' => 'Thêm tùy chọn'],
            ],
            [
                'name' => 'option-types.update',
                'en' => ['display_name' => 'Update Option'],
                'vi' => ['display_name' => 'Cập nhật tùy chọn'],
            ],
            [
                'name' => 'option-types.delete',
                'en' => ['display_name' => 'Delete Option'],
                'vi' => ['display_name' => 'Xóa tùy chọn'],
            ],

           [
               'name' => 'admins.view',
               'en' => ['display_name' => 'View Admin'],
               'vi' => ['display_name' => 'Xem quản trị viên'],
           ],
            [
                'name' => 'admins.create',
                'en' => ['display_name' => 'Create Admin'],
                'vi' => ['display_name' => 'Thêm quản trị viên'],
            ],
            [
                'name' => 'admins.update',
                'en' => ['display_name' => 'Update Admin'],
                'vi' => ['display_name' => 'Cập nhật quản trị viên'],
            ],
            [
                'name' => 'admins.delete',
                'en' => ['display_name' => 'Delete Admin'],
                'vi' => ['display_name' => 'Xóa quản trị viên'],
            ],

            [
                'name' => 'roles.view',
                'en' => ['display_name' => 'View Role'],
                'vi' => ['display_name' => 'Xem vai trò'],
            ],
            [
                'name' => 'roles.create',
                'en' => ['display_name' => 'Create Role'],
                'vi' => ['display_name' => 'Thêm vai trò'],
            ],
            [
                'name' => 'roles.update',
                'en' => ['display_name' => 'Update Role'],
                'vi' => ['display_name' => 'Cập nhật vai trò'],
            ],
            [
                'name' => 'roles.delete',
                'en' => ['display_name' => 'Delete Role'],
                'vi' => ['display_name' => 'Xóa vai trò'],
            ],
            [
                'name' => 'pages.view',
                'en' => ['display_name' => 'View Page'],
                'vi' => ['display_name' => 'Xem trang'],
            ],
            [
                'name' => 'pages.create',
                'en' => ['display_name' => 'Create Page'],
                'vi' => ['display_name' => 'Thêm trang'],
            ],
            [
                'name' => 'pages.update',
                'en' => ['display_name' => 'Update Page'],
                'vi' => ['display_name' => 'Cập nhật trang'],
            ],
            [
                'name' => 'pages.delete',
                'en' => ['display_name' => 'Delete Page'],
                'vi' => ['display_name' => 'Xóa trang'],
            ],

            [
                'name' => 'posts.view',
                'en' => ['display_name' => 'View Post'],
                'vi' => ['display_name' => 'Xem bài viết'],
            ],
            [
                'name' => 'posts.create',
                'en' => ['display_name' => 'Create Post'],
                'vi' => ['display_name' => 'Thêm bài viết'],
            ],
            [
                'name' => 'posts.update',
                'en' => ['display_name' => 'Update Post'],
                'vi' => ['display_name' => 'Cập nhật bài viết'],
            ],
            [
                'name' => 'posts.delete',
                'en' => ['display_name' => 'Delete Post'],
                'vi' => ['display_name' => 'Xóa bài viết'],
            ],

            [
                'name' => 'banners.view',
                'en' => ['display_name' => 'View Banner'],
                'vi' => ['display_name' => 'Xem banner'],
            ],
            [
                'name' => 'banners.create',
                'en' => ['display_name' => 'Create Banner'],
                'vi' => ['display_name' => 'Thêm banner'],
            ],
            [
                'name' => 'banners.update',
                'en' => ['display_name' => 'Update Banner'],
                'vi' => ['display_name' => 'Cập nhật banner'],
            ],
            [
                'name' => 'banners.delete',
                'en' => ['display_name' => 'Delete Banner'],
                'vi' => ['display_name' => 'Xóa banner'],
            ],

            [
                'name' => 'contacts.view',
                'en' => ['display_name' => 'View Contacts'],
                'vi' => ['display_name' => 'Xem danh sách liên hệ'],
            ],
            [
                'name' => 'log-search.view',
                'en' => ['display_name' => 'View Log Search'],
                'vi' => ['display_name' => 'Xem lịch sử tìm kiếm'],
            ],

            [
                'name' => 'subscribe-email.view',
                'en' => ['display_name' => 'View Subscribe Email'],
                'vi' => ['display_name' => 'Xem danh sách đăng ký nhận tin'],
            ],
            [
                'name' => 'mail-settings.view',
                'en' => ['display_name' => 'View Mail Setting'],
                'vi' => ['display_name' => 'Xem cài đặt chiến dịch gửi Mail'],
            ],
            [
                'name' => 'mail-settings.create',
                'en' => ['display_name' => 'Create Mail Setting'],
                'vi' => ['display_name' => 'Tạo chiến dịch gửi Mail'],
            ],
            [
                'name' => 'mail-settings.update',
                'en' => ['display_name' => 'Update Mail Setting'],
                'vi' => ['display_name' => 'Cập nhật chiến dịch gửi Mail'],
            ],
            [
                'name' => 'mail-settings.delete',
                'en' => ['display_name' => 'Delete Mail Setting'],
                'vi' => ['display_name' => 'Xóa chiến dịch gửi Mail'],
            ],
            [
                'name' => 'mail-settings.send',
                'en' => ['display_name' => 'Send Mail Setting'],
                'vi' => ['display_name' => 'Gửi chiến dịch Mail'],
            ],
            [
                'name' => 'menus.delete',
                'en' => ['display_name' => 'Delete Menu'],
                'vi' => ['display_name' => 'Xóa Menu'],
            ],
            [
                'name' => 'log-activities.index',
                'en' => ['display_name' => 'View Log Activities'],
                'vi' => ['display_name' => 'Xem lịch sử thao tác'],
            ],
            [
                'name' => 'log-activities.destroy',
                'en' => ['display_name' => 'Delete Log Activities'],
                'vi' => ['display_name' => 'Xóa lịch sử thao tác'],
            ],

            [
                'name' => 'menus.index',
                'en' => ['display_name' => 'View Menu'],
                'vi' => ['display_name' => 'Xem cài đặt Menu'],
            ],
            [
                'name' => 'menus.store',
                'en' => ['display_name' => 'Create Menu'],
                'vi' => ['display_name' => 'Thêm Menu'],
            ],
            [
                'name' => 'menus.edit',
                'en' => ['display_name' => 'Update Menu'],
                'vi' => ['display_name' => 'Cập nhật Menu'],
            ],
        ];

        foreach ($permissions as $permission) {
            Permission::create($permission);
        }

        $roles = [
            [
                'name' => 'admin',
                'en' => ['display_name' => 'Manager'],
                'vi' => ['display_name' => 'Quản trị viên'],
            ],
        ];
        foreach ($roles as $role) {
            Role::create($role);
        }

        $superAdminRole = Role::create([
            'name' => 'superadmin',
            'en' => ['display_name' => 'Admin'],
            'vi' => ['display_name' => 'Admin'],
        ]);


        $superAdmin = Admin::create([
            'email' => 'admin@gmail.com',
            'first_name' => 'Super',
            'last_name' => 'Admin',
            'password' => bcrypt('12312312'),
        ]);

        $superAdmin->assignRole($superAdminRole);
    }
}
