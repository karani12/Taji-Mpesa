<?php

require 'vendor/autoload.php';

use Dotenv\Dotenv;
use TajiMpesa\MpesaOAuth;
use TajiMpesa\SafaricomOAuth;



it('can get access token', function () {
    $dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
    $dotenv->load();
    $password = $_ENV['PASSWORD'];
    $username = $_ENV['USERNAME'];

    $Ouath = new MpesaOAuth($password, $username);
    $accessToken = $Ouath->getAccessToken();
    expect($accessToken)->not->toBeEmpty();
    expect($accessToken)->toBeString();
})->skip();

