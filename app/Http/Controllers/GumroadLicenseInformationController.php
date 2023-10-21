<?php

namespace App\Http\Controllers;

use App\Services\GumroadApi;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Auth;
use JsonException;

class GumroadLicenseInformationController extends Controller
{
    /**
     * @throws GuzzleException | ClientException | JsonException
     */
    public function __invoke(GumroadApi $gumroadApi)
    {
        $license = Auth::user()->gumroadLicense->license;
        $response = [
            'license' => $license,
        ];

        try {
            $res = $gumroadApi->verifyLicense($license);
            $info = json_decode($res->getBody()->getContents(), JSON_THROW_ON_ERROR | JSON_OBJECT_AS_ARRAY);
            $purchase = $info['purchase'];

            $cancelled = $purchase['subscription_cancelled_at'];
            $ended = $purchase['subscription_ended_at'];
            $failed = $purchase['subscription_failed_at'];

            $response['exists'] = true;
            $response['licenseInformation'] = [
                "uses" => $info['uses'],
                "order_number" => $purchase['order_number'],
                "sale_id" => $purchase['sale_id'],
                "subscription_id" => $purchase['subscription_id'],
                "active" => $cancelled === null && $ended === null&& $failed === null
            ];
        } catch (ClientException $e) {
            if ($e->getCode() === 404) {
                $response['exists'] = false;
            } else {
                throw $e;
            }
        }

        return $response;
    }
}
