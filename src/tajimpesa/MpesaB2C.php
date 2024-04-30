<?php

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class MpesaB2CPayment
{
    private $client;
    private $baseUrl;
    private $authToken;

    public function __construct($baseUrl, $authToken)
    {
        $this->client = new Client();
        $this->baseUrl = $baseUrl;
        $this->authToken = $authToken;
    }

    public function makePaymentRequest($requestData)
    {
        try {
            $response = $this->client->post($this->baseUrl, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->authToken,
                    'Content-Type' => 'application/json',
                ],
                'json' => $requestData,
            ]);

            return $response->getBody()->getContents();
        } catch (GuzzleException $e) {
            return $e->getMessage();
        }
    }
}


