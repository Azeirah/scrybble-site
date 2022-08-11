<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreConnectedGumroadLicenseRequest;
use App\Models\GumroadLicense;
use App\Models\User;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RuntimeException;

class ConnectedGumroadLicenseController extends Controller
{
    /**
     */
    public function store(StoreConnectedGumroadLicenseRequest $request)
    {
        /** @var User $user */
        $user = Auth::user();

        $license = $request->get('license');

        if ($user->gumroadLicense !== null && $user->gumroadLicense->valid) {
            throw new RuntimeException('A license is already connected');
        }

        $client = new Client();
        try {
            $res = $client->post('https://api.gumroad.com/v2/licenses/verify', [
                'json' => [
                    'product_permalink' => 'remarkable-to-obsidian',
                    'license_key' => $license
                ]
            ])->withHeader('Content-Type', 'application/json');
        } catch (GuzzleException $e) {
            return redirect('dashboard')->withErrors(["License \"{$license}\" not found, did you fill it in correctly?"]);
        }
        $data = json_decode($res->getBody(), true, 512, JSON_THROW_ON_ERROR);

        if ($data['success']) {
            $user->gumroadLicense()->firstOrCreate([
                'valid' => true,
                'license' => $license
            ]);
            return redirect('dashboard');
        }

        return redirect()->withErrors([$data['message']]);
    }
}
