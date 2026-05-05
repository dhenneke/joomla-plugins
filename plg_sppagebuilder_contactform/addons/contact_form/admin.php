<?php

/**
 * @package     Joomla.Plugin.Sppagebuilder.Contactform
 * @subpackage  Sppagebuilder.Contactform
 *
 * @copyright   (C) 2026 Dominik Henneke
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;

$language = Factory::getApplication()->getLanguage();
$language->load('plg_sppagebuilder_contactform', JPATH_ADMINISTRATOR)
    || $language->load('plg_sppagebuilder_contactform', JPATH_SITE)
    || $language->load('plg_sppagebuilder_contactform', JPATH_PLUGINS . '/sppagebuilder/contactform');

SpAddonsConfig::addonConfig(
    [
        'type' => 'content',
        'addon_name' => 'contact_form',
        'title' => Text::_('PLG_SPPAGEBUILDER_CONTACTFORM_ADDON_TITLE'),
        'desc' => Text::_('PLG_SPPAGEBUILDER_CONTACTFORM_ADDON_DESC'),
        'icon' => Uri::root() . 'plugins/sppagebuilder/contactform/addons/contact_form/assets/images/icon.svg',
        'category' => 'MyAddons',
        'attr' => [
            'general' => [
                'admin_label' => [
                    'type' => 'text',
                    'title' => Text::_('COM_SPPAGEBUILDER_ADDON_ADMIN_LABEL'),
                    'desc' => Text::_('COM_SPPAGEBUILDER_ADDON_ADMIN_LABEL_DESC'),
                    'std' => '',
                ],
                'form_title' => [
                    'type' => 'text',
                    'title' => Text::_('PLG_SPPAGEBUILDER_CONTACTFORM_FIELD_FORM_TITLE_LABEL'),
                    'desc' => Text::_('PLG_SPPAGEBUILDER_CONTACTFORM_FIELD_FORM_TITLE_DESC'),
                    'std' => Text::_('PLG_SPPAGEBUILDER_CONTACTFORM_DEFAULT_TITLE'),
                ],
                'submit_label' => [
                    'type' => 'text',
                    'title' => Text::_('PLG_SPPAGEBUILDER_CONTACTFORM_FIELD_SUBMIT_LABEL_LABEL'),
                    'desc' => Text::_('PLG_SPPAGEBUILDER_CONTACTFORM_FIELD_SUBMIT_LABEL_DESC'),
                    'std' => Text::_('PLG_SPPAGEBUILDER_CONTACTFORM_DEFAULT_SUBMIT_LABEL'),
                ],
                'recipient_email' => [
                    'type' => 'text',
                    'title' => Text::_('PLG_SPPAGEBUILDER_CONTACTFORM_FIELD_RECIPIENT_EMAIL_LABEL'),
                    'desc' => Text::_('PLG_SPPAGEBUILDER_CONTACTFORM_FIELD_RECIPIENT_EMAIL_DESC'),
                    'placeholder' => 'name@example.com',
                    'std' => '',
                ],
                'enable_captcha' => [
                    'type' => 'checkbox',
                    'title' => Text::_('PLG_SPPAGEBUILDER_CONTACTFORM_FIELD_ENABLE_CAPTCHA_LABEL'),
                    'desc' => Text::_('PLG_SPPAGEBUILDER_CONTACTFORM_FIELD_ENABLE_CAPTCHA_DESC'),
                    'std' => 1,
                ],
                'captcha_type' => [
                    'type' => 'plugin',
                    'plugin_type' => 'captcha',
                    'title' => Text::_('COM_SPPAGEBUILDER_GLOBAL_CAPTCHA_TYPE'),
                    'desc' => Text::_('COM_SPPAGEBUILDER_GLOBAL_CAPTCHA_TYPE_DESC'),
                    'std' => 'default',
                    'inline' => true,
                    'depends' => [['enable_captcha', '=', 1]],
                ],
            ],
        ],
    ]
);
