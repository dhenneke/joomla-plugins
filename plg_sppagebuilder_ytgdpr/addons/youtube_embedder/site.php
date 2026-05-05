<?php

//no direct accees
defined('_JEXEC') or die('Restricted Aceess');

jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.path');

class SppagebuilderAddonYoutube_embedder extends SppagebuilderAddons
{

    public function render()
    {
        $youtubeUrl = $this->getSetting('youtube_url', '');
        $title = $this->getSetting('title', '');
        $title = trim(strip_tags((string) $title));

        if ($youtubeUrl === '') {
            return '';
        }

        $videoId = $this->extractYoutubeVideoId($youtubeUrl);
        if ($videoId === '') {
            return '<div class="ytgdpr ytgdpr-error">Invalid YouTube URL.</div>';
        }

        $watchUrl = 'https://www.youtube.com/watch?v=' . rawurlencode($videoId);
        $thumbnailUrl = $this->getCachedThumbnailUrl($videoId);
        $videoMeta = $this->getCachedVideoMeta($videoId, $watchUrl);

        $metaTitle = '';
        if (!empty($videoMeta['title'])) {
            $metaTitle = (string) $videoMeta['title'];
        } elseif ($title !== '') {
            $metaTitle = $title;
        } else {
            $metaTitle = 'YouTube video';
        }

        $metaChannel = !empty($videoMeta['author_name']) ? (string) $videoMeta['author_name'] : 'YouTube';
        $avatarLabel = strtoupper(substr($metaChannel, 0, 1));
        if ($avatarLabel === '') {
            $avatarLabel = 'Y';
        }

        $safeTitle = htmlspecialchars($metaTitle, ENT_QUOTES, 'UTF-8');
        $safeWatchUrl = htmlspecialchars($watchUrl, ENT_QUOTES, 'UTF-8');
        $safeThumbnailUrl = htmlspecialchars($thumbnailUrl, ENT_QUOTES, 'UTF-8');
        $safeMetaTitle = htmlspecialchars($metaTitle, ENT_QUOTES, 'UTF-8');
        $safeMetaChannel = htmlspecialchars($metaChannel, ENT_QUOTES, 'UTF-8');
        $safeAvatarLabel = htmlspecialchars($avatarLabel, ENT_QUOTES, 'UTF-8');

        $output = '';
        $output .= '<div class="ytgdpr">';

        if ($thumbnailUrl !== '') {
            $output .= '<a class="ytgdpr-link" href="' . $safeWatchUrl . '" target="_blank" rel="noopener noreferrer nofollow">';
            $output .= '<img class="ytgdpr-thumb" src="' . $safeThumbnailUrl . '" alt="' . $safeTitle . '" loading="lazy" />';
            $output .= '<div class="ytgdpr-meta">';
            $output .= '<span class="ytgdpr-avatar" aria-hidden="true">' . $safeAvatarLabel . '</span>';
            $output .= '<span class="ytgdpr-meta-text">';
            $output .= '<span class="ytgdpr-meta-title">' . $safeMetaTitle . '</span>';
            $output .= '<span class="ytgdpr-meta-channel">' . $safeMetaChannel . '</span>';
            $output .= '</span>';
            $output .= '</div>';
            $output .= '<span class="ytgdpr-play" aria-hidden="true"><span class="ytgdpr-play-triangle"></span></span>';
            $output .= '<span class="ytgdpr-watch">Ansehen auf YouTube</span>';
            $output .= '</a>';
        } else {
            $output .= '<div class="ytgdpr-placeholder" role="status" aria-live="polite">';
            $output .= '<span class="ytgdpr-placeholder-text">Thumbnail unavailable for ' . $safeMetaTitle . '</span>';
            $output .= '<a class="ytgdpr-fallback-link" href="' . $safeWatchUrl . '" target="_blank" rel="noopener noreferrer nofollow">Open on YouTube</a>';
            $output .= '</div>';
        }

        $output .= '</div>';

        return $output;
    }

