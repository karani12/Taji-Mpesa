<?php

use Dotenv\Dotenv;
use TajiMpesa\RegisterC2BUrl;
use TajiMpesa\SafaricomOAuth;


it('can register c2b url', function () {
    $dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
    $dotenv->load();
    $password = $_ENV['PASSWORD'];
    $username = $_ENV['USERNAME'];
    $shortCode = $_ENV['SHORTCODE'];
    $confirmationUrl = $_ENV['CONFIRMATION_URL'];
    $validationUrl = $_ENV['VALIDATION_URL'];

    $headers = [
        'Authorization' => 'Bearer ' . (new SafaricomOAuth($password, $username))->getAccessToken(),
        'Content-Type' => 'application/json'
    ];

    $body = json_encode([
        'ShortCode' => $shortCode,
        'ResponseType' => 'Completed',
        'ConfirmationURL' => $confirmationUrl,
        'ValidationURL' => $validationUrl
    ]);

    $registerC2BUrl = new RegisterC2BUrl('sandbox', $headers, $body);
    $response = $registerC2BUrl->registerUrl();
    expect($response)->not->toBeEmpty();
    expect($response)->toBeString();

    $response = json_decode($response, true);
    expect($response['ResponseDescription'])->toBe('success');
    expect($response['ResponseCode'])->toBe('0');
})->skip();

