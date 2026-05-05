<?php

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

extract($displayData);
?>
<div class="ytgdpr">
    <a class="ytgdpr-link" href="<?= $watchUrl ?>" target="_blank" rel="noopener noreferrer nofollow">
        <img class="ytgdpr-thumb" src="<?= $thumbnailUrl ?>" alt="<?= $title ?>" loading="lazy" />
        <div class="ytgdpr-meta">
            <span class="ytgdpr-avatar" aria-hidden="true"><?= $avatarLabel ?></span>
            <span class="ytgdpr-meta-text">
                <span class="ytgdpr-meta-title"><?= $title ?></span>
                <span class="ytgdpr-meta-channel"><?= $channel ?></span>
            </span>
        </div>
        <span class="ytgdpr-play" aria-hidden="true"><span class="ytgdpr-play-triangle"></span></span>
        <span class="ytgdpr-watch"><?= Text::_('PLG_SPPAGEBUILDER_YOUTUBE_GDPR_WATCH_CTA') ?></span>
    </a>
</div>