<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;
use App\Domain\Banner\Models\Banner;
use App\Domain\Contact\Models\Contact;
use App\Domain\LogSearch\Models\LogSearch;
use App\Domain\Page\Models\Page;
use App\Domain\Post\Models\Post;
use App\Domain\SubscribeEmail\Models\SubscribeEmail;
use App\Domain\Taxonomy\Models\Taxonomy;

class DashboardController
{
    public function index()
    {
        $totalTaxonomy = Taxonomy::count();
        $totalPages = Page::count();
        $totalPosts = Post::count();
        $totalContacts = Contact::count();
        $totalBanners = Banner::count();
        $totalSearchs = LogSearch::count();
        $totalSubscribeEmails = SubscribeEmail::count();

        $pageTops = Page::orderBy('view', 'desc')->take(10)->get();
        $postTops = Post::orderBy('view', 'desc')->take(10)->get();

        return view('admin.dashboards.dashboard', compact( 'totalPosts', 'totalContacts', 'totalTaxonomy', 'totalPages', 'totalBanners', 'postTops', 'pageTops', 'totalSearchs', 'totalSubscribeEmails'));
    }

    public function genSiteMap()
    {
        $baseUrl = 'https://demo.kqbd.ai';

        $files = ['general.xml'];
        $files = array_merge($files, $this->genModel('App\Models\Country', '-football/', 'sitemap_country'));
        $files = array_merge($files, $this->genModel('App\Models\League', '-league/', 'sitemap_league'));
        $files = array_merge($files, $this->genModel('App\Models\Team', '', 'sitemap_team'));
        $files = array_merge($files, $this->genModel('App\Models\Country', '-football/national-team?', 'sitemap_nation'));
        $this->genGeneral();

        $sitemapIndex = '<?xml version="1.0" encoding="UTF-8"?>';
        $sitemapIndex .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

        foreach ($files as $file) {
            $sitemapIndex .= '<sitemap>';
            $sitemapIndex .= '<loc>' . $baseUrl . '/' .$file . '</loc>';
            $sitemapIndex .= '<lastmod>' . now()->tz('UTC')->toAtomString() . '</lastmod>';
            $sitemapIndex .= '</sitemap>';
        }

        $sitemapIndex .= '</sitemapindex>';

        file_put_contents(public_path("sitemap.xml"), $sitemapIndex);
    }

    public function genModel($model, $suffix, $filename)
    {
        $file = [];
        $chunkCount = 0;
        $model::chunk(100, function($countries) use (&$chunkCount, $suffix, $filename, &$file) {
            $chunkCount++;
            // Create XML content for the sitemap
            $sitemap = '<?xml version="1.0" encoding="UTF-8"?>';
            $sitemap .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

            // Add the team URLs
            foreach ($countries as $country) {
                if ($filename != 'sitemap_nation') {
                    if ($filename == 'sitemap_team') {
                        $sitemap .= $this->addLink( $country->slug . $suffix . '/club');
                    } else {
                        $sitemap .= $this->addLink( $country->slug . $suffix);
                    }
                }

                $sitemap .= $this->addLink( $country->slug . $suffix . 'standings');
                $sitemap .= $this->addLink( $country->slug . $suffix . 'top-scorers');
                $sitemap .= $this->addLink( $country->slug . $suffix . 'results');
                $sitemap .= $this->addLink( $country->slug . $suffix . 'fixtures');
                $sitemap .= $this->addLink( $country->slug . $suffix . 'livescore');
                $sitemap .= $this->addLink( $country->slug . $suffix . 'betting-odds');
                $sitemap .= $this->addLink( $country->slug . $suffix . 'national-team');

                if ($filename == 'sitemap_country' || $filename == 'sitemap_league') {
                    $sitemap .= $this->addLink($country->slug . $suffix . 'predictions');
                }

                if ($filename == 'sitemap_country') {
                    $sitemap .= $this->addLink($country->slug . $suffix . 'analysis');
                }
            }

            $sitemap .= '</urlset>';

            $file[] = "$filename$chunkCount.xml";
            file_put_contents(public_path("$filename$chunkCount.xml"), $sitemap);
        });

        return $file;
    }

    public function genGeneral()
    {
        $sitemap = '<?xml version="1.0" encoding="UTF-8"?>';
        $sitemap .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

        // Add the team URLs
        $sitemap .= $this->addLink('club');
        $sitemap .= $this->addLink('results');
        $sitemap .= $this->addLink('standings');
        $sitemap .= $this->addLink('top-scorers');
        $sitemap .= $this->addLink('fixtures');
        $sitemap .= $this->addLink('livescore');
        $sitemap .= $this->addLink('betting-odds');
        $sitemap .= $this->addLink('predictions');
        $sitemap .= $this->addLink('analysis');
        $sitemap .= $this->addLink('premium-tips');
        $sitemap .= $this->addLink('dropping-odds');
        $sitemap .= $this->addLink('fifa-rankings');
        $sitemap .= $this->addLink('rss');
        $sitemap .= $this->addLink('login');
        $sitemap .= $this->addLink('log-out');
        $sitemap .= $this->addLink('sign-up');
        $sitemap .= $this->addLink('favourite');
        $sitemap .= $this->addLink('404-not-found');
        $sitemap .= $this->addLink('about-us');
        $sitemap .= $this->addLink('terms-of-use');
        $sitemap .= $this->addLink('privacy-policy');
        $sitemap .= $this->addLink('feedback');
        $sitemap .= $this->addLink('link-exchange');
        $sitemap .= $this->addLink('ads');

        $sitemap .= '</urlset>';

        file_put_contents(public_path("general.xml"), $sitemap);
    }

    public function addLink($slug) {
        $baseUrl = 'https://demo.kqbd.ai';

        $sitemap = '<url>';
        $sitemap .= '<loc>' . $baseUrl . '/'. $slug . '</loc>';
        $sitemap .= '<lastmod>' . now() . '</lastmod>';
        $sitemap .= '<changefreq>weekly</changefreq>';
        $sitemap .= '<priority>0.8</priority>';
        $sitemap .= '</url>';

        return $sitemap;
    }
}
