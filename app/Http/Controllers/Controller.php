<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: '1.0.0',
    title: 'UEE API',
    description: 'Ukrainian Energy Exchange — REST API',
)]
#[OA\Server(url: 'http://localhost:8080', description: 'Local development')]
abstract class Controller {}
