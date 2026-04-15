<?php
/**
 * @var string $message From CodeIgniter exception handler
 * @var int    $code    HTTP status (403)
 */
$htmlTitle        = lang('Errors.forbidden');
$heading          = lang('Errors.heading_forbidden');
$messageHtml      = ENVIRONMENT !== 'production'
    ? '<div class="text-break">' . nl2br(esc($message)) . '</div>'
    : '<p class="mb-0">' . esc(lang('Errors.sorryForbidden')) . '</p>';
$isLoggedIn       = function_exists('session') && session()->get('isLoggedIn');
$homeUrl          = $isLoggedIn ? base_url('welcome') : base_url();
$homeLabel        = lang('Errors.backHome');
$showLogin        = !$isLoggedIn;
$errorDisplayCode = '403';

include __DIR__ . DIRECTORY_SEPARATOR . 'optima_error_shell.php';
