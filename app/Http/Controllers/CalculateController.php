<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;

class CalculateController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Calculate');
    }
}
