<?php
namespace TajiMpesa;
require 'vendor/autoload.php';

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;


/**
 * RegisterC2BUrl class for registering callback URLs for M-Pesa C2B API.
 *
 * This class allows you to register callback URLs for M-Pesa Customer to Business (C2B) transactions.
 * It provides a method to send a POST request to the Safaricom API endpoint for registering callback URLs.
 *
 * @package TajiMpesa
 */
class RegisterC2BUrl
{
    private $headers;
    private $baseUrl;
    private $body;
    private  $client;

      /**
     * Constructor method for initializing RegisterC2BUrl instance.
     *
     * @param string $mode (Optional) API environment mode ('sandbox' or 'production').
     * @param array $headers HTTP headers for the request.
     * @param string $body JSON-encoded request body.
     */
    
    public function __construct($mode = 'sandbox', $headers, $body)
    {
        $this->client = new Client();
        $this->body = $body;
        $this->headers = $headers;
        $this->baseUrl = ($mode == 'sandbox') ?
            'https://sandbox.safaricom.co.ke/mpesa/c2b/v1/registerurl' :
            'https://api.safaricom.co.ke/mpesa/c2b/v1/registerurl';
    }

  /**
     * Register callback URLs with the Safaricom C2B API.
     *
     * This method sends a POST request to the Safaricom C2B API endpoint to register callback URLs.
     * It expects the response body to be returned.
     *
     * @return string Response body containing the registration status.
     */
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
