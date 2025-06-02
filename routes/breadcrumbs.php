<?php

declare(strict_types=1);

use App\Domain\Acl\Models\Role;
use App\Domain\Admin\Models\Admin;
use App\Domain\Banner\Models\Banner;
use App\Domain\Menu\Models\InternalLink;
use App\Domain\Page\Models\Page;
use App\Domain\Post\Models\Post;
use App\Domain\Taxonomy\Models\Taxon;
use App\Domain\Taxonomy\Models\Taxonomy;
use DaveJamesMiller\Breadcrumbs\BreadcrumbsGenerator;
use DaveJamesMiller\Breadcrumbs\Facades\Breadcrumbs;
use Spatie\Activitylog\Models\Activity;
use App\Domain\Menu\Models\Menu;
use App\Models\Order;

// Home
Breadcrumbs::for('admin.dashboard', function (BreadcrumbsGenerator $trail) {
    $trail->push(__('Trang chủ'), route('admin.dashboard'), ['icon' => 'fal fa-home']);
});

// Home => Account Settings
Breadcrumbs::for('admin.account-settings.edit', function (BreadcrumbsGenerator $trail) {
    $trail->parent('admin.dashboard');
    $trail->push(__('Thiết lập tài khoản'), route('admin.account-settings.edit'));
});


/*
|--------------------------------------------------------------------------
| Application Breadcrumbs
|--------------------------------------------------------------------------
*/
// Home > Taxonomies
Breadcrumbs::for('admin.taxonomies.index', function (BreadcrumbsGenerator $trail) {
    $trail->parent('admin.dashboard');
    $trail->push(__('Loại danh mục'), route('admin.taxonomies.index'), ['icon' => 'fal fa-folder-tree']);
});

// Home > Taxonomies > Create

Breadcrumbs::for('admin.taxonomies.create', function (BreadcrumbsGenerator $trail) {
    $trail->parent('admin.taxonomies.index');
    $trail->push(__('Tạo'), route('admin.taxonomies.create'));
});

// Home > Taxonomies > [taxonomy] > Edit
Breadcrumbs::for('admin.taxonomies.edit', function (BreadcrumbsGenerator $trail, Taxonomy $taxonomy) {
    $trail->parent('admin.taxonomies.index');
    $trail->push($taxonomy->name, '#');
    $trail->push(__('Chỉnh sửa'), route('admin.taxonomies.edit', $taxonomy));
});

// Home > Taxons > [taxon] > Edit
Breadcrumbs::for('admin.taxons.edit', function (BreadcrumbsGenerator $trail, Taxon $taxon) {
    $trail->push(__('Taxons'), '#');
    $trail->push($taxon->name, '#');
    $trail->push(__('Chỉnh sửa'), route('admin.taxons.edit', $taxon));
});

// Home > Posts
Breadcrumbs::for('admin.posts.index', function (BreadcrumbsGenerator $trail) {
    $trail->parent('admin.dashboard');
    $trail->push(__('Bài viết'), route('admin.posts.index'), ['icon' => 'fal fa-edit']);
});

// Home > Posts > Create

Breadcrumbs::for('admin.posts.create', function (BreadcrumbsGenerator $trail) {
    $trail->parent('admin.posts.index');
    $trail->push(__('Tạo'), route('admin.posts.create'));
});

// Home > Posts > [admin] > Edit
Breadcrumbs::for('admin.posts.edit', function (BreadcrumbsGenerator $trail, Post $post) {
    $trail->parent('admin.posts.index');
    $trail->push($post->title, '#');
    $trail->push(__('Chỉnh sửa'), route('admin.posts.edit', $post));
});

/*
|--------------------------------------------------------------------------
| System Breadcrumbs
|--------------------------------------------------------------------------
*/

// Home > Admins
Breadcrumbs::for('admin.admins.index', function (BreadcrumbsGenerator $trail) {
    $trail->parent('admin.dashboard');
    $trail->push(__('Tài khoản'), route('admin.admins.index'), ['icon' => 'fal fa-user']);
});

// Home > Admins > Create

Breadcrumbs::for('admin.admins.create', function (BreadcrumbsGenerator $trail) {
    $trail->parent('admin.admins.index');
    $trail->push(__('Tạo'), route('admin.admins.create'));
});

// Home > Admins > [admin] > Edit
Breadcrumbs::for('admin.admins.edit', function (BreadcrumbsGenerator $trail, Admin $admin) {
    $trail->parent('admin.admins.index');
    $trail->push($admin->email, '#');
    $trail->push(__('Chỉnh sửa'), route('admin.admins.edit', $admin));
});

