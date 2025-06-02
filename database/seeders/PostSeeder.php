<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Domain\Post\Models\Post;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Post::create([
            'user_id' => 1,
            'title' => 'Bài viết đầu tiên',
            'description' => 'Mô tả cho bài viết đầu tiên',
            'status' => \App\Enums\PostState::Active,
            'slug' => 'bai-viet-dau-tien',
            'body' => 'Nội dung của bài viết đầu tiên',
        ]);
    }
}