    public function css()
    {
        $css = '';
        $css .= '.ytgdpr{max-width:100%;}';
        $css .= '.ytgdpr-link{position:relative;display:block;border-radius:10px;overflow:hidden;text-decoration:none;line-height:0;}';
        $css .= '.ytgdpr-thumb{display:block;width:100%;height:auto;aspect-ratio:16/9;object-fit:cover;}';
        $css .= '.ytgdpr-meta{position:absolute;left:12px;top:12px;right:12px;display:flex;align-items:center;gap:10px;';
        $css .= 'padding:8px 10px;background:linear-gradient(180deg,rgba(0,0,0,.72),rgba(0,0,0,.35));border-radius:10px;line-height:1.2;}';
        $css .= '.ytgdpr-avatar{width:34px;height:34px;border-radius:999px;background:rgba(255,255,255,.9);';
        $css .= 'color:#111;display:inline-flex;align-items:center;justify-content:center;font-weight:700;font-size:14px;flex:0 0 34px;}';
        $css .= '.ytgdpr-meta-text{display:flex;flex-direction:column;min-width:0;}';
        $css .= '.ytgdpr-meta-title{font-size:18px;font-weight:700;color:#fff;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;line-height:1.15;}';
        $css .= '.ytgdpr-meta-channel{font-size:13px;color:rgba(255,255,255,.9);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}';
        $css .= '.ytgdpr-play{position:absolute;left:50%;top:50%;transform:translate(-50%,-50%);';
        $css .= 'width:72px;height:50px;border-radius:14px;background:#ff0033;display:flex;align-items:center;justify-content:center;';
        $css .= 'box-shadow:0 8px 24px rgba(0,0,0,.35);}';
        $css .= '.ytgdpr-play-triangle{display:block;width:0;height:0;border-top:11px solid transparent;border-bottom:11px solid transparent;';
        $css .= 'border-left:18px solid #fff;margin-left:4px;}';
        $css .= '.ytgdpr-watch{position:absolute;right:12px;bottom:12px;background:rgba(0,0,0,.68);color:#fff;';
        $css .= 'padding:10px 14px;border-radius:999px;font-size:14px;line-height:1;font-weight:600;}';
        $css .= '.ytgdpr-placeholder{display:flex;flex-direction:column;align-items:center;justify-content:center;gap:10px;';
        $css .= 'min-height:180px;padding:24px;border:1px dashed #aaa;border-radius:10px;background:#f5f5f5;text-align:center;}';
        $css .= '.ytgdpr-placeholder-text{font-weight:600;color:#444;line-height:1.4;}';
        $css .= '.ytgdpr-fallback-link{display:inline-block;padding:8px 12px;border-radius:6px;background:#111;color:#fff;text-decoration:none;line-height:1.2;}';
        $css .= '.ytgdpr-error{padding:10px 12px;border:1px solid #d9534f;color:#d9534f;border-radius:6px;}';

        return $css;
    }

    private function getSetting($name, $default = '')
    {
        if (isset($this->addon->settings->{$name}) && $this->addon->settings->{$name} !== '') {
            return (string) $this->addon->settings->{$name};
        }

        return (string) $default;
    }

    private function extractYoutubeVideoId($url)
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

        if (strpos($host, 'youtube.com') !== false) {
            if (!empty($parts['query'])) {
                parse_str($parts['query'], $query);
                if (!empty($query['v']) && preg_match('/^[a-zA-Z0-9_-]{11}$/', $query['v'])) {
                    return $query['v'];
                }
            }

            $segments = explode('/', $path);
            if (count($segments) >= 2 && in_array($segments[0], array('embed', 'shorts', 'live'), true)) {
                $candidate = $segments[1];
                return preg_match('/^[a-zA-Z0-9_-]{11}$/', $candidate) ? $candidate : '';
            }
        }

