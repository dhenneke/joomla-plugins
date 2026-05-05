<?php

defined('_JEXEC') or die;

use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\Filesystem\Path;
use Joomla\CMS\Factory;
use Joomla\CMS\Http\HttpFactory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Uri\Uri;
use Joomla\Registry\Registry;

class SppagebuilderAddonYoutube_embedder extends SppagebuilderAddons
{
    private ?int $cacheDurationSeconds = null;

    private $httpClient = null;

    private const WATCH_URL_TEMPLATE = 'https://www.youtube.com/watch?v=%s';

    private const CSS_FILE = 'media/plg_sppagebuilder_youtube_gdpr/assets/css/youtube_embedder.css';

    public function render(): string
    {
        $this->loadPluginLanguage();

        $youtubeUrl = $this->getSetting('youtube_url');
        $title = trim(strip_tags($this->getSetting('title')));

        if ($youtubeUrl === '') {
            return '';
        }

        $videoId = $this->extractYoutubeVideoId($youtubeUrl);
        if ($videoId === '') {
            return '<div class="ytgdpr ytgdpr-error">' . Text::_('PLG_SPPAGEBUILDER_YOUTUBE_GDPR_INVALID_URL') . '</div>';
        }

        $watchUrl = sprintf(self::WATCH_URL_TEMPLATE, rawurlencode($videoId));
        $thumbnailUrl = $this->getCachedThumbnailUrl($videoId);
        $videoMeta = $this->getCachedVideoMeta($videoId);

        $metaTitle = !empty($videoMeta['title']) ? (string) $videoMeta['title'] : ($title !== '' ? $title : Text::_('PLG_SPPAGEBUILDER_YOUTUBE_GDPR_DEFAULT_TITLE'));
        $metaChannel = !empty($videoMeta['author_name']) ? (string) $videoMeta['author_name'] : Text::_('PLG_SPPAGEBUILDER_YOUTUBE_GDPR_DEFAULT_CHANNEL');
        $avatarLabel = strtoupper(substr($metaChannel, 0, 1));
        if ($avatarLabel === '') {
            $avatarLabel = 'Y';
        }

        $safeWatchUrl = $this->esc($watchUrl);
        $safeThumbnailUrl = $this->esc($thumbnailUrl);
        $safeMetaTitle = $this->esc($metaTitle);
        $safeMetaChannel = $this->esc($metaChannel);
        $safeAvatarLabel = $this->esc($avatarLabel);

        if ($thumbnailUrl !== '') {
            return '<div class="ytgdpr">'
                . '<a class="ytgdpr-link" href="' . $safeWatchUrl . '" target="_blank" rel="noopener noreferrer nofollow">'
                . '<img class="ytgdpr-thumb" src="' . $safeThumbnailUrl . '" alt="' . $safeMetaTitle . '" loading="lazy" />'
                . '<div class="ytgdpr-meta">'
                . '<span class="ytgdpr-avatar" aria-hidden="true">' . $safeAvatarLabel . '</span>'
                . '<span class="ytgdpr-meta-text">'
                . '<span class="ytgdpr-meta-title">' . $safeMetaTitle . '</span>'
                . '<span class="ytgdpr-meta-channel">' . $safeMetaChannel . '</span>'
                . '</span>'
                . '</div>'
                . '<span class="ytgdpr-play" aria-hidden="true"><span class="ytgdpr-play-triangle"></span></span>'
                . '<span class="ytgdpr-watch">' . Text::_('PLG_SPPAGEBUILDER_YOUTUBE_GDPR_WATCH_CTA') . '</span>'
                . '</a>'
                . '</div>';
        }

        return '<div class="ytgdpr">'
            . '<div class="ytgdpr-placeholder" role="status" aria-live="polite">'
            . '<span class="ytgdpr-placeholder-text">' . Text::sprintf('PLG_SPPAGEBUILDER_YOUTUBE_GDPR_THUMBNAIL_UNAVAILABLE', $safeMetaTitle) . '</span>'
            . '<a class="ytgdpr-fallback-link" href="' . $safeWatchUrl . '" target="_blank" rel="noopener noreferrer nofollow">' . Text::_('PLG_SPPAGEBUILDER_YOUTUBE_GDPR_PLACEHOLDER_CTA') . '</a>'
            . '</div>'
            . '</div>';
    }

    public function stylesheets(): array
    {
        return [
            Uri::root() . self::CSS_FILE,
        ];
    }

    private function esc(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }

    private function getSetting(string $name, string $default = ''): string
    {
        if (isset($this->addon->settings->{$name}) && $this->addon->settings->{$name} !== '') {
            return (string) $this->addon->settings->{$name};
        }

        return $default;
    }

