<?php

namespace Database\Seeders;

use App\Domain\Acl\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AddPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $permissions = [
            [
                'name' => 'comments.view',
                'en' => ['display_name' => 'View Comment'],
                'vi' => ['display_name' => 'Xem bình luận'],
            ],

            [
                'name' => 'banners.view',
                'en' => ['display_name' => 'View Ads'],
                'vi' => ['display_name' => 'Xem quảng cáo'],
            ],
            [
                'name' => 'banners.create',
                'en' => ['display_name' => 'Create Ads'],
                'vi' => ['display_name' => 'Thêm quảng cáo'],
            ],
            [
                'name' => 'banners.update',
                'en' => ['display_name' => 'Update Ads'],
                'vi' => ['display_name' => 'Cập nhật quảng cáo'],
            ],
            [
                'name' => 'banners.delete',
                'en' => ['display_name' => 'Delete Ads'],
                'vi' => ['display_name' => 'Xóa quảng cáo'],
            ],

            [
                'name' => 'contents.view',
                'en' => ['display_name' => 'View Content'],
                'vi' => ['display_name' => 'Quản lý nội dung'],
            ],

            [
                'name' => 'seo.view',
                'en' => ['display_name' => 'View SEO'],
                'vi' => ['display_name' => 'Xem Tool SEO'],
            ],
        ];

        foreach ($permissions as $permission) {
            Permission::updateOrCreate([
                'name' => $permission['name']
            ], $permission);
        }
    }
}
