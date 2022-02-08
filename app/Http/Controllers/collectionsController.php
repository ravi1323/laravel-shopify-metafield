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
        $collections = $shop->api()->rest('GET', $shop_endpoints["collections"])["body"]["collects"];

        $collections_metafields = [];
        foreach ($collections as $collection) {
            $collection_metafield = $shop->api()->rest("GET", $shop_endpoints["collection_metafields"]($collection["collection_id"]))["body"]["metafields"];
            if (count($collection_metafield) > 0) {
                $collections_metafields[$collection["collection_id"]] = $collection_metafield;
                $single_collection = $shop->api()->rest('GET', $shop_endpoints["single_collection"]($collection["collection_id"]))["body"]["collection"];
                $collections_metafields[$collection["collection_id"]]["collection_id"] = $single_collection["id"];
                $collections_metafields[$collection["collection_id"]]["collection_title"] = $single_collection["title"];
            }
        }
        return view('dashboard.collection_metafield', [
            "collections_metafields" => $collections_metafields
        ]);
    }
}
