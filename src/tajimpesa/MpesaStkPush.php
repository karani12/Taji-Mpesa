<?php
namespace TajiMpesa;
require 'vendor/autoload.php';

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
/**
 * MpesaSTKPush class for initiating M-Pesa STK Push transactions.
 *
 * This class allows you to initiate M-Pesa Secure Transaction (STK) Push transactions
 * by sending a POST request to the Safaricom API endpoint.
 *
 * @package TajiMpesa
 */
class MpesaSTKPush
{
    private $client;
    private $headers;
    private $baseUrl;

     /**
     * Constructor method for initializing MpesaSTKPush instance.
     *
     * @param array $headers HTTP headers for the request.
     * @param string $mode (Optional) API environment mode ('sandbox' or 'production').
     */
    public function __construct($headers,$mode='sandbox')
    {
        $this->client = new Client();
        $this->headers = $headers;
        $this->baseUrl = ($mode == 'sandbox') ?
            'https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest' :
            'https://api.safaricom.co.ke/mpesa/stkpush/v1/processrequest';
    }
   /**
     * Initiate an M-Pesa STK Push transaction.
     *
     * This method sends a POST request to the Safaricom API endpoint to initiate an STK Push transaction.
     * It expects the response body to be returned.
     *
     * @param string $requestData JSON-encoded request data.
     * @return string Response body containing transaction details.
     */
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


