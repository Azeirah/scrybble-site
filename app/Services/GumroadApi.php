<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Psr\Http\Message\ResponseInterface;

class GumroadApi
{
    const API_ROOT = 'https://api.gumroad.com/v2/';
    private Client $client;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => self::API_ROOT,
            'query' => [
                'access_token' => config('services.gumroad.token')
            ]
        ]);
    }

    /**
     */
    public function saleById(string $sale_id)
    {
        $contents = Cache::remember("gumroad:sales:$sale_id", Carbon::now()->addHour(),
            fn() => $this->client->get("sales/$sale_id")->getBody()->getContents()
        );
        return json_decode($contents, true);
    }

    /**
     *
     * @link https://app.gumroad.com/api#licenses
     * @param string $license
     * @return ResponseInterface
     * @throws GuzzleException
     */
    public function verifyLicense(string $license): ResponseInterface
    {
        return $this->client->post('licenses/verify', [
            'json' => [
                'product_permalink' => 'remarkable-to-obsidian',
                'license_key' => $license
            ]
        ])->withHeader('Content-Type', 'application/json');
    }
}
