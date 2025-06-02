<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Domain\Page\Models\Page;

class PageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Page::create([
            'user_id' => 1,
            'title' => 'Trang đầu tiên',
            'status' => \App\Enums\PageState::Active,
            'slug' => 'trang-dau-tien',
            'body' => 'Nội dung của trang đầu tiên',
        ]);
    }
}
