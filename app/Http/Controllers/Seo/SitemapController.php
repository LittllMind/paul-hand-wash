<?php

namespace App\Http\Controllers\Seo;

use App\Http\Controllers\Controller;
use App\Models\Lieu;
use App\Models\Presence;
use App\Models\Evenement;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;
use Illuminate\Support\Facades\URL as LaravelUrl;

class SitemapController extends Controller
{
    /**
     * Generate and return the sitemap XML.
     */
    public function index()
    {
        $sitemap = Sitemap::create();

        // Add static pages
        $sitemap->add(
            Url::create('/')
                ->setPriority(1.0)
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                ->setLastModificationDate(now())
        );

        $sitemap->add(
            Url::create('/reserver')
                ->setPriority(0.9)
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY)
                ->setLastModificationDate(now())
        );

        // Add dynamic pages - Presences (reservation slots)
        $presences = Presence::with('lieu')->where('date', '>=', now())->get();
        foreach ($presences as $presence) {
            $sitemap->add(
                Url::create(route('reserver.show', $presence->id, false))
                    ->setPriority(0.8)
                    ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                    ->setLastModificationDate($presence->updated_at ?? now())
            );
        }

        // Add confirmation pages (lower priority)
        $sitemap->add(
            Url::create('/reservation/confirmation')
                ->setPriority(0.5)
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_NEVER)
        );

        // Payment pages
        $sitemap->add(
            Url::create('/payment/success')
                ->setPriority(0.3)
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_NEVER)
        );
        
        $sitemap->add(
            Url::create('/payment/cancel')
                ->setPriority(0.3)
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_NEVER)
        );

        return response($sitemap->render(), 200)
            ->header('Content-Type', 'text/xml; charset=UTF-8');
    }
}
