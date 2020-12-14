<?php

declare(strict_types=1);

namespace dasbit\apiclient;

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Client as Guzzle;

class ClientTest extends \PHPUnit\Framework\TestCase
{
    private function getClientInstanceWithMocks(
        array $mocks = [],
        $defaultRequestOptions = [],
        $tokenPlace = []
    ): Client {
        foreach ($mocks as $mock) {
            $responses[] = new Response(
                $mock['code'],
                $mock['headers'],
                is_array($mock['body'])
                    ? json_encode($mock['body'], JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_LINE_TERMINATORS | JSON_THROW_ON_ERROR)
                    : $mock['body']
            );
        }
        $guzzle = new Guzzle(['handler' => HandlerStack::create(new MockHandler($responses))]);
        return new Client($guzzle, 'http://fake-host.com/', $defaultRequestOptions, $tokenPlace);
    }

    private function arraysAreSimilar(array $a, array $b): bool
    {
        foreach ($a as $k => $v) {
            if (is_array($v) && !$this->arraysAreSimilar($v, $b[$k])) {
                return false;
            } else {
                if (!isset($b[$k]) || $v !== $b[$k]) {
                    return false;
                }
            }
        }
        return true;
    }

    public function testAuthenticateSuccess()
    {
        $token = 'dsfd79843r32d1d3dx23d32d';
        $client = $this->getClientInstanceWithMocks([[
            'code' => 200,
            'headers' => ['Content-Type' => 'application/json'],
            'body' => [
                'status' => 'OK',
                'token' => $token,
            ],
        ]]);
        self::assertTrue($client->authenticate('test', 'test'));
        self::assertSame($token, $client->getToken());
    }

    public function testAuthenticateFail()
    {
        $client = $this->getClientInstanceWithMocks([[
            'code' => 400,
            'headers' => ['Content-Type' => 'application/json'],
            'body' => [
                'status' => 'ERROR',
            ]
        ]]);
        self::assertFalse($client->authenticate('test', 'test'));
        self::assertNull($client->getToken());
    }

    public function testRequestsSuccess()
    {
        $body =
        <<<JSONBODY
{
	"status": "OK",
	"active": "1",
	"blocked": false,
	"created_at": 1587457590,
	"id": 23,
	"name": "Ivanov Ivan",
	"permissions": [
    	{
        	"id": 1,
        	"permission": "comment"
    	},
    	{
        	"id": 2,
        	"permission": "upload photo"
    	},
    	{
        	"id": 3,
        	"permission": "add event"
    	}
	]
}
JSONBODY;

        $code = 200;
        $client = $this->getClientInstanceWithMocks([
            [
                'code' => $code,
                'headers' => ['Content-Type' => 'application/json'],
                'body' => $body
            ]
        ]);
        $body = json_decode($body, true, 512, JSON_THROW_ON_ERROR);
        $response = $client->request('get', 'fake');

        self::assertTrue($this->arraysAreSimilar($body, $response['body']));
        self::assertSame($code, $response['code']);
    }

    public function testRequestFail()
    {
        $code = 400;
        $body =  [
            'status' => "ERROR"
        ];
        $client = $this->getClientInstanceWithMocks([
            [
                'code' => $code,
                'headers' => ['Content-Type' => 'application/json'],
                'body' => $body
            ]
        ]);
        $response = $client->request('get', 'fake');
        self::assertTrue($this->arraysAreSimilar($body, $response['body']));
        self::assertSame($code, $response['code']);
    }

    public function testTokenPlace()
    {
        $body = ['salam' => 'aleikum'];
        $token = 'dsfd79843r32d1d3dx23d32d';
        $defaultRequestOptions = [
            'headers' => [
                'Content-Type' => 'application/json',
                'User-Agent' => 'SimpleHttpClient/v1',
            ]
        ];
        $tokenPlace = [
            'headers' => [
                'Authorization' => 'Bearer {token}'
            ],
        ];
        $client = $this->getClientInstanceWithMocks([
            [
                'code' => 200,
                'headers' => ['Content-Type' => 'application/json'],
                'body' => [
                    'status' => 'OK',
                    'token' => $token,
                ],
            ],
            [
                'code' => 200,
                'headers' => ['Content-Type' => 'application/json'],
                'body' => [
                    'status' => 'OK',
                    'body' => $body,
                ],
            ]
        ], $defaultRequestOptions, $tokenPlace);
        self::assertTrue($client->authenticate('fake', 'fake'));
        self::assertSame($token, $client->getToken());
        $tokenPlace['headers']['Authorization'] = str_replace('{token}', $client->getToken(), $tokenPlace['headers']['Authorization']);
        $options = array_merge_recursive($defaultRequestOptions, $tokenPlace);
        $response = $client->request('post', 'fake');
        self::assertTrue($this->arraysAreSimilar($response['options'], $options));
    }

//    public function testClientErrors()
//    {
//      //todo
//    }
}
