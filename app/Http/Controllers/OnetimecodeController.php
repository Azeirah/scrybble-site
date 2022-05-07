<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateOnetimecodeRequest;
use App\Services\RMapi;

class OnetimecodeController extends Controller
{
    public function create(CreateOnetimecodeRequest $request, RMapi $RMapi) {
        try {
            $RMapi->authenticate($request->get('code'));
        } catch (\InvalidArgumentException $e) {
            return response('Wrong code');
        } catch (\Exception $e) {
            return response($e->getMessage());
        }

        return response('Authenticated');
    }
}
