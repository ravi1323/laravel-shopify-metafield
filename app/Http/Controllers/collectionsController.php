<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;

class collectionsController extends Controller
{
    public function index()
    {
        $shop = Auth::user();
        $shop_endpoints = Config::get('constants.api_endpoints');
        $collections = $shop->api()->rest('GET', $shop_endpoints["collections"])["body"]["collections"];
        return $collections;
    }
}
