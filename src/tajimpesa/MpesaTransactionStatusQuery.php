<?php

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class MpesaTransactionStatusQuery
{
    private $client;
    private $headers;
    private $baseUrl;

    public function __construct($headers, $mode = 'sandbox')
    {
        $this->client = new Client();
        $this->headers = $headers;
        $this->baseUrl = ($mode == 'sandbox') ?
            'https://sandbox.safaricom.co.ke/mpesa/transactionstatus/v1/query' :
            'https://api.safaricom.co.ke/mpesa/transactionstatus/v1/query';
    }

    public function queryTransactionStatus($requestData)
    {
        try {
            $response = $this->client->post($this->baseUrl, [
                'headers' => [
                    'Authorization' => $this->headers['Authorization'],
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