// Home > Roles
Breadcrumbs::for('admin.roles.index', function (BreadcrumbsGenerator $trail) {
    $trail->parent('admin.dashboard');
    $trail->push(__('Vai trò'), route('admin.roles.index'), ['icon' => 'fal fa-project-diagram']);
});

// Home > Roles > Create

Breadcrumbs::for('admin.roles.create', function (BreadcrumbsGenerator $trail) {
    $trail->parent('admin.roles.index');
    $trail->push(__('Tạo'), route('admin.roles.create'));
});

// Home > Roles > [role] > Edit
Breadcrumbs::for('admin.roles.edit', function (BreadcrumbsGenerator $trail, Role $role) {
    $trail->parent('admin.roles.index');
    $trail->push($role->display_name, '#');
    $trail->push(__('Chỉnh sửa'), route('admin.roles.edit', $role));
});

// Home > Pages
Breadcrumbs::for('admin.pages.index', function (BreadcrumbsGenerator $trail) {
    $trail->parent('admin.dashboard');
    $trail->push(__('Trang'), route('admin.pages.index'), ['icon' => 'fal fa-file']);
});

// Home > Pages > Create

Breadcrumbs::for('admin.pages.create', function (BreadcrumbsGenerator $trail) {
    $trail->parent('admin.pages.index');
    $trail->push(__('Tạo'), route('admin.pages.create'));
});

// Home > Admins > [admin] > Edit
Breadcrumbs::for('admin.pages.edit', function (BreadcrumbsGenerator $trail, Page $page) {
    $trail->parent('admin.pages.index');
    $trail->push(__('Chỉnh sửa'), route('admin.pages.edit', $page));
});

// Home > Admins > [admin] > Update
Breadcrumbs::for('admin.pages.update', function (BreadcrumbsGenerator $trail, Page $page) {
    $trail->parent('admin.pages.index');
    $trail->push(__('Cập nhật'), route('admin.pages.update', $page));
});


// Home > Banners
Breadcrumbs::for('admin.banners.index', function (BreadcrumbsGenerator $trail) {
    $trail->parent('admin.dashboard');
    $trail->push(__('Quảng Cáo'), route('admin.banners.index'), ['icon' => 'fal fa-image']);
});

// Home > Banners > Create

Breadcrumbs::for('admin.banners.create', function (BreadcrumbsGenerator $trail) {
    $trail->parent('admin.banners.index');
    $trail->push(__('Tạo'), route('admin.banners.create'));
});

// Home > Admins > [banner] > Edit
Breadcrumbs::for('admin.banners.edit', function (BreadcrumbsGenerator $trail, Banner $banner) {
    $trail->parent('admin.banners.index');
    $trail->push(__('Chỉnh sửa'), route('admin.banners.edit', $banner));
});

// Home > Admins > [banner] > Update
Breadcrumbs::for('admin.banners.update', function (BreadcrumbsGenerator $trail, Banner $banner) {
    $trail->parent('admin.banners.index');
    $trail->push(__('Cập nhật'), route('admin.banners.update', $banner));
});

// Home > Order
Breadcrumbs::for('admin.orders.index', function (BreadcrumbsGenerator $trail) {
    $trail->parent('admin.dashboard');
    $trail->push(__('Đơn hàng'), route('admin.orders.index'), ['icon' => 'fal fa-image']);
});

// Home > Order > Create

Breadcrumbs::for('admin.orders.create', function (BreadcrumbsGenerator $trail) {
    $trail->parent('admin.orders.index');
    $trail->push(__('Tạo'), route('admin.orders.create'));
});

// Home > Admins > [order] > Edit
Breadcrumbs::for('admin.orders.edit', function (BreadcrumbsGenerator $trail, Order $order) {
    $trail->parent('admin.orders.index');
    $trail->push(__('Chỉnh sửa'), route('admin.orders.edit', $order));
});

// Home > Admins > [order] > Update
Breadcrumbs::for('admin.orders.update', function (BreadcrumbsGenerator $trail, Order $order) {
    $trail->parent('admin.orders.index');
    $trail->push(__('Cập nhật'), route('admin.orders.update', $order));
});

// Home > [Setting] > Edit
Breadcrumbs::for('admin.settings.edit', function (BreadcrumbsGenerator $trail) {
    $trail->parent('admin.dashboard');
    $trail->push(__('Cài đặt chung'), route('admin.settings.edit'), ['icon' => 'fal fa-cog']);
});

