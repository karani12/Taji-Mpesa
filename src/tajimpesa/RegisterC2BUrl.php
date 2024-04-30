<?php
namespace TajiMpesa;
require 'vendor/autoload.php';

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;



class RegisterC2BUrl
{
    private $headers;
    private $baseUrl;
    private $body;
    private  $client;


    public function __construct($mode = 'sandbox', $headers, $body)
    {
        $this->client = new Client();
        $this->body = $body;
        $this->headers = $headers;
        $this->baseUrl = ($mode == 'sandbox') ?
            'https://sandbox.safaricom.co.ke/mpesa/c2b/v1/registerurl' :
            'https://api.safaricom.co.ke/mpesa/c2b/v1/registerurl';
    }

    public function registerUrl()
    {

        try {
            $response = $this->client->post($this->baseUrl, [
                'headers' => [
                    'Authorization' => $this->headers['Authorization'],
                    'Content-Type' => 'application/json'
                ],
                'body' => $this->body
            ]);

            return $response->getBody()->getContents();
        } catch (RequestException $e) {
            return json_encode([
                'error' => 'HTTP request failed',
                'message' => $e->getMessage()
            ]);
        }
    }
}
