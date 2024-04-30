<?php

use Dotenv\Dotenv;
use TajiMpesa\MpesaOAuth;
use TajiMpesa\MpesaSTKPush;

it('make a successful stk push', function () {
    $dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
    $dotenv->load();
    $password = $_ENV['PASSWORD'];
    $passkey = $_ENV['PASSKEY'];
    $username = $_ENV['USERNAME'];
    $phone_number=$_ENV['PHONE_NUMBER'];
    $shortCode = intval($_ENV['SHORTCODE']);
    $call_back = $_ENV('CALLBACK');


    $headers = [
        'Authorization' => 'Bearer ' . (new MpesaOAuth($password, $username))->getAccessToken(),
        'Content-Type' => 'application/json'
    ];

    $body = json_encode([
        'BusinessShortCode' => $shortCode,
        'Password' => base64_encode($shortCode . $passkey . date('YmdHis')),
        'Timestamp' => date('YmdHis'),
        'TransactionType' => 'CustomerPayBillOnline',
        'Amount' => 1,
        'PartyA' => 254708374149,
        'PartyB' => $shortCode,
        'PhoneNumber' => $phone_number,
        'CallBackURL' => 'https://d909-41-90-189-93.ngrok-free.app/validation',
        'AccountReference' => 'CompanyXLTD',
        'TransactionDesc' => 'Payment of X'
    ]);

    $mpesa = new MpesaSTKPush($headers);
    $response = $mpesa->processRequest($body);
    var_dump($response);
    expect($response)->not->toBeEmpty();
    expect($response)->toBeString();
    $response = json_decode($response, true);
    expect($response['ResponseDescription'])->toBe('Success. Request accepted for processing');
    expect($response['ResponseCode'])->toBe('0');
})->skip();
