<?php

namespace App\Http\Controllers\Shop;

use App\Domain\Page\Models\Page;
use App\Http\Controllers\Controller;
use Spatie\SchemaOrg\Schema;

class PageController extends Controller
{
    public function show(Page $page)
    {
        $page->increment('view');
        $pageSchemaMarkup = $this->schemaMarkup($page);
        return view('shop.page.show', compact('page', 'pageSchemaMarkup'));
    }

    public function schemaMarkup($page)
    {
        return Schema::webPage()
            ->url(route('page.show', $page->slug))
            ->name($page->title)
            ->description($page->meta_description)
            ->author($page->user->fullname ?? null);
    }
}