// Home > Contacts
Breadcrumbs::for('admin.contacts.index', function (BreadcrumbsGenerator $trail) {
    $trail->parent('admin.dashboard');
    $trail->push(__('Liên hệ'), route('admin.contacts.index'), ['icon' => 'fal fa-phone']);
});
Breadcrumbs::for('admin.contacts.subscribe_email', function (BreadcrumbsGenerator $trail) {
    $trail->parent('admin.dashboard');
    $trail->push('Email đăng ký', route('admin.contacts.subscribe_email'), ['icon' => 'fal fa-envelope']);
});
Breadcrumbs::for('admin.contacts.search', function (BreadcrumbsGenerator $trail) {
    $trail->parent('admin.dashboard');
    $trail->push('Lịch sử tìm kiếm', route('admin.contacts.search'), ['icon' => 'fal fa-search']);
});

Breadcrumbs::for('admin.mail-settings.index', function (BreadcrumbsGenerator $trail) {
    $trail->parent('admin.dashboard');
    $trail->push('Chiến dịch gửi mail', route('admin.mail-settings.index'), ['icon' => 'fal fa-paper-plane']);
});

// Home > Log Activities
Breadcrumbs::for('admin.log-activities.index', function (BreadcrumbsGenerator $trail) {
    $trail->parent('admin.dashboard');
    $trail->push('Lịch sử thao tác ', route('admin.log-activities.index'), ['icon' => 'fal fa-history']);
});
Breadcrumbs::for('admin.log-activities.show', function (BreadcrumbsGenerator $trail, Activity $activity) {
    $trail->parent('admin.log-activities.index');
    $trail->push('Chi tiết thao tác', route('admin.log-activities.show', $activity));
});

// Home > Menu
Breadcrumbs::for('admin.menus.index', function (BreadcrumbsGenerator $trail) {
    $trail->parent('admin.dashboard');
    $trail->push('Quản lý menu', route('admin.menus.index'), ['icon' => 'fal fa-bars']);
});
Breadcrumbs::for('admin.menus.edit', function (BreadcrumbsGenerator $trail, Menu $menu) {
    $trail->parent('admin.menus.index');
    $trail->push('Chỉnh sửa', route('admin.menus.edit', $menu));
});

Breadcrumbs::for('admin.internal-links', function (BreadcrumbsGenerator $trail) {
    $trail->parent('admin.dashboard');
    $trail->push('Quản lý link nội tuyến', route('admin.internal-links'), ['icon' => 'fal fa-bars']);
});

Breadcrumbs::for('admin.internal-links.edit', function (BreadcrumbsGenerator $trail, InternalLink $internalLink) {
    $trail->parent('admin.internal-links');
    $trail->push('Chỉnh sửa', route('admin.internal-links.edit', $internalLink));
});

// Home > Pages
Breadcrumbs::for('admin.comments', function (BreadcrumbsGenerator $trail) {
    $trail->parent('admin.dashboard');
    $trail->push(__('Bình luận'), route('admin.comments'), ['icon' => 'fal fa-file']);
});

// Home > Pages > Create

// Breadcrumbs::for('admin.comments.create', function (BreadcrumbsGenerator $trail) {
//     $trail->parent('admin.pages.index');
//     $trail->push(__('Tạo'), route('admin.comments.create'));
// });

// // Home > Admins > [admin] > Edit
// Breadcrumbs::for('admin.comments.edit', function (BreadcrumbsGenerator $trail, Page $page) {
//     $trail->parent('admin.pages.index');
//     $trail->push(__('Chỉnh sửa'), route('admin.comments.edit', $page));
// });

// // Home > Admins > [admin] > Update
// Breadcrumbs::for('admin.comments.update', function (BreadcrumbsGenerator $trail, Page $page) {
//     $trail->parent('admin.comments.index');
//     $trail->push(__('Cập nhật'), route('admin.pages.update', $page));
// });


// Home > Group đăng ký
Breadcrumbs::for('admin.subs_group.index', function (BreadcrumbsGenerator $trail) {
    $trail->parent('admin.dashboard');
    $trail->push(__('Group đăng ký'), route('admin.subs_group.index'), ['icon' => 'fal fa-image']);
});

// Home > Group đăng ký > Create

Breadcrumbs::for('admin.subs_group.create', function (BreadcrumbsGenerator $trail) {
    $trail->parent('admin.subs_group.index');
    $trail->push(__('Tạo'), route('admin.subs_group.create'));
});

// Home > Admins > [banner] > Edit
Breadcrumbs::for('admin.subs_group.edit', function (BreadcrumbsGenerator $trail, \App\Models\SubscribeGroup $banner) {
    $trail->parent('admin.subs_group.index');
    $trail->push(__('Chỉnh sửa'), route('admin.subs_group.edit', $banner));
});

// Home > Admins > [banner] > Update
Breadcrumbs::for('admin.subs_group.update', function (BreadcrumbsGenerator $trail, \App\Models\SubscribeGroup $banner) {
    $trail->parent('admin.subs_group.index');
    $trail->push(__('Cập nhật'), route('admin.subs_group.update', $banner));
});
