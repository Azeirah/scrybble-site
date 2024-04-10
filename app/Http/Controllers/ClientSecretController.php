<?php

namespace App\Http\Controllers;

use Laravel\Passport\Passport;

class ClientSecretController extends Controller
{
    public function show()
    {
        $obsidianClient = Passport
            ::clientModel()
            ::query()
            ->where('name', 'obsidian-client')
            ->first('secret');
        return $obsidianClient->secret;
    }
}
