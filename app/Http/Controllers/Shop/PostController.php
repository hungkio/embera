<?php

namespace App\Http\Controllers\Shop;

use App\Domain\Post\Models\Post;
use App\Domain\Taxonomy\Models\Taxon;
use App\Enums\PostState;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Spatie\SchemaOrg\Schema;

class PostController extends Controller
{
    public function index()
    {
        if (request('category')){

            $taxons = self::taxons();
            $postNews = self::newEstPost();

            $category = Taxon::whereSlug(request('category'))->with(['ancestors' => function ($sub) {
                $sub->whereNotNull('parent_id')->breadthFirst();
            }])->firstOrFail();

            $posts = Post::whereHas('taxons', function($q) use($category){
                $q->where('id', $category->id);
            })->where('status', PostState::Active)->latest()->paginate(10);
            return view('shop.post.index', compact('posts', 'category', 'taxons', 'postNews'));
        }
        abort(404);
    }

    public function show(Post $post)
    {
        $post->increment('view');
        $taxons = self::taxons();
        $postNews = self::newEstPost();
        $relatedPosts = collect([]);
        $postSchemaMarkup = $this->schemaMarkup($post);
        if (! empty($post->related_posts)) {
            $relatedPosts = Post::query()
                ->whereIntegerInRaw('id', $post->related_posts)
                ->get();
        }

        return view('shop.post.show', compact('post', 'postNews', 'taxons', 'relatedPosts', 'postSchemaMarkup'));
    }

    public function taxons(){
        $rootTaxon = Taxon::whereTaxonomyId(setting('post_taxonomy', 1))->whereNull('parent_id')->first();
        if (empty($rootTaxon)) {
            return [];
        }

        return Taxon::where('parent_id', $rootTaxon->id)
            ->ordered()
            ->with(['media', 'childs' => function ($q) {
                $q->with(['childs' => function ($sub) {
                    $sub->with('media');
                }]);
            }])->get();
    }

    public function newEstPost(){
        $postNews = Post::where('status', PostState::Active)->take(4)->get();
        return $postNews;
    }

    public function schemaMarkup($post)
    {
        return Schema::article()
            ->url(route('post.show', $post->slug))
            ->author($post->user->fullname ?? null)
            ->mainEntityOfPage(
                Schema::WebPage()
                    ->id(route('post.show', $post->slug))
            )
            ->image(
                Schema::imageObject()
                    ->url($post->getFirstMediaUrl('image'))
                    ->width('700')
                    ->height('438')
            )
            ->description($post->description)
            ->datePublished($post->created_at)
            ->dateModified($post->created_at)
            ->publisher(
                Schema::Organization()
                    ->name(config('app.url'))
                    ->logo(
                        Schema::imageObject()
                            ->url(\Storage::url(setting('store_logo')))
                            ->width('60')
                            ->height('60')
                    )
            );
    }

}
