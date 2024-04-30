<?php

namespace TajiMpesa;

require 'vendor/autoload.php';


session_start();

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use TajiMPesa\Exceptions\HTTPException\HTTPException as TajiMPesaHTTPException;

/**
 * MpesaOAuth class for handling OAuth authentication with Mpesa APIs.
 *
 * This class provides methods for obtaining and managing access tokens from
 * the Mpesa OAuth API. It supports both sandbox and production environments.
 *
 * Usage:
 *  - Create an instance of MpesaOAuth with your Mpesa API credentials.
 *  - Call getAccessToken() method to retrieve a valid access token for API requests.
 *
 * @package TajiMpesa
 */
class MpesaOAuth
{
    private $authorizationHeader;
    private $client;
    private static $instance;

    /**
     * Constructor method for initializing MpesaOAuth instance.
     *
     * @param string $password Mpesa API password.
     * @param string $username Mpesa API username.
     * @param string $mode (Optional) API environment mode ('sandbox' or 'production').
     */

    public function __construct($password, $username, $mode = 'sandbox')
    {
        $this->authorizationHeader = self::encodeCredentials($username, $password);

        $baseUrl = ($mode == 'sandbox') ?
            'https://sandbox.Mpesa.co.ke/oauth/v1/generate?grant_type=client_credentials' :
            'https://api.Mpesa.co.ke/oauth/v1/generate?grant_type=client_credentials';


        $this->client = new Client([
            'base_uri' => $baseUrl,
            'headers' => [
                'Authorization' => $this->authorizationHeader,
                'Accept' => 'application/json'
            ]
        ]);
    }



    public static function getInstance($password, $username, $mode = 'sandbox')
    {
        if (self::$instance === null) {
            self::$instance = new self($password, $username, $mode);
        }
        return self::$instance;
    }

    /**
     * Get the access token from the Mpesa OAuth API.
     *
     * This method retrieves a valid access token from the Mpesa OAuth API.
     * If a valid access token is already available in the session and not expired, it returns the stored token.
     * Otherwise, it makes a request to the OAuth API to obtain a new access token.
     *
     * @throws GuzzleException If an error occurs while making the HTTP request.
     * @return string The access token obtained from the OAuth API.
     */

    private function acquireLock($lockName)
    {
        $lockFile = sys_get_temp_dir() . '/' . $lockName . '.lock';
        $lockHandle = fopen($lockFile, 'w');
        if ($lockHandle === false || !flock($lockHandle, LOCK_EX | LOCK_NB)) {
            return false;
        }

        return $lockHandle;
    }

    private static function encodeCredentials($username, $password)
    {

        $authorizationHeader = "Basic " . base64_encode("$username:$password");

        return $authorizationHeader;
    }

    private function releaseLock($lockHandle)
    {
        flock($lockHandle, LOCK_UN);
        fclose($lockHandle);
    }
    /**
     * Get the access token from the Mpesa OAuth API
     * @throws GuzzleException
     * 
     */
    public function getAccessToken()
    {
        // Check if access token is already available and not expired
        if (isset($_SESSION['access_token']) && isset($_SESSION['expires_in']) && isset($_SESSION['timestamp']) && time() < $_SESSION['timestamp'] + $_SESSION['expires_in']) {
            return $_SESSION['access_token'];
        }

        $lockKey = 'access_token_lock';
        $lock = $this->acquireLock($lockKey);

        if (!$lock) {
            // todo, figure out what is to be done
            throw new TajiMPesaHTTPException("Failed to acquire access token lock");
        }

        try {

            $response = $this->client->request('GET', '', [
                'verify' => true
            ]);

            $body = $response->getBody()->getContents();
            $body = json_decode($body, true);

            $_SESSION['access_token'] = $body['access_token'];
            $_SESSION['expires_in'] = $body['expires_in'];
            $_SESSION['timestamp'] = time();

            $this->releaseLock($lock);

            return $_SESSION['access_token'];
        } catch (GuzzleException $e) {
            $this->releaseLock($lock);
            return json_encode([
                'error' => 'HTTP request failed',
                'message' => $e->getMessage()
            ]);
        }
    }
}
