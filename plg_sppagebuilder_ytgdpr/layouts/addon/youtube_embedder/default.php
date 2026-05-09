<?php

/**
 * @package     Joomla.Plugin.Sppagebuilder.YoutubeGdpr
 * @subpackage  Sppagebuilder.YoutubeGdpr
 *
 * @copyright   (C) 2026 Dominik Henneke
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;

/**
 * Layout for YouTube GDPR with thumbnail
 * 
 * @var string $watchUrl URL to watch on YouTube
 * @var string $thumbnailUrl URL to the thumbnail image
 * @var string $title Video title
 * @var string $channel Channel name
 * @var string $avatarLabel First letter of channel name
 */

/**
 * @var array{
 *     watchUrl?: string,
 *     thumbnailUrl?: string,
 *     title?: string,
 *     channel?: string,
 *     avatarLabel?: string
 * } $data
 */
$data = (isset($displayData) && is_array($displayData)) ? $displayData : [];
$watchUrl = $data['watchUrl'] ?? '';
$thumbnailUrl = $data['thumbnailUrl'] ?? '';
$title = $data['title'] ?? '';
$channel = $data['channel'] ?? '';
$avatarLabel = $data['avatarLabel'] ?? '';
?>
<div class="ytgdpr">
    <a class="ytgdpr-link" href="<?= $watchUrl ?>" target="_blank" rel="noopener noreferrer nofollow">
        <img class="ytgdpr-thumb img-fluid" src="<?= $thumbnailUrl ?>" alt="<?= $title ?>" loading="lazy" />
        <div class="ytgdpr-meta d-flex align-items-center gap-2">
            <span class="ytgdpr-avatar d-inline-flex align-items-center justify-content-center" aria-hidden="true"><?= $avatarLabel ?></span>
            <span class="ytgdpr-meta-text d-flex flex-column">
                <span class="ytgdpr-meta-title"><?= $title ?></span>
                <span class="ytgdpr-meta-channel"><?= $channel ?></span>
            </span>
        </div>
        <span class="ytgdpr-play d-flex align-items-center justify-content-center" aria-hidden="true"><span class="ytgdpr-play-triangle"></span></span>
        <span class="ytgdpr-watch"><?= Text::_('PLG_SPPAGEBUILDER_YOUTUBE_GDPR_WATCH_CTA') ?></span>
    </a>
</div>