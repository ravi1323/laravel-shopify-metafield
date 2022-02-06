<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;

class CustomerController extends Controller
{
    public function index()
    {
        $shop = Auth::user();
        $shop_endpoints = Config::get('constants.api_endpoints');
        $customers = $shop->api()->rest('GET', $shop_endpoints["customers"])["body"]["customers"];

        $customers_metafields = [];
        foreach ($customers as $customer) {
            $customer_metafields = $shop->api()->rest("GET", $shop_endpoints["customer_metafields"]($customer["id"]))["body"]["metafields"];
            foreach ($customer_metafields as $customer_metafield) {
                array_push($customers_metafields, $customer_metafield);
            }
        }

        dd($customers_metafields);
    }
}
