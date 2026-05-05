<?php

/**
 * @package     Joomla.Plugin.Sppagebuilder.Contactform
 * @subpackage  Sppagebuilder.Contactform
 *
 * @copyright   (C) 2026 Dominik Henneke
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

defined('_JEXEC') or die;

$data = (isset($displayData) && is_array($displayData)) ? $displayData : [];

$formId = (string) ($data['formId'] ?? '');
$formTitle = (string) ($data['formTitle'] ?? '');
$actionUrl = (string) ($data['actionUrl'] ?? '');
$nameLabel = (string) ($data['nameLabel'] ?? '');
$emailLabel = (string) ($data['emailLabel'] ?? '');
$subjectLabel = (string) ($data['subjectLabel'] ?? '');
$messageLabel = (string) ($data['messageLabel'] ?? '');
$nameValue = (string) ($data['nameValue'] ?? '');
$emailValue = (string) ($data['emailValue'] ?? '');
$subjectValue = (string) ($data['subjectValue'] ?? '');
$messageValue = (string) ($data['messageValue'] ?? '');
$captchaHtml = (string) ($data['captchaHtml'] ?? '');
$submitMarkerName = (string) ($data['submitMarkerName'] ?? 'cf_submit');
$addonId = (int) ($data['addonId'] ?? 0);
$returnUrl = (string) ($data['returnUrl'] ?? '');
$stateToken = (string) ($data['stateToken'] ?? '');
$submitLabel = (string) ($data['submitLabel'] ?? '');
$feedbackType = (string) ($data['feedbackType'] ?? '');
$feedbackText = (string) ($data['feedbackText'] ?? '');
?>
<div class="sppb-contact-form-wrap">
    <?php if ($formTitle !== '') : ?>
        <h3 class="sppb-contact-form-title"><?php echo $formTitle; ?></h3>
    <?php endif; ?>

    <?php if (!empty($feedbackText)) : ?>
        <div class="sppb-contact-form-feedback sppb-contact-form-feedback-<?php echo $feedbackType === 'success' ? 'success' : 'error'; ?>">
            <?php echo htmlspecialchars((string) $feedbackText, ENT_QUOTES, 'UTF-8'); ?>
        </div>
    <?php endif; ?>

    <?php if ($feedbackType !== 'success') : ?>
        <form id="<?php echo $formId; ?>" class="sppb-contact-form" action="<?php echo $actionUrl; ?>" method="post">
            <div class="sppb-contact-form-row">
                <label for="<?php echo $formId; ?>-name"><?php echo $nameLabel; ?></label>
                <input id="<?php echo $formId; ?>-name" name="cf_name" type="text" value="<?php echo $nameValue; ?>" required>
            </div>

            <div class="sppb-contact-form-row">
                <label for="<?php echo $formId; ?>-email"><?php echo $emailLabel; ?></label>
                <input id="<?php echo $formId; ?>-email" name="cf_email" type="email" value="<?php echo $emailValue; ?>" required>
            </div>

            <div class="sppb-contact-form-row">
                <label for="<?php echo $formId; ?>-subject"><?php echo $subjectLabel; ?></label>
                <input id="<?php echo $formId; ?>-subject" name="cf_subject" type="text" value="<?php echo $subjectValue; ?>" required>
            </div>

            <div class="sppb-contact-form-row">
                <label for="<?php echo $formId; ?>-message"><?php echo $messageLabel; ?></label>
                <textarea id="<?php echo $formId; ?>-message" name="cf_message" rows="6" required><?php echo $messageValue; ?></textarea>
            </div>

            <?php if ($captchaHtml !== '') : ?>
                <div class="sppb-contact-form-row sppb-contact-form-captcha-wrap">
                    <?php echo $captchaHtml; ?>
                </div>
            <?php endif; ?>

            <input type="hidden" name="<?php echo $submitMarkerName; ?>" value="1">
            <input type="hidden" name="cf_addon_id" value="<?php echo (int) $addonId; ?>">
            <input type="hidden" name="cf_return" value="<?php echo $returnUrl; ?>">
            <input type="hidden" name="cf_state" value="<?php echo $stateToken; ?>">

            <button type="submit" class="sppb-btn sppb-btn-primary"><?php echo $submitLabel; ?></button>
        </form>
    <?php endif; ?>
</div>
