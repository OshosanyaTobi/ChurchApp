<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SanctumController
{
    
    public function issueToken(Request $request) : Response{
        return new Response('Hi');
    }


}
