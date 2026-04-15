<?php
/**
 * Generic error view — used when a specific error_* status view is missing,
 * or for custom error payloads with $title / $message.
 *
 * @var string|null $title
 * @var string|null $message
 * @var int|null    $code    Optional HTTP-style code from handler
 */
$htmlTitle = $title ?? lang('Errors.whoops');
$heading   = $title ?? lang('Errors.heading_error');
$body      = $message ?? lang('Errors.weHitASnag');
$messageHtml = '<p class="mb-0">' . nl2br(esc($body)) . '</p>';

$isLoggedIn = function_exists('session') && session()->get('isLoggedIn');
$homeUrl    = $isLoggedIn ? base_url('welcome') : base_url();
$homeLabel  = lang('Errors.backHome');
$showLogin  = !$isLoggedIn;

$errorDisplayCode = isset($code) && is_numeric($code) ? (string) (int) $code : '—';

include __DIR__ . DIRECTORY_SEPARATOR . 'optima_error_shell.php';
