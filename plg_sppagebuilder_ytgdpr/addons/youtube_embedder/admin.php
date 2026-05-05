<?php

//no direct accees
defined('_JEXEC') or die('Restricted Aceess');

SpAddonsConfig::addonConfig(
    array(
        'type' => 'content',
        'addon_name' => 'youtube_embedder',
        'title' => 'YouTube Embedder',
        'desc' => 'A GDPR-compliant YouTube video embedder for SP Page Builder.',
        'icon' => JURI::root() . 'plugins/sppagebuilder/ytgdpr/addons/youtube_embedder/assets/images/icon.png',
        'category' => 'MyAddons',
        'attr' => array(
            'general' => array(
                'admin_label' => array(
                    'type' => 'text',
                    'title' => JText::_('COM_SPPAGEBUILDER_ADDON_ADMIN_LABEL'),
                    'desc' => JText::_('COM_SPPAGEBUILDER_ADDON_ADMIN_LABEL_DESC'),
                    'std' => ''
                ),
                // Title
                'title' => array(
                    'type' => 'textarea',
                    'title' => JText::_('COM_SPPAGEBUILDER_ADDON_TITLE'),
                    'desc' => JText::_('COM_SPPAGEBUILDER_ADDON_TITLE_DESC'),
                    'std' => ''
                ),
                'youtube_url' => array(
                    'type' => 'text',
                    'title' => 'YouTube Video URL',
                    'desc' => 'Paste a YouTube link (watch, short, embed, or youtu.be).',
                    'placeholder' => 'https://www.youtube.com/watch?v=abcdef12345',
                    'std' => '',
                ),

            ),
        ),
    )
);
