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
 * Layout for YouTube GDPR error state
 * 
 * @var string $message Error message
 */

$data = (isset($displayData) && is_array($displayData)) ? $displayData : [];
$message = (string) ($data['message'] ?? Text::_('PLG_SPPAGEBUILDER_YOUTUBE_GDPR_INVALID_URL'));
?>
<div class="ytgdpr alert alert-danger mb-0" role="alert">
    <?= $message ?>
</div>