    private function extractYoutubeVideoId(string $url): string
    {
        $url = trim((string) $url);
        if ($url === '') {
            return '';
        }

        if (preg_match('/^[a-zA-Z0-9_-]{11}$/', $url)) {
            return $url;
        }

        $parts = parse_url($url);
        if (!is_array($parts) || empty($parts['host'])) {
            return '';
        }

        $host = strtolower($parts['host']);
        $path = isset($parts['path']) ? trim($parts['path'], '/') : '';

        if ($host === 'youtu.be') {
            $segments = explode('/', $path);
            $candidate = isset($segments[0]) ? $segments[0] : '';
            return preg_match('/^[a-zA-Z0-9_-]{11}$/', $candidate) ? $candidate : '';
        }

        $allowedYouTubeHosts = ['youtube.com', 'www.youtube.com', 'm.youtube.com'];
        if (in_array($host, $allowedYouTubeHosts, true)) {
            if (!empty($parts['query'])) {
                parse_str($parts['query'], $query);
                if (!empty($query['v']) && preg_match('/^[a-zA-Z0-9_-]{11}$/', $query['v'])) {
                    return $query['v'];
                }
            }

            $segments = explode('/', $path);
            if (count($segments) >= 2 && in_array($segments[0], ['embed', 'shorts', 'live'], true)) {
                $candidate = $segments[1];
                return preg_match('/^[a-zA-Z0-9_-]{11}$/', $candidate) ? $candidate : '';
            }
        }

        return '';
    }

    private function getCacheDurationSeconds(): int
    {
        if ($this->cacheDurationSeconds !== null) {
            return $this->cacheDurationSeconds;
        }

        $cacheDuration = 3600;
        $plugin = PluginHelper::getPlugin('sppagebuilder', 'youtube_gdpr');

        if ($plugin && isset($plugin->params)) {
            $params = new Registry($plugin->params);
            $configured = (int) $params->get('cache_duration', 3600);
            if ($configured > 0) {
                $cacheDuration = $configured;
            }
        }

        $this->cacheDurationSeconds = $cacheDuration;

        return $this->cacheDurationSeconds;
    }

    private function getCacheFolderPath(): string
    {
        return Path::clean(JPATH_ROOT . '/cache/ytgdpr_thumbnails');
    }

    private function getCacheFolderUrl(): string
    {
        return rtrim(Uri::root(), '/') . '/cache/ytgdpr_thumbnails';
    }

    private function getCachedThumbnailUrl(string $videoId): string
    {
        $cachePath = $this->getCacheFolderPath();
        $cacheUrl = $this->getCacheFolderUrl();
        $cacheDuration = $this->getCacheDurationSeconds();
        $fileName = 'yt_' . $videoId . '.jpg';
        $filePath = $cachePath . '/' . $fileName;
        $fileUrl = $cacheUrl . '/' . rawurlencode($fileName);

        if (!$this->ensureCacheFolder()) {
            return '';
        }

        if (File::exists($filePath)) {
            if (!$this->isUsableImageFile($filePath)) {
                File::delete($filePath);
            }
        }

        if (File::exists($filePath) && $this->isFresh($filePath, $cacheDuration)) {
            return $fileUrl;
        }

        if (!$this->downloadThumbnailToFile($videoId, $filePath)) {
            if (File::exists($filePath) && $this->isUsableImageFile($filePath)) {
                return $fileUrl;
            }

            return '';
        }

        clearstatcache(true, $filePath);
        if (!$this->isUsableImageFile($filePath)) {
            if (File::exists($filePath)) {
                File::delete($filePath);
            }

            return '';
        }

        return $fileUrl;
    }

    private function getCachedVideoMeta(string $videoId): array
    {
        $cacheDuration = $this->getCacheDurationSeconds();
        $metaPath = $this->getCacheFolderPath() . '/meta_' . $videoId . '.json';

        if (!$this->ensureCacheFolder()) {
            return [];
        }

        if (File::exists($metaPath) && $this->isFresh($metaPath, $cacheDuration)) {
            $cached = json_decode((string) @file_get_contents($metaPath), true);
            if (!empty($cached['title'])) {
                return is_array($cached) ? $cached : [];
            }
        }

        $fetched = $this->fetchVideoMeta($videoId);
        if (!empty($fetched['title'])) {
            $json = json_encode($fetched, JSON_UNESCAPED_SLASHES);
            if ($json !== false) {
                File::write($metaPath, $json);
            }
            return $fetched;
        }

        if (File::exists($metaPath)) {
            $cached = json_decode((string) @file_get_contents($metaPath), true);
            return is_array($cached) ? $cached : [];
        }

        return [];
    }

