<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $announcement = Announcement::query()
            ->live()
            ->latest('id')
            ->first();

        if (! $announcement) {
            $announcement = Announcement::query()
                ->where('is_active', true)
                ->latest('id')
                ->first();
        }

        if ($announcement && $announcement->media_type === 'youtube' && $announcement->youtube_url) {
            $announcement->youtube_embed_url = $this->toYoutubeEmbedUrl($announcement->youtube_url, $announcement->autoplay);
        }

        return view('pages.home', compact('announcement'));
    }

    protected function toYoutubeEmbedUrl(string $url, bool $autoplay): string
    {
        $videoId = null;
        $parts = parse_url($url);
        if (($parts['host'] ?? '') === 'youtu.be') {
            $videoId = ltrim((string) ($parts['path'] ?? ''), '/');
        }

        if (! $videoId && isset($parts['query'])) {
            parse_str($parts['query'], $query);
            $videoId = $query['v'] ?? null;
        }

        if (! $videoId) {
            return $url;
        }

        $params = $autoplay ? '?autoplay=1&mute=1&rel=0' : '?rel=0';

        return 'https://www.youtube.com/embed/'.$videoId.$params;
    }
}
