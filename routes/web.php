<?php

use App\Http\Controllers\Shop\ContactController;
use App\Http\Controllers\Shop\HomeController;
use App\Http\Controllers\Shop\PageController;
use App\Http\Controllers\Shop\PostController;
use Illuminate\Support\Facades\Route;
use Spatie\Sitemap\SitemapGenerator;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
//SitemapGenerator::create(config('app.url')->writeToFile(public_path('sitemap.xml'));

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/tim-kiem', [HomeController::class, 'search'])->name('search');

Route::get('/lien-he', [ContactController::class, 'index'])->name('page.contact');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');
Route::post('/contact-email', [ContactController::class, 'subscribeEmail'])->name('contact.subscribe_email');

Auth::routes();

Route::get('/bai-viet', [PostController::class, 'index'])->name('post.index');
Route::get('/bai-viet/{post:slug?}', [PostController::class, 'show'])->name('post.show');

Route::get('/trang/{page:slug?}', [PageController::class, 'show'])->name('page.show');
Route::group(['prefix' => 'laravel-filemanager', 'middleware' => ['web', 'auth']], function () {
    \UniSharp\LaravelFilemanager\Lfm::routes();
});
