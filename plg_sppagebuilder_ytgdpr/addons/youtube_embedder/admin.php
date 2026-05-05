<?php

/**
 * @package     Joomla.Plugin.Sppagebuilder.YoutubeGdpr
 * @subpackage  Sppagebuilder.YoutubeGdpr
 *
 * @copyright   (C) 2026 Dominik Henneke
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;

$language = Factory::getApplication()->getLanguage();
$language->load('plg_sppagebuilder_youtube_gdpr', JPATH_ADMINISTRATOR)
    || $language->load('plg_sppagebuilder_youtube_gdpr', JPATH_SITE)
    || $language->load('plg_sppagebuilder_youtube_gdpr', JPATH_PLUGINS . '/sppagebuilder/youtube_gdpr');

SpAddonsConfig::addonConfig(
    [
        'type' => 'content',
        'addon_name' => 'youtube_embedder',
        'title' => Text::_('PLG_SPPAGEBUILDER_YOUTUBE_GDPR_ADDON_TITLE'),
        'desc' => Text::_('PLG_SPPAGEBUILDER_YOUTUBE_GDPR_ADDON_DESC'),
        'icon' => Uri::root() . 'plugins/sppagebuilder/youtube_gdpr/addons/youtube_embedder/assets/images/icon.svg',
        'category' => 'MyAddons',
        'attr' => [
            'general' => [
                'admin_label' => [
                    'type' => 'text',
                    'title' => Text::_('COM_SPPAGEBUILDER_ADDON_ADMIN_LABEL'),
                    'desc' => Text::_('COM_SPPAGEBUILDER_ADDON_ADMIN_LABEL_DESC'),
                    'std' => '',
                ],
                'title' => [
                    'type' => 'textarea',
                    'title' => Text::_('COM_SPPAGEBUILDER_ADDON_TITLE'),
                    'desc' => Text::_('COM_SPPAGEBUILDER_ADDON_TITLE_DESC'),
                    'std' => '',
                ],
                'youtube_url' => [
                    'type' => 'text',
                    'title' => Text::_('PLG_SPPAGEBUILDER_YOUTUBE_GDPR_FIELD_YOUTUBE_URL_LABEL'),
                    'desc' => Text::_('PLG_SPPAGEBUILDER_YOUTUBE_GDPR_FIELD_YOUTUBE_URL_DESC'),
                    'placeholder' => Text::_('PLG_SPPAGEBUILDER_YOUTUBE_GDPR_FIELD_YOUTUBE_URL_PLACEHOLDER'),
                    'std' => '',
                ],
            ],
        ],
    ]
);
