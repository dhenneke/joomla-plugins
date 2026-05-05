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
 * Layout for YouTube GDPR placeholder state (thumbnail unavailable)
 * 
 * @var string $watchUrl URL to watch on YouTube
 * @var string $title Video title
 */

extract($displayData);
?>
<div class="ytgdpr">
    <div class="ytgdpr-placeholder d-flex flex-column align-items-center justify-content-center gap-2" role="status" aria-live="polite">
        <span class="ytgdpr-placeholder-text"><?= Text::sprintf('PLG_SPPAGEBUILDER_YOUTUBE_GDPR_THUMBNAIL_UNAVAILABLE', str_replace('%', '%%', $title)) ?></span>
        <a class="btn btn-sm btn-dark" href="<?= $watchUrl ?>" target="_blank" rel="noopener noreferrer nofollow">
            <?= Text::_('PLG_SPPAGEBUILDER_YOUTUBE_GDPR_PLACEHOLDER_CTA') ?>
        </a>
    </div>
</div>