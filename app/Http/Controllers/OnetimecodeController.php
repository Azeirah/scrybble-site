<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\CreateOnetimecodeRequest;
use App\Services\RMapi;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Response;
use InvalidArgumentException;

/**
 *
 */
class OnetimecodeController extends Controller
{
    /**
     * @param CreateOnetimecodeRequest $request
     * @param RMapi $RMapi
     * @return Response|Application|ResponseFactory
     */
    public function create(CreateOnetimecodeRequest $request, RMapi $RMapi): Response|Application|ResponseFactory {
        try {
            $RMapi->authenticate($request->get('code'));
        } catch (InvalidArgumentException) {
            return response('Wrong code');
        } catch (Exception $err) {
            return response($err->getMessage());
        }

        return response('Authenticated');
    }
}
