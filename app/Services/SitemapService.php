<?php

namespace App\Services;

use App\Models\Article;
use App\Models\News;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;
use Illuminate\Support\Facades\Storage;

class SitemapService
{
     private function getFirstImageFromContent($content, $defaultImageUrl)
    {
        preg_match('/<img[^>]+src="([^">]+)"/', $content, $matches);
        return $matches[1] ?? $defaultImageUrl;
    }

    public function generateArticlesSitemap(string $database)
    {
        $sitemap = Sitemap::create();
        $defaultImageUrl = asset('assets/img/front-pages/icons/articles_default_image.jpg');

        $articles = Article::on($database)->get();

        foreach ($articles as $article) {
            $url = Url::create(route('frontend.articles.show', ['database' => $database, 'article' => $article->id]))
                ->setLastModificationDate($article->updated_at)
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                ->setPriority(0.8);

             $imageUrl = $article->image_url ?? $this->getFirstImageFromContent($article->content, $defaultImageUrl);
            $altText = $article->alt ?? $article->title;

             if ($imageUrl) {
                $url->addImage($imageUrl, $altText);
            }

            $sitemap->add($url);
        }

         $fileName = "sitemaps/sitemap_articles_{$database}.xml";
        Storage::disk('public')->put($fileName, $sitemap->render());
    }

    public function generateNewsSitemap(string $database)
    {
        $sitemap = Sitemap::create();
        $defaultImageUrl = asset('assets/img/front-pages/icons/news_default_image.jpg');

        // Fetch news items based on the selected database
        $newsItems = News::on($database)->get();

        foreach ($newsItems as $news) {
            // Create the URL for the news item
            $url = Url::create(route('content.frontend.news.show', ['database' => $database, 'id' => $news->id]))
                ->setLastModificationDate($news->updated_at)
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY)
                ->setPriority(0.8);

            // Add image to sitemap if available
            $imageUrl = $news->image_url ?? $this->getFirstImageFromContent($news->content, $defaultImageUrl);
            $altText = $news->alt ?? $news->title;

            if ($imageUrl) {
                $url->addImage($imageUrl, $altText);
            }

            $sitemap->add($url);
        }

        // Save the sitemap
        $fileName = "sitemaps/sitemap_news_{$database}.xml";
        Storage::disk('public')->put($fileName, $sitemap->render());
    }


    public function generateSitemap()
    {
        Sitemap::create()
            ->add(Url::create('/')->setPriority(1.0)->setChangeFrequency('daily'))
            ->add(Url::create('/about')->setPriority(0.8))
            ->writeToFile(public_path('sitemap.xml'));

        return response()->json(['success' => true, 'message' => 'Sitemap generated successfully']);
    }
}
