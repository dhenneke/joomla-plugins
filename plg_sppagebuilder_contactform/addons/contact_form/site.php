<?php

/**
 * @package     Joomla.Plugin.Sppagebuilder.Contactform
 * @subpackage  Sppagebuilder.Contactform
 *
 * @copyright   (C) 2026 Dominik Henneke
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

defined('_JEXEC') or die;

use Joomla\CMS\Captcha\Captcha;
use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Cache\CacheControllerFactoryInterface;
use Joomla\CMS\Cache\Controller\OutputController;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Log\Log;
use Joomla\CMS\Uri\Uri;
use Joomla\Filter\InputFilter;

class SppagebuilderAddonContact_form extends SppagebuilderAddons
{
    private const CSS_FILE = 'media/plg_sppagebuilder_contactform/assets/css/contact_form.css';
    private const STATE_TTL_SECONDS = 3600;
    private const MAX_NAME_LENGTH = 120;
    private const MAX_EMAIL_LENGTH = 254;
    private const MAX_SUBJECT_LENGTH = 180;
    private const MAX_MESSAGE_LENGTH = 5000;
    private const RATE_LIMIT_WINDOW_SECONDS = 900;
    private const RATE_LIMIT_MAX_SUBMISSIONS = 5;

    /** @var array{name: string, email: string, subject: string, message: string, privacy: string} */
    private array $failedInput = [
        'name' => '',
        'email' => '',
        'subject' => '',
        'message' => '',
        'privacy' => '0',
    ];

    private string $inlineFeedbackType = '';
    private string $inlineFeedbackText = '';

    public function render(): string
    {
        $this->loadPluginLanguage();

        $submitLabel = trim((string) $this->getSetting('submit_label', Text::_('PLG_SPPAGEBUILDER_CONTACTFORM_DEFAULT_SUBMIT_LABEL')));
        $recipientEmail = trim((string) $this->getSetting('recipient_email', ''));
        $captchaEnabled = $this->isCaptchaEnabled();
        $captchaPlugin = $this->resolveCaptchaPlugin();
        $privacyConsentRequired = $this->isPrivacyConsentRequired();
        $privacyConsentHtml = $this->resolvePrivacyConsentHtml();

        $addonId = isset($this->addon->id) ? (int) $this->addon->id : random_int(1000, 9999);
        $formId = 'sppb-contact-form-' . $addonId;
        $captchaFieldName = 'cf_captcha_' . $addonId;
        $returnUrl = Uri::getInstance()->toString(['path', 'query']);

        $this->handleSubmitIfTargeted(
            $addonId,
            $captchaFieldName,
            $recipientEmail,
            $captchaEnabled,
            $captchaPlugin,
            $privacyConsentRequired,
            $returnUrl
        );

        $stateToken = $this->createStateToken($addonId, $returnUrl);
        if ($stateToken === '') {
            return '';
        }

        $feedback = ($this->inlineFeedbackType !== '' && $this->inlineFeedbackText !== '')
            ? ['type' => $this->inlineFeedbackType, 'text' => $this->inlineFeedbackText]
            : ['type' => '', 'text' => ''];

        return $this->renderLayout('default', [
            'formId' => $this->esc($formId),
            'actionUrl' => $this->esc($returnUrl),
            'addonId' => $addonId,
            'returnUrl' => $this->esc(base64_encode($returnUrl)),
            'stateToken' => $this->esc($stateToken),
            'submitLabel' => $this->esc($submitLabel),
            'captchaHtml' => $this->getCaptchaHtml($captchaFieldName, $formId, $captchaEnabled, $captchaPlugin),
            'feedbackType' => $feedback['type'],
            'feedbackText' => $feedback['text'],
            'nameValue' => $this->esc($this->failedInput['name']),
            'emailValue' => $this->esc($this->failedInput['email']),
            'subjectValue' => $this->esc($this->failedInput['subject']),
            'messageValue' => $this->esc($this->failedInput['message']),
            'privacyConsentRequired' => $privacyConsentRequired,
            'privacyConsentChecked' => $this->failedInput['privacy'] === '1',
            'privacyConsentHtml' => $privacyConsentHtml,
            'nameLabel' => Text::_('PLG_SPPAGEBUILDER_CONTACTFORM_FIELD_NAME_LABEL'),
            'emailLabel' => Text::_('PLG_SPPAGEBUILDER_CONTACTFORM_FIELD_EMAIL_LABEL'),
            'subjectLabel' => Text::_('PLG_SPPAGEBUILDER_CONTACTFORM_FIELD_SUBJECT_LABEL'),
            'messageLabel' => Text::_('PLG_SPPAGEBUILDER_CONTACTFORM_FIELD_MESSAGE_LABEL'),
            'submitMarkerName' => 'cf_submit',
        ]);
    }

    /** @return array<int, string> */
    public function stylesheets(): array
    {
        return [
            Uri::root() . self::CSS_FILE,
        ];
    }

    private function getSetting(string $name, string $default = ''): string
    {
        if (isset($this->addon->settings->{$name}) && $this->addon->settings->{$name} !== '') {
            return (string) $this->addon->settings->{$name};
        }

        return $default;
    }

    private function getCaptchaHtml(string $fieldName, string $formId, bool $captchaEnabled, string $captchaPlugin): string
    {
        if (!$captchaEnabled || $captchaPlugin === '') {
            return '';
        }

        try {
            $captcha = Captcha::getInstance($captchaPlugin);

            if ($captcha === null) {
                return '';
            }

            return (string) $captcha->display($fieldName, $formId . '-captcha', 'sppb-contact-form-captcha');
        } catch (Throwable $exception) {
            return '';
        }
    }

    private function isCaptchaEnabled(): bool
    {
        return (int) $this->getSetting('enable_captcha', '1') === 1;
    }

    private function isPrivacyConsentRequired(): bool
    {
        return (int) $this->getSetting('require_privacy_consent', '0') === 1;
    }

    private function resolvePrivacyConsentHtml(): string
    {
        $configuredHtml = trim((string) $this->getSetting('privacy_consent_html', ''));

        return $this->sanitizePrivacyConsentHtml($configuredHtml);
    }

    private function resolveCaptchaPlugin(): string
    {
        $configured = trim((string) $this->getSetting('captcha_type', 'default'));

        if ($configured === '' || strtolower($configured) === 'default') {
            return trim($this->normalizeStringValue(ComponentHelper::getParams('com_config')->get('captcha')));
        }

        return $configured;
    }

    private function loadPluginLanguage(): void
    {
        $language = Factory::getApplication()->getLanguage();
        $language->load('plg_sppagebuilder_contactform', JPATH_ADMINISTRATOR)
            || $language->load('plg_sppagebuilder_contactform', JPATH_SITE)
            || $language->load('plg_sppagebuilder_contactform', JPATH_PLUGINS . '/sppagebuilder/contactform');
    }

    private function esc(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }

    /** @param array<string, mixed> $displayData */
    private function renderLayout(string $layoutName, array $displayData = []): string
    {
        $layoutFile = JPATH_PLUGINS . '/sppagebuilder/contactform/layouts/addon/contact_form/' . $layoutName . '.php';

        if (!file_exists($layoutFile)) {
            return '';
        }

        ob_start();
        include $layoutFile;
        return (string) ob_get_clean();
    }

    private function handleSubmitIfTargeted(
        int $addonId,
        string $captchaFieldName,
        string $recipientEmail,
        bool $captchaEnabled,
        string $captchaPlugin,
        bool $privacyConsentRequired,
        string $defaultReturnUrl
    ): void
    {
        /** @var CMSApplication $app */
        $app = Factory::getApplication();
        $input = $app->input;

        $requestMethod = strtoupper($input->server->getString('REQUEST_METHOD', 'GET'));
        if ($requestMethod !== 'POST') {
            return;
        }

        if ($input->post->getInt('cf_submit', 0) !== 1) {
            return;
        }

        if ($input->post->getInt('cf_addon_id', 0) !== $addonId) {
            return;
        }

        $name = trim($input->post->getString('cf_name', ''));
        $email = trim($input->post->getString('cf_email', ''));
        $subject = trim($input->post->getString('cf_subject', ''));
        $message = trim($input->post->getString('cf_message', ''));
        $privacyConsent = $input->post->getInt('cf_privacy_consent', 0) === 1;

        $state = $this->verifyStateToken($input->post->getString('cf_state', ''), $addonId);
        if ($state === null) {
            $this->logSecurityEvent('Rejected submission due to invalid state token.', Log::WARNING, ['addon_id' => $addonId]);
            $this->setFailedSubmission('PLG_SPPAGEBUILDER_CONTACTFORM_ERROR_INVALID_SUBMISSION', $name, $email, $subject, $message, $privacyConsent);
            return;
        }

        $returnUrl = $this->resolveInternalReturnUrl(
            $input->post->getBase64('cf_return', ''),
            (string) ($state['return_url'] ?? ''),
            $defaultReturnUrl
        );

        if (!$this->checkSameOriginRequest()) {
            $this->logSecurityEvent('Rejected submission due to same-origin validation failure.', Log::WARNING, ['addon_id' => $addonId]);
            $this->setFailedSubmission('PLG_SPPAGEBUILDER_CONTACTFORM_ERROR_INVALID_SUBMISSION', $name, $email, $subject, $message, $privacyConsent);
            return;
        }

        if (!$this->checkRateLimit($addonId)) {
            $this->logSecurityEvent('Rejected submission due to rate limit.', Log::WARNING, ['addon_id' => $addonId]);
            $this->setFailedSubmission('PLG_SPPAGEBUILDER_CONTACTFORM_ERROR_RATE_LIMIT', $name, $email, $subject, $message, $privacyConsent);
            return;
        }

        $validationErrorKey = $this->validateSubmissionFields($name, $email, $subject, $message, $privacyConsentRequired, $privacyConsent);
        if ($validationErrorKey !== null) {
            $this->logSecurityEvent('Rejected submission due to field validation failure.', Log::NOTICE, ['addon_id' => $addonId]);
            $this->setFailedSubmission($validationErrorKey, $name, $email, $subject, $message, $privacyConsent);
            return;
        }

        if (!$this->validateCaptcha($captchaFieldName, $captchaEnabled, $captchaPlugin)) {
            $this->logSecurityEvent('Rejected submission due to captcha validation failure.', Log::NOTICE, ['addon_id' => $addonId]);
            $this->setFailedSubmission('PLG_SPPAGEBUILDER_CONTACTFORM_ERROR_CAPTCHA', $name, $email, $subject, $message, $privacyConsent);
            return;
        }

        $resolvedRecipient = $recipientEmail;
        if (!$this->isValidEmail($resolvedRecipient)) {
            $this->logSecurityEvent('Rejected submission due to invalid recipient configuration.', Log::ERROR, ['addon_id' => $addonId]);
            $this->setFailedSubmission('PLG_SPPAGEBUILDER_CONTACTFORM_ERROR_RECIPIENT', $name, $email, $subject, $message, $privacyConsent);
            return;
        }

        try {
            $config = Factory::getConfig();
            if (!(bool) $config->get('mailonline', 1)) {
                throw new RuntimeException('Mail is disabled in Joomla global configuration.');
            }

            $mailer = Factory::getMailer();
            $mailer->setSender([
                $this->normalizeStringValue($config->get('mailfrom')),
                $this->normalizeStringValue($config->get('fromname')),
            ]);
            $mailer->addRecipient($resolvedRecipient);
            $mailer->setSubject($subject);
            $mailer->isHtml(false);
            $mailer->setBody($this->buildMailBody($name, $email, $subject, $message));

            $result = $mailer->send();
            if ($result !== true) {
                $errorInfo = trim((string) $mailer->ErrorInfo);
                $detail = $errorInfo !== '' ? $errorInfo : 'Unknown mailer error';
                throw new RuntimeException('Failed to send message: ' . $detail);
            }
        } catch (Throwable $exception) {
            $this->logSecurityEvent('Failed to send contact form email.', Log::ERROR, [
                'addon_id' => $addonId,
                'error' => $exception->getMessage(),
            ]);
            $this->setFailedSubmission('PLG_SPPAGEBUILDER_CONTACTFORM_ERROR_SEND', $name, $email, $subject, $message, $privacyConsent);
            return;
        }

        $this->respondWithMessage('PLG_SPPAGEBUILDER_CONTACTFORM_SUCCESS');
    }

    private function validateCaptcha(string $fieldName, bool $captchaEnabled, string $captchaPlugin): bool
    {
        if (!$captchaEnabled) {
            return true;
        }

        if ($captchaPlugin === '') {
            return false;
        }

        /** @var CMSApplication $app */
        $app = Factory::getApplication();
        $input = $app->input;
        $captchaCode = $input->post->getString($fieldName, '');

        try {
            $captcha = Captcha::getInstance($captchaPlugin);
            return $captcha !== null && $captcha->checkAnswer($captchaCode);
        } catch (Throwable $exception) {
            return false;
        }
    }

    private function validateSubmissionFields(
        string $name,
        string $email,
        string $subject,
        string $message,
        bool $privacyConsentRequired,
        bool $privacyConsentAccepted
    ): ?string
    {
        if ($name === '' || $email === '' || $subject === '' || $message === '') {
            return 'PLG_SPPAGEBUILDER_CONTACTFORM_ERROR_REQUIRED_FIELDS';
        }

        if (
            $this->stringLength($name) > self::MAX_NAME_LENGTH
            || $this->stringLength($subject) > self::MAX_SUBJECT_LENGTH
            || $this->stringLength($message) > self::MAX_MESSAGE_LENGTH
            || $this->stringLength($email) > self::MAX_EMAIL_LENGTH
        ) {
            return 'PLG_SPPAGEBUILDER_CONTACTFORM_ERROR_REQUIRED_FIELDS';
        }

        if (!$this->isValidEmail($email)) {
            return 'PLG_SPPAGEBUILDER_CONTACTFORM_ERROR_INVALID_EMAIL';
        }

        if ($privacyConsentRequired && !$privacyConsentAccepted) {
            return 'PLG_SPPAGEBUILDER_CONTACTFORM_ERROR_PRIVACY_CONSENT_REQUIRED';
        }

        return null;
    }

    private function isValidEmail(string $email): bool
    {
        $email = trim($email);

        return $email !== ''
            && $this->stringLength($email) <= self::MAX_EMAIL_LENGTH
            && filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    private function stringLength(string $value): int
    {
        if (function_exists('mb_strlen')) {
            return mb_strlen($value, 'UTF-8');
        }

        return strlen($value);
    }

    private function resolveInternalReturnUrl(string $encodedReturnUrl, string $tokenReturnUrl, string $defaultReturnUrl): string
    {
        if ($tokenReturnUrl !== '' && Uri::isInternal($tokenReturnUrl)) {
            return $tokenReturnUrl;
        }

        $decoded = base64_decode($encodedReturnUrl, true);

        return ($decoded !== false && Uri::isInternal($decoded)) ? $decoded : $defaultReturnUrl;
    }

    private function buildMailBody(string $name, string $email, string $subject, string $message): string
    {
        $siteName = $this->normalizeStringValue(Factory::getConfig()->get('sitename'));

        return Text::sprintf(
            'PLG_SPPAGEBUILDER_CONTACTFORM_EMAIL_BODY',
            $siteName,
            $name,
            $email,
            $subject,
            $message
        );
    }

    private function respondWithMessage(string $messageKey): void
    {
        $this->setSubmissionFeedback('success', $messageKey, [
            'name' => '',
            'email' => '',
            'subject' => '',
            'message' => '',
            'privacy' => '0',
        ]);
    }

    private function setFailedSubmission(string $messageKey, string $name, string $email, string $subject, string $message, bool $privacyConsentAccepted): void
    {
        $this->setSubmissionFeedback('error', $messageKey, [
            'name' => $name,
            'email' => $email,
            'subject' => $subject,
            'message' => $message,
            'privacy' => $privacyConsentAccepted ? '1' : '0',
        ]);
    }

    /** @param array{name?: string, email?: string, subject?: string, message?: string, privacy?: string} $inputValues */
    private function setSubmissionFeedback(string $type, string $messageKey, array $inputValues): void
    {
        $this->failedInput = [
            'name' => (string) ($inputValues['name'] ?? ''),
            'email' => (string) ($inputValues['email'] ?? ''),
            'subject' => (string) ($inputValues['subject'] ?? ''),
            'message' => (string) ($inputValues['message'] ?? ''),
            'privacy' => (string) ($inputValues['privacy'] ?? '0'),
        ];
        $this->inlineFeedbackType = $type;
        $this->inlineFeedbackText = Text::_($messageKey);
    }

    private function sanitizePrivacyConsentHtml(string $html): string
    {
        $filter = new InputFilter(
            ['a', 'abbr', 'b', 'br', 'code', 'em', 'i', 'small', 'span', 'strong', 'u'],
            ['href', 'rel', 'target', 'title'],
            InputFilter::ONLY_ALLOW_DEFINED_TAGS,
            InputFilter::ONLY_ALLOW_DEFINED_ATTRIBUTES,
            1
        );

        return trim($this->normalizeStringValue($filter->clean($html, 'html')));
    }

    private function createStateToken(int $addonId, string $returnUrl): string
    {
        $signingKey = $this->getSigningKey();
        if ($signingKey === null) {
            return '';
        }

        $issuedAt = time();
        $payload = [
            'addon_id' => $addonId,
            'return_url' => $returnUrl,
            'iat' => $issuedAt,
            'exp' => $issuedAt + self::STATE_TTL_SECONDS,
        ];
        $encodedPayload = $this->base64UrlEncode((string) json_encode($payload));
        $signature = hash_hmac('sha256', $encodedPayload, $signingKey, true);
        $encodedSignature = $this->base64UrlEncode($signature);

        return $encodedPayload . '.' . $encodedSignature;
    }

    /** @return array<string, int|string>|null */
    private function verifyStateToken(string $token, int $expectedAddonId): ?array
    {
        $token = trim($token);
        if ($token === '' || strpos($token, '.') === false) {
            return null;
        }

        $parts = explode('.', $token, 2);
        if (count($parts) !== 2 || $parts[0] === '' || $parts[1] === '') {
            return null;
        }

        $signingKey = $this->getSigningKey();
        if ($signingKey === null) {
            return null;
        }

        $payloadEncoded = $parts[0];
        $signatureProvided = $this->base64UrlDecode($parts[1]);
        if ($signatureProvided === null) {
            return null;
        }

        $signatureExpected = hash_hmac('sha256', $payloadEncoded, $signingKey, true);
        if (!hash_equals($signatureExpected, $signatureProvided)) {
            return null;
        }

        $payloadRaw = $this->base64UrlDecode($payloadEncoded);
        if ($payloadRaw === null) {
            return null;
        }

        $payload = json_decode($payloadRaw, true);
        if (!is_array($payload)) {
            return null;
        }

        $tokenAddonId = is_int($payload['addon_id'] ?? null) ? $payload['addon_id'] : 0;
        $issuedAt = is_int($payload['iat'] ?? null) ? $payload['iat'] : 0;
        $expiresAt = is_int($payload['exp'] ?? null) ? $payload['exp'] : 0;
        $returnUrl = is_string($payload['return_url'] ?? null) ? $payload['return_url'] : '';
        $now = time();

        if ($tokenAddonId !== $expectedAddonId || $issuedAt <= 0 || $expiresAt <= $issuedAt || $expiresAt < $now) {
            return null;
        }

        return [
            'addon_id' => $tokenAddonId,
            'return_url' => $returnUrl,
            'iat' => $issuedAt,
            'exp' => $expiresAt,
        ];
    }

    private function getSigningKey(): ?string
    {
        $secret = $this->normalizeStringValue(Factory::getConfig()->get('secret', ''));

        if ($secret === '') {
            return null;
        }

        return $secret;
    }

    private function normalizeStringValue(mixed $value): string
    {
        if (is_scalar($value)) {
            return (string) $value;
        }

        if (is_object($value) && method_exists($value, '__toString')) {
            return (string) $value;
        }

        return '';
    }

    private function checkSameOriginRequest(): bool
    {
        /** @var CMSApplication $app */
        $app = Factory::getApplication();
        $server = $app->input->server;
        $origin = trim((string) $server->getString('HTTP_ORIGIN', ''));
        $referer = trim((string) $server->getString('HTTP_REFERER', ''));

        if ($origin !== '') {
            return $this->isSameOriginUrl($origin);
        }

        if ($referer !== '') {
            return $this->isSameOriginUrl($referer);
        }

        return false;
    }

    private function checkRateLimit(int $addonId): bool
    {
        $clientKey = $this->getRateLimitClientKey();
        if ($clientKey === '') {
            return false;
        }

        $cacheKey = 'contactform_rate_' . hash('sha256', $addonId . ':' . $clientKey);
        $now = time();

        try {
            /** @var CacheControllerFactoryInterface $cacheFactory */
            $cacheFactory = Factory::getContainer()->get(CacheControllerFactoryInterface::class);
            /** @var OutputController $cache */
            $cache = $cacheFactory->createCacheController('output', [
                'caching' => true,
                'defaultgroup' => 'plg_sppagebuilder_contactform',
                'lifetime' => (int) ceil(self::RATE_LIMIT_WINDOW_SECONDS / 60),
                'storage' => 'file',
            ]);

            $state = $cache->get($cacheKey);
            if ($state === false || !is_array($state)) {
                $state = ['window_start' => 0, 'count' => 0];
            }

            $cachedWindowStart = $state['window_start'] ?? 0;
            $cachedCount = $state['count'] ?? 0;
            $windowStart = is_scalar($cachedWindowStart) ? (int) $cachedWindowStart : 0;
            $count = is_scalar($cachedCount) ? (int) $cachedCount : 0;

            if ($windowStart <= 0 || ($now - $windowStart) >= self::RATE_LIMIT_WINDOW_SECONDS) {
                $windowStart = $now;
                $count = 0;
            }

            if ($count >= self::RATE_LIMIT_MAX_SUBMISSIONS) {
                return false;
            }

            if (!$cache->store(
                ['window_start' => $windowStart, 'count' => $count + 1],
                $cacheKey
            )) {
                return false;
            }

            return true;
        } catch (Throwable $exception) {
            $this->logSecurityEvent('Rate limit check failed.', Log::ERROR, [
                'addon_id' => $addonId,
                'error' => $exception->getMessage(),
            ]);

            return false;
        }
    }

    private function getRateLimitClientKey(): string
    {
        /** @var CMSApplication $app */
        $app = Factory::getApplication();
        $server = $app->input->server;
        $ip = trim((string) $server->getString('REMOTE_ADDR', ''));

        if ($ip === '') {
            return '';
        }

        return $ip;
    }

    private function isSameOriginUrl(string $url): bool
    {
        $target = parse_url($url);
        $base = parse_url(Uri::root());

        if (!is_array($target) || !is_array($base)) {
            return false;
        }

        $targetHost = strtolower((string) ($target['host'] ?? ''));
        $baseHost = strtolower((string) ($base['host'] ?? ''));

        if ($targetHost === '' || $baseHost === '' || $targetHost !== $baseHost) {
            return false;
        }

        $targetScheme = strtolower((string) ($target['scheme'] ?? ''));
        $baseScheme = strtolower((string) ($base['scheme'] ?? ''));

        if ($targetScheme !== '' && $baseScheme !== '' && $targetScheme !== $baseScheme) {
            return false;
        }

        $targetPort = (int) ($target['port'] ?? 0);
        $basePort = (int) ($base['port'] ?? 0);

        if ($targetPort > 0 && $basePort > 0 && $targetPort !== $basePort) {
            return false;
        }

        return true;
    }

    private function base64UrlEncode(string $value): string
    {
        return rtrim(strtr(base64_encode($value), '+/', '-_'), '=');
    }

    private function base64UrlDecode(string $value): ?string
    {
        $value = strtr($value, '-_', '+/');
        $padding = strlen($value) % 4;

        if ($padding > 0) {
            $value .= str_repeat('=', 4 - $padding);
        }

        $decoded = base64_decode($value, true);

        return $decoded === false ? null : $decoded;
    }

    /** @param array<string, scalar|null> $context */
    private function logSecurityEvent(string $message, int $level, array $context = []): void
    {
        if ($context !== []) {
            $pairs = [];

            foreach ($context as $key => $value) {
                $safeValue = str_replace(["\r", "\n"], ' ', (string) $value);
                $pairs[] = $key . '=' . $safeValue;
            }

            $message .= ' [' . implode(', ', $pairs) . ']';
        }

        try {
            Log::add($message, $level, 'plg_sppagebuilder_contactform');
        } catch (Throwable $exception) {
            // Do not block form handling if logging is unavailable.
        }
    }

}
