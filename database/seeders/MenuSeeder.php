<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Domain\Menu\Models\Menu;
use App\Domain\Menu\Models\MenuItem;
use App\Domain\Post\Models\Post;
use App\Domain\Page\Models\Page;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $menus = [
            0 => [
                'name' => 'Đầu trang',
                'status' => Menu::STATUS_SHOW
            ],
            1 => [
                'name' => 'Header',
                'status' => Menu::STATUS_SHOW
            ],
            2 => [
                'name' => 'Menu Vi',
                'status' => Menu::STATUS_SHOW
            ],
            3 => [
                'name' => 'Menu En',
                'status' => Menu::STATUS_SHOW
            ],
            4 => [
                'name' => 'Menu Hot Vi',
                'status' => Menu::STATUS_SHOW
            ],
            5 => [
                'name' => 'Menu Hot En',
                'status' => Menu::STATUS_SHOW
            ],
        ];
        $posts = Post::select('id', 'title')->get();
        $pages = Page::select('id', 'title')->get();

        foreach ($menus as $menu) {
            $menu_db = Menu::create($menu);
            $parent_menu = MenuItem::create(array_merge($menu, [
                'menu_id' => $menu_db->id,
                'type' => 0,
            ]));

            // create sub item menu
            if ($posts) {
                foreach ($posts as $post) {
                    MenuItem::create(array_merge($menu, [
                        'menu_id' => $menu_db->id,
                        'type' => MenuItem::TYPE_POST,
                        'parent_id' => $parent_menu->id,
                        'status' => MenuItem::STATUS_SHOW,
                        'item_id' => $post->id,
                        'name' => $post->title,
                    ]));
                }
            }
            if ($pages) {
                foreach ($pages as $page) {
                    MenuItem::create(array_merge($menu, [
                        'menu_id' => $menu_db->id,
                        'type' => MenuItem::TYPE_PAGE,
                        'parent_id' => $parent_menu->id,
                        'status' => MenuItem::STATUS_SHOW,
                        'item_id' => $page->id,
                        'name' => $page->title,
                    ]));
                }
            }
        }
    }
}
