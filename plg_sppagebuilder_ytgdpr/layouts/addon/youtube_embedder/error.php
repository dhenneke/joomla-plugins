<?php

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;

/**
 * Layout for YouTube GDPR error state
 * 
 * @var string $message Error message
 */

extract($displayData);
?>
<div class="ytgdpr ytgdpr-error">
    <?= $message ?? Text::_('PLG_SPPAGEBUILDER_YOUTUBE_GDPR_INVALID_URL') ?>
</div>