<?php

declare(strict_types=1);

namespace dasbit\apiclient;

use Exception;
use JsonException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Client as GuzzleClient;

class Client
{
    /**
     * Instance of http client
     *
     * @var GuzzleClient $http
     */
    private GuzzleClient $http;

    /**
     * Target host for this client
     *
     * @var string $host
     */
    private string $host;

    /**
     * Access token
     *
     * @var string|null $token
     */
    private ?string $token = null;


    /**
     * @var array|string[][] $defaultRequestOptions
     */
    protected array $defaultRequestOptions = [
        'headers' => [
            'Content-Type' => 'application/json',
            'User-Agent' => 'SimpleHttpClient/v1',
        ]
    ];

    /**
     * Token place for authenticated requests
     *
     * @var array|string[][]
     */
    protected array $tokenPlace = [
        'query' => [
            'token' => '{token}'
        ],
        //or
//        'headers' => [
//            'Authorization' => 'Bearer {token}'
//        ]
    ];

    /**
     * Client constructor.
     *
     * @param GuzzleClient $http
     * @param string $host
     * @param array $defaultRequestOptions
     * @param array $tokenPlace
     */
    public function __construct(GuzzleClient $http, string $host, array $defaultRequestOptions = [], array $tokenPlace = [])
    {
        $this->http = $http;
        $this->host = $host;
        if (!empty($defaultRequestOptions)) {
            $this->defaultRequestOptions = $defaultRequestOptions;
        }
        if (!empty($tokenPlace)) {
            $this->tokenPlace = $tokenPlace;
        }
    }

    /**
     * Sets token
     *
     * @param string|null $token
     */
    public function setToken(?string $token) : void
    {
        $this->token = $token;
    }

    /**
     * Returns token
     *
     * @return string|null
     */
    public function getToken() : ?string
    {
        return $this->token;
    }

    /**
     * Authenticates and store token
     *
     * @param $login
     * @param $password
     * @return bool
     * @throws ClientException
     */
    public function authenticate($login, $password) : bool
    {
        //todo realize more flexible
        $res = $this->request('get', 'auth', [
            'query' => compact('login', 'password')
        ]);

        if ($res['code'] === 200) {
            $this->setToken($res['body']['token'] ?? null);
            return true;
        }
        return false;
    }

    /**
     * Request
     *
     * @param $method
     * @param $path
     * @param array $options
     * @return array|null
     * @throws ClientException
     */
    public function request($method, $path, array $options = []) : ?array
    {
        $options = array_merge_recursive($this->defaultRequestOptions, $options);
        try {
            if ($this->token !== null) {
                $where = array_key_first($this->tokenPlace);
                $key = array_key_first($this->tokenPlace[$where]);
                $value = $this->tokenPlace[$where][$key];
                $options[$where][$key] = str_replace('{token}', $this->token, $value);
            }
            $response = $this->http->request($method, $this->host . $path, $options);
            $code = (int) $response->getStatusCode();
            $headers = $response->getHeaders();
            $body = $this->handleResponseBody((string) $response->getBody(), $headers);
            $error = null;
        } catch (RequestException $e) {
            $headers = null;
            $body = null;
            if ($e->getResponse() !== null) {
                $headers = $e->getResponse()->getHeaders();
                $body = $this->handleResponseBody((string) $e->getResponse()->getBody(), $headers);
            }
            $code = (int) $e->getCode();
            $error = $e->getMessage();
        } catch (Exception $e) {
            throw new ClientException($e->getMessage(), 1, $e);
        }
        return compact('code', 'body', 'headers', 'error', 'options');
    }

    /**
     * Async requests
     *
     * @param array $requestData
     * @return string[]
     */
    public function asyncRequests(array $requestData): array
    {
        return ['coming_soon'];
    }

    /**
     * Handles response body
     *
     * @param string $body
     * @param array $headers
     * @return mixed
     * @throws JsonException
     */
    protected function handleResponseBody(string $body, $headers = [])
    {
        if (isset($headers['Content-Type'])
            && in_array('application/json', $headers['Content-Type'], true)) {
            return json_decode($body, true, 512, JSON_THROW_ON_ERROR);
        }
        return $body;
    }
}