    private function fetchVideoMeta(string $videoId): array
    {
        $watchUrl = sprintf(self::WATCH_URL_TEMPLATE, rawurlencode($videoId));
        $endpoint = 'https://www.youtube.com/oembed?url=' . rawurlencode($watchUrl) . '&format=json';

        try {
            $response = $this->getHttpClient()->get(
                $endpoint,
                ['Accept' => 'application/json', 'User-Agent' => 'Mozilla/5.0 (compatible; ytgdpr-meta-fetcher/1.0)'],
                10
            );
            if ((int) $response->code !== 200) {
                return [];
            }

            $payload = json_decode((string) $response->body, true);
            if (!is_array($payload)) {
                return [];
            }

            $result = [];
            if (!empty($payload['title'])) {
                $result['title'] = trim((string) $payload['title']);
            }
            if (!empty($payload['author_name'])) {
                $result['author_name'] = trim((string) $payload['author_name']);
            }
            if (!empty($payload['author_url'])) {
                $result['author_url'] = trim((string) $payload['author_url']);
            }
            if (!empty($payload['provider_name'])) {
                $result['provider_name'] = trim((string) $payload['provider_name']);
            }

            if (!empty($result['title'])) {
                return $result;
            }
        } catch (\Throwable $e) {
            return [];
        }

        return [];
    }

    private function downloadThumbnailToFile(string $videoId, string $filePath): bool
    {
        $sources = [
            'https://i.ytimg.com/vi/' . rawurlencode($videoId) . '/maxresdefault.jpg',
            'https://i.ytimg.com/vi/' . rawurlencode($videoId) . '/hqdefault.jpg',
            'https://i.ytimg.com/vi/' . rawurlencode($videoId) . '/mqdefault.jpg',
            'https://i.ytimg.com/vi/' . rawurlencode($videoId) . '/default.jpg',
        ];

        try {
            foreach ($sources as $sourceUrl) {
                $response = $this->getHttpClient()->get(
                    $sourceUrl,
                    ['Accept' => 'image/*', 'User-Agent' => 'Mozilla/5.0 (compatible; ytgdpr-thumbnail-fetcher/1.0)'],
                    10
                );
                if ((int) $response->code !== 200) {
                    continue;
                }

                $body = (string) $response->body;
                if (!$this->isImageResponse($response, $body)) {
                    continue;
                }

                if (File::write($filePath, $body)) {
                    return true;
                }
            }
        } catch (\Throwable $e) {
            return false;
        }

        return false;
    }

    private function isUsableImageFile(string $filePath): bool
    {
        if (!File::exists($filePath)) {
            return false;
        }

        $size = @filesize($filePath);
        if (!$size || $size < 1000) {
            return false;
        }

        $imageInfo = @getimagesize($filePath);
        return is_array($imageInfo) && !empty($imageInfo[0]) && !empty($imageInfo[1]);
    }

    private function isImageResponse($response, string $body): bool
    {
        if ($body === '' || strlen($body) < 1000) {
            return false;
        }

        $contentType = '';
        if (isset($response->headers)) {
            if (is_object($response->headers) && method_exists($response->headers, 'get')) {
                $contentType = (string) $response->headers->get('Content-Type');
            } elseif (is_array($response->headers) && isset($response->headers['Content-Type'])) {
                $contentType = (string) $response->headers['Content-Type'];
            }
        }

        if ($contentType !== '' && stripos($contentType, 'image/') === false) {
            return false;
        }

        $signature = substr($body, 0, 12);
        $isJpeg = substr($signature, 0, 2) === "\xFF\xD8";
        $isPng = substr($signature, 0, 8) === "\x89PNG\x0D\x0A\x1A\x0A";
        $isWebp = substr($signature, 0, 4) === 'RIFF' && substr($signature, 8, 4) === 'WEBP';

        return $isJpeg || $isPng || $isWebp;
    }

    private function getHttpClient()
    {
        if ($this->httpClient === null) {
            $this->httpClient = HttpFactory::getHttp();
        }

        return $this->httpClient;
    }

    private function loadPluginLanguage(): void
    {
        $language = Factory::getApplication()->getLanguage();

        $language->load('plg_sppagebuilder_youtube_gdpr', JPATH_ADMINISTRATOR)
            || $language->load('plg_sppagebuilder_youtube_gdpr', JPATH_SITE)
            || $language->load('plg_sppagebuilder_youtube_gdpr', JPATH_PLUGINS . '/sppagebuilder/youtube_gdpr');
    }

    private function ensureCacheFolder(): bool
    {
        $cachePath = $this->getCacheFolderPath();

        return Folder::exists($cachePath) || Folder::create($cachePath);
    }

    private function isFresh(string $filePath, int $cacheDuration): bool
    {
        $modified = @filemtime($filePath);

        return (bool) $modified && (time() - (int) $modified) < $cacheDuration;
    }
}
