<?php

/**
 * @package     Joomla.Plugin.Sppagebuilder.Contactform
 * @subpackage  Sppagebuilder.Contactform
 *
 * @copyright   (C) 2026 Dominik Henneke
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

defined('_JEXEC') or die;

/**
 * @var array{
 *     formId?: string,
 *     actionUrl?: string,
 *     nameLabel?: string,
 *     emailLabel?: string,
 *     subjectLabel?: string,
 *     messageLabel?: string,
 *     nameValue?: string,
 *     emailValue?: string,
 *     subjectValue?: string,
 *     messageValue?: string,
 *     privacyConsentRequired?: bool,
 *     privacyConsentChecked?: bool,
 *     privacyConsentHtml?: string,
 *     captchaHtml?: string,
 *     submitMarkerName?: string,
 *     addonId?: int,
 *     returnUrl?: string,
 *     stateToken?: string,
 *     submitLabel?: string,
 *     feedbackType?: string,
 *     feedbackText?: string
 * } $data
 */
$data = (isset($displayData) && is_array($displayData)) ? $displayData : [];
$formId = $data['formId'] ?? '';
$actionUrl = $data['actionUrl'] ?? '';
$nameLabel = $data['nameLabel'] ?? '';
$emailLabel = $data['emailLabel'] ?? '';
$subjectLabel = $data['subjectLabel'] ?? '';
$messageLabel = $data['messageLabel'] ?? '';
$nameValue = $data['nameValue'] ?? '';
$emailValue = $data['emailValue'] ?? '';
$subjectValue = $data['subjectValue'] ?? '';
$messageValue = $data['messageValue'] ?? '';
$privacyConsentRequired = !empty($data['privacyConsentRequired']);
$privacyConsentChecked = !empty($data['privacyConsentChecked']);
$privacyConsentHtml = $data['privacyConsentHtml'] ?? '';
$captchaHtml = $data['captchaHtml'] ?? '';
$submitMarkerName = $data['submitMarkerName'] ?? 'cf_submit';
$addonId = $data['addonId'] ?? 0;
$returnUrl = $data['returnUrl'] ?? '';
$stateToken = $data['stateToken'] ?? '';
$submitLabel = $data['submitLabel'] ?? '';
$feedbackType = $data['feedbackType'] ?? '';
$feedbackText = $data['feedbackText'] ?? '';
?>
<div class="sppb-contact-form-wrap">
    <?php if (!empty($feedbackText)) : ?>
        <div class="sppb-contact-form-feedback sppb-contact-form-feedback-<?php echo $feedbackType === 'success' ? 'success' : 'error'; ?>">
            <?php echo htmlspecialchars((string) $feedbackText, ENT_QUOTES, 'UTF-8'); ?>
        </div>
    <?php endif; ?>

    <?php if ($feedbackType !== 'success') : ?>
        <form id="<?php echo $formId; ?>" class="sppb-contact-form sppb-row" action="<?php echo $actionUrl; ?>" method="post">
            <div class="sppb-contact-form-row sppb-form-group sppb-col-sm-12">
                <label class="sppb-contact-form-label" for="<?php echo $formId; ?>-name"><?php echo $nameLabel; ?></label>
                <input id="<?php echo $formId; ?>-name" class="form-control sppb-form-control" name="cf_name" type="text" value="<?php echo $nameValue; ?>" placeholder="<?php echo $nameLabel; ?>" required>
            </div>

            <div class="sppb-contact-form-row sppb-form-group sppb-col-sm-12">
                <label class="sppb-contact-form-label" for="<?php echo $formId; ?>-email"><?php echo $emailLabel; ?></label>
                <input id="<?php echo $formId; ?>-email" class="form-control sppb-form-control" name="cf_email" type="email" value="<?php echo $emailValue; ?>" placeholder="<?php echo $emailLabel; ?>" required>
            </div>

            <div class="sppb-contact-form-row sppb-form-group sppb-col-sm-12">
                <label class="sppb-contact-form-label" for="<?php echo $formId; ?>-subject"><?php echo $subjectLabel; ?></label>
                <input id="<?php echo $formId; ?>-subject" class="form-control sppb-form-control" name="cf_subject" type="text" value="<?php echo $subjectValue; ?>" placeholder="<?php echo $subjectLabel; ?>" required>
            </div>

            <div class="sppb-contact-form-row sppb-form-group sppb-col-sm-12">
                <label class="sppb-contact-form-label" for="<?php echo $formId; ?>-message"><?php echo $messageLabel; ?></label>
                <textarea id="<?php echo $formId; ?>-message" class="form-control sppb-form-control" name="cf_message" rows="6" placeholder="<?php echo $messageLabel; ?>" required><?php echo $messageValue; ?></textarea>
            </div>

            <?php if ($privacyConsentRequired) : ?>
                <div class="sppb-contact-form-row sppb-form-group sppb-col-sm-12">
                    <div class="sppb-form-check">
                        <input
                            id="<?php echo $formId; ?>-privacy"
                            class="sppb-form-check-input"
                            name="cf_privacy_consent"
                            type="checkbox"
                            value="1"
                            <?php echo $privacyConsentChecked ? 'checked' : ''; ?>
                            required
                        >
                        <label class="sppb-form-check-label" for="<?php echo $formId; ?>-privacy">
                            <?php if ($privacyConsentHtml !== '') : ?>
                                <?php echo $privacyConsentHtml; ?>
                            <?php endif; ?>
                        </label>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($captchaHtml !== '') : ?>
                <div class="sppb-contact-form-row sppb-contact-form-captcha-wrap sppb-form-group sppb-col-sm-12">
                    <?php echo $captchaHtml; ?>
                </div>
            <?php endif; ?>

            <input type="hidden" name="<?php echo $submitMarkerName; ?>" value="1">
            <input type="hidden" name="cf_addon_id" value="<?php echo (int) $addonId; ?>">
            <input type="hidden" name="cf_return" value="<?php echo $returnUrl; ?>">
            <input type="hidden" name="cf_state" value="<?php echo $stateToken; ?>">

            <div class="sppb-form-button sppb-col-sm-12">
                <button type="submit" class="sppb-btn sppb-btn-primary"><?php echo $submitLabel; ?></button>
            </div>
        </form>
    <?php endif; ?>
</div>
