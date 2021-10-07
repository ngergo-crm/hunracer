<?php


namespace App\Services;


use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\JsonResponse;

class TrainingPeaksService
{
    #CLIENT CONSTANTS
    private const CLIENT_ID = "webfish-kft";
    private const CLIENT_SECRET = "5yEvVd8qt1nAwyWhghoeEEfidpIWNVytd5sWmVC9v0";
    private const CLIENT_SCOPE = "events:read metrics:read workouts:details workouts:read";
    private const CLIENT_SCOPE_ARRAY = ["events:read", "metrics:read", "workouts:details", "workouts:read"];
    public const COOKIE_TOKEN_NAME = 'tpToken';

    #TP OAUTH CONSTANTS TEST
    public const AUTHORIZE_URL_TEST = "https://oauth.sandbox.trainingpeaks.com/OAuth/Authorize";
    public const TOKENEXCHANGE_URL_TEST = "https://oauth.sandbox.trainingpeaks.com/oauth/token";
    public const DEAUTHORIZE_URL_TEST = "https://oauth.sandbox.trainingpeaks.com/oauth/deauthorize";
    public const API_HOST_TEST = "https://api.sandbox.trainingpeaks.com/";


    #TP OAUTH CONSTANTS PROD
    public const AUTHORIZE_URL = "https://oauth.trainingpeaks.com/OAuth/Authorize";
    public const TOKENEXCHANGE_URL = "https://oauth.trainingpeaks.com/oauth/token";
    public const DEAUTHORIZE_URL = "https://oauth.trainingpeaks.com/oauth/deauthorize";
    public const API_HOST = "https://api.trainingpeaks.com/";

    private $test;
    private $session;

    public function apiRequest(string $endpoint, $token, $test = false): array
    {
        $response['response'] = null;
        $this->test = $test;
        if (is_object($token['expires_at'])) {
            $token['expires_at'] = json_decode($token['expires_at'], true);
        }
        $isRefreshTokenNeeded = \DateTime::__set_state($token['expires_at']) < new \DateTime('now');
        if ($token and $endpoint) {
            $refreshSuccessful = false;
            if ($isRefreshTokenNeeded) {
                $token = $this->refreshTpToken($token);
                $refreshSuccessful = $token ? true : false;
            }
            if (!$isRefreshTokenNeeded or $refreshSuccessful) {
                $apiUrl = !$this->test ? self::API_HOST : self::API_HOST_TEST;
                $endpoint = $apiUrl . $endpoint;
                $response['response'] = $this->handleTpApiRequest($endpoint, $token['access_token']); //$this->handleTpRequest($endpoint, "", $header);
            }
        }
        if ($token) {
            $response['token'] = $token;
        }
        return $response;
    }

    private function refreshTpToken(array $token)
    {
        $tokenExchangeUrl = !$this->test ? self::TOKENEXCHANGE_URL : self::TOKENEXCHANGE_URL_TEST;
        $grantType = 'refresh_token';
        $refreshToken = $token['refresh_token'];
        $postfield = sprintf('client_id=%s&grant_type=%s&refresh_token=%s&client_secret=%s', self::CLIENT_ID, $grantType, $refreshToken, self::CLIENT_SECRET);
        $token = $this->handleTpRequest($tokenExchangeUrl, $postfield);
        $token = !isset($token['error']) ? $this->setTokenExpiresAt($token) : null;
        return $token;
    }

    /**
     * @param string $endpoint
     * @param string|null $postfields
     * @param array $header
     */
    private function handleTpRequest(string $endpoint, ?string $postfields, array $header = [])
    {
        $postfields = !$postfields ? "" : $postfields;
        $curl = curl_init();
        $parameters = array(
            CURLOPT_URL => $endpoint,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $postfields,
            CURLOPT_HEADER => $header
        );
        curl_setopt_array($curl, $parameters);

        $response = curl_exec($curl);
        if ($response === FALSE) {
            die(curl_error($curl));
        }
        curl_close($curl);
        return json_decode($response, true);
    }

    /**
     * @param array $token
     * @return array
     */
    private function setTokenExpiresAt(?array $token)
    {
        if ($token) {
            $token['expires_at'] = new \DateTime(sprintf('now + %d seconds', $token['expires_in']));
        }
        return $token;
    }

    /**
     * @param $token
     * @return Cookie
     */
    public function createTpCookie(array $token): Cookie
    {
        return $cookie = new Cookie(
            self::COOKIE_TOKEN_NAME,
            json_encode($token, true),
            time() + (2 * 365 * 24 * 60 * 60)  // Expires 2 years.
        );
    }

    private function handleTpApiRequest(string $endpoint, string $token)
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $endpoint,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                "Authorization: Bearer $token"
            ),
        ));

        $response = curl_exec($curl);
        if ($response === FALSE) {
            die(curl_error($curl));
        }
        curl_close($curl);
        return json_decode($response, true);
    }

}
