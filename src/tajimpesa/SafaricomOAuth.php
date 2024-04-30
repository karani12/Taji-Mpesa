<?php

namespace TajiMpesa;

require 'vendor/autoload.php';


session_start();

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use TajiMPesa\Exceptions\HTTPException\HTTPException as TajiMPesaHTTPException;

class SafaricomOAuth
{
    private $authorizationHeader;
    private $client;
    private static $instance;

    public function __construct($password, $username, $mode = 'sandbox')
    {
        $this->authorizationHeader = self::encodeCredentials($username, $password);

        $baseUrl = ($mode == 'sandbox') ?
            'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials' :
            'https://api.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';


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
     * Get the access token from the Safaricom OAuth API
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
