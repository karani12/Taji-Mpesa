<?php

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
/**
 * MpesaTransactionStatusQuery class for querying the status of M-Pesa transactions.
 *
 * This class allows you to query the status of M-Pesa transactions by sending a POST request
 * to the Safaricom API endpoint for transaction status queries.
 *
 * @package TajiMpesa
 */
class MpesaTransactionStatusQuery
{
    private $client;
    private $headers;
    private $baseUrl;
 /**
     * Constructor method for initializing MpesaTransactionStatusQuery instance.
     *
     * @param array $headers HTTP headers for the request.
     * @param string $mode (Optional) API environment mode ('sandbox' or 'production').
     */

    public function __construct($headers, $mode = 'sandbox')
    {
        $this->client = new Client();
        $this->headers = $headers;
        $this->baseUrl = ($mode == 'sandbox') ?
            'https://sandbox.safaricom.co.ke/mpesa/transactionstatus/v1/query' :
            'https://api.safaricom.co.ke/mpesa/transactionstatus/v1/query';
    }
    
   /**
     * Query the status of an M-Pesa transaction.
     *
     * This method sends a POST request to the Safaricom API endpoint to query the status of an M-Pesa transaction.
     * It expects the response body to be returned.
     *
     * @param array $requestData Request data for the transaction status query.
     * @return string Response body containing the transaction status.
     */
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