        return '';
    }

    private function getCacheDurationSeconds()
    {
        $cacheDuration = 3600;
        $plugin = JPluginHelper::getPlugin('sppagebuilder', 'youtube_gdpr');

        if ($plugin && isset($plugin->params)) {
            $params = new JRegistry($plugin->params);
            $configured = (int) $params->get('cache_duration', 3600);
            if ($configured > 0) {
                $cacheDuration = $configured;
            }
        }

        return $cacheDuration;
    }

    private function getCacheFolderPath()
    {
        return JPath::clean(JPATH_ROOT . '/cache/ytgdpr_thumbnails');
    }

    private function getCacheFolderUrl()
    {
        return rtrim(JURI::root(), '/') . '/cache/ytgdpr_thumbnails';
    }

    private function getMetaCacheFilePath($videoId)
    {
        return $this->getCacheFolderPath() . '/meta_' . $videoId . '.json';
    }

    private function getCachedThumbnailUrl($videoId)
    {
        $cachePath = $this->getCacheFolderPath();
        $cacheUrl = $this->getCacheFolderUrl();
        $cacheDuration = $this->getCacheDurationSeconds();
        $fileName = 'yt_' . $videoId . '.jpg';
        $filePath = $cachePath . '/' . $fileName;
        $fileUrl = $cacheUrl . '/' . rawurlencode($fileName);

        if (!JFolder::exists($cachePath) && !JFolder::create($cachePath)) {
            return '';
        }

        if (JFile::exists($filePath)) {
            if (!$this->isUsableImageFile($filePath)) {
                JFile::delete($filePath);
            }
        }

        if (JFile::exists($filePath)) {
            $modified = @filemtime($filePath);
            if ($modified && (time() - (int) $modified) < $cacheDuration) {
                return $fileUrl;
            }
        }

        if (!$this->downloadThumbnailToFile($videoId, $filePath)) {
            if (JFile::exists($filePath) && $this->isUsableImageFile($filePath)) {
                return $fileUrl;
            }

            return '';
        }

        clearstatcache(true, $filePath);
        if (!$this->isUsableImageFile($filePath)) {
            if (JFile::exists($filePath)) {
                JFile::delete($filePath);
            }

            return '';
        }

        return $fileUrl;
    }

    private function getCachedVideoMeta($videoId, $watchUrl)
    {
        $cacheDuration = $this->getCacheDurationSeconds();
        $cachePath = $this->getCacheFolderPath();
        $metaPath = $this->getMetaCacheFilePath($videoId);

        if (!JFolder::exists($cachePath) && !JFolder::create($cachePath)) {
            return array();
        }

        if (JFile::exists($metaPath)) {
            $modified = @filemtime($metaPath);
            if ($modified && (time() - (int) $modified) < $cacheDuration) {
                $cachedJson = @file_get_contents($metaPath);
                $cached = json_decode((string) $cachedJson, true);
                if (is_array($cached) && !empty($cached['title'])) {
                    return $cached;
                }
            }
        }

        $fetched = $this->fetchVideoMeta($watchUrl);
        if (!empty($fetched['title'])) {
            JFile::write($metaPath, json_encode($fetched));
            return $fetched;
        }

        if (JFile::exists($metaPath)) {
            $cachedJson = @file_get_contents($metaPath);
            $cached = json_decode((string) $cachedJson, true);
            if (is_array($cached)) {
                return $cached;
            }
        }

        return array();
    }

    private function fetchVideoMeta($watchUrl)
    {
        $endpoints = array(
            'https://noembed.com/embed?url=' . rawurlencode($watchUrl),
            'https://www.youtube.com/oembed?url=' . rawurlencode($watchUrl) . '&format=json',
        );

        try {
            $http = JHttpFactory::getHttp();

            foreach ($endpoints as $endpoint) {
                $response = $http->get($endpoint, array('Accept' => 'application/json', 'User-Agent' => 'Mozilla/5.0 (compatible; ytgdpr-meta-fetcher/1.0)'), 10);
                if ((int) $response->code !== 200) {
                    continue;
                }

                $payload = json_decode((string) $response->body, true);
                if (!is_array($payload)) {
                    continue;
                }

                $normalized = $this->normalizeMetaPayload($payload);
                if (!empty($normalized['title'])) {
                    return $normalized;
                }
            }
        } catch (Exception $e) {
            return array();
        }

        return array();
    }

    private function normalizeMetaPayload($payload)
    {
        $result = array();

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

        return $result;
    }

    private function downloadThumbnailToFile($videoId, $filePath)
    {
        $sources = array(
            'https://i.ytimg.com/vi/' . rawurlencode($videoId) . '/maxresdefault.jpg',
            'https://i.ytimg.com/vi/' . rawurlencode($videoId) . '/hqdefault.jpg',
            'https://i.ytimg.com/vi/' . rawurlencode($videoId) . '/mqdefault.jpg',
            'https://i.ytimg.com/vi/' . rawurlencode($videoId) . '/default.jpg',
        );

        try {
            $http = JHttpFactory::getHttp();

            foreach ($sources as $sourceUrl) {
                $response = $http->get($sourceUrl, array('Accept' => 'image/*', 'User-Agent' => 'Mozilla/5.0 (compatible; ytgdpr-thumbnail-fetcher/1.0)'), 10);
                if ((int) $response->code !== 200) {
                    continue;
                }

                $body = (string) $response->body;
                if (!$this->isImageResponse($response, $body)) {
                    continue;
                }

                if (JFile::write($filePath, $body)) {
                    return true;
                }
            }
        } catch (Exception $e) {
            return false;
        }

        return false;
    }

    private function isUsableImageFile($filePath)
    {
        if (!JFile::exists($filePath)) {
            return false;
        }

        $size = @filesize($filePath);
        if (!$size || $size < 1000) {
            return false;
        }

        $imageInfo = @getimagesize($filePath);
        return is_array($imageInfo) && !empty($imageInfo[0]) && !empty($imageInfo[1]);
    }

    private function isImageResponse($response, $body)
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
}
