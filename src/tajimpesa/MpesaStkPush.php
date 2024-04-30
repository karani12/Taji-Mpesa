<?php
namespace TajiMpesa;
require 'vendor/autoload.php';

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class MpesaSTKPush
{
    private $client;
    private $headers;
    private $baseUrl;

    public function __construct($headers,$mode='sandbox')
    {
        $this->client = new Client();
        $this->headers = $headers;
        $this->baseUrl = ($mode == 'sandbox') ?
            'https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest' :
            'https://api.safaricom.co.ke/mpesa/stkpush/v1/processrequest';
    }

    public function processRequest($requestData)
    {
        try {
            $response = $this->client->post($this->baseUrl, [
                'headers' => [
                    'Authorization' => $this->headers['Authorization'],
                    'Content-Type' => 'application/json',
                ],
               'body' => $requestData,
            ]);

            return $response->getBody()->getContents();
        } catch (GuzzleException $e) {
            return $e->getMessage();
        }
    }
}


