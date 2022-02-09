<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
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

    public function create()
    {
        $shop_endpoints = Config::get('constants.api_endpoints');

        /**
         * collecting all collection ids
         */
        $shop = Auth::user();
        $collections = $shop->api()->rest('GET', $shop_endpoints['collections'])["body"]["collects"];


        /**
         * collecting all namespaces of all collections metafield's namespace.
         */
        $namespaces = [];
        $collections_detail = [];
        foreach ($collections as $collection) {
            $product_metafields = $shop->api()->rest('GET', $shop_endpoints['collection_metafields']($collection["collection_id"]))["body"]["metafields"];
            if (count($product_metafields) > 0) {
                foreach ($product_metafields as $product_metafield) {
                    array_push($namespaces, $product_metafield['namespace']);
                }
            }
            $single_collection = $shop->api()->rest('GET', $shop_endpoints['single_collection']($collection["collection_id"]))["body"]["collection"];
            array_push($collections_detail, $single_collection);
        }

        $value_types = get_metafield_value_types();
        return view('dashboard.create_collection_metafield', [
            'namespaces' => array_unique($namespaces),
            'value_types' => $value_types,
            "collections" => $collections_detail
        ]);
    }

    public function store(Request $request)
    {
        $boolean_conf = ["true" => true, "false" => false];
        $request->request->set('value', ($request->value == 'true' || $request->value == 'false') && $request->value_type == 'boolean' ? $boolean_conf[$request->value] : $request->value);
        $type_validation = $request->value_type;
        $validation_rules = array(
            "key" => "required",
            "value_type" => 'required',
            "type" => "required",
            "namespace" => "required",
            "collection" => "required",
            "value" => "required|" . $type_validation
        );
        $validate = Validator::make($request->input(), $validation_rules);
        if ($validate->fails()) {
            return Response::json(array(
                'success' => false,
                'errors' => $validate->getMessageBag()->toArray(),
                "submitted" => $request->input()
            ), 400);
        } else {
            $shop = Auth::user();
            $type = "";
            $metafield = [
                "metafield" => [
                    "namespace" => $request->namespace,
                    "key" => $request->key,
                    "value" => $request->value,
                    "value_type" => $request->value_type == 'json' ? "json_string" : $request->value_type,
                    "type" => $request->type,
                    "description" => $request->description
                ],
            ];
            $shop_endpoints = Config::get('constants.api_endpoints');
            $product_metafield = $shop->api()->rest('POST', $shop_endpoints["store_collection_metafield"]($request->collection), $metafield);
            if ($product_metafield['errors']) {
                return Response::json(array(
                    'success' => false,
                    'errors' => $product_metafield["body"],
                    "submitted" => $request->input()
                ), 400);
            } else {
                return Response::json(array('success' => true, "shopify_response" => $product_metafield, "metafield" => $metafield), 200);
            }
        }
    }

    public function edit(Request $request)
    {
        $shop_endpoints = Config::get('constants.api_endpoints');
        $shop = Auth::user();
        $collection_metafield_by_id = $shop->api()->rest('GET', $shop_endpoints['single_collection_metafield']($request->collection_id, $request->id))["body"]["metafield"];
        if ($collection_metafield_by_id['value_type'] == "json_string") {
            $collection_metafield_by_id['value_type'] = "json";
        }
        $collection_metafields = $shop->api()->rest('GET', $shop_endpoints["collection_metafields"]($request->collection_id))["body"]["metafields"];
        $namespaces = [];
        foreach ($collection_metafields as $collection_metafield) {
            array_push($namespaces, $collection_metafield['namespace']);
        }
        $value_types = get_metafield_value_types();
        return view('dashboard.update_collection_metafield', [
            "namespaces" => array_unique($namespaces),
            "collection" => ["id" => $request->collection_id],
            "collection_metafield_by_id" => $collection_metafield_by_id,
            "value_types" => $value_types
        ]);
    }

    public function update(Request $request)
    {
        /**
         * Getting existing key and namespace
         * because shopify doesn't allow updating existing key and namespace
         */
        $shop_endpoints = Config::get('constants.api_endpoints');
        $shop = Auth::user();
        $collection_metafield_by_id = $shop->api()->rest('GET', $shop_endpoints['single_collection_metafield']($request->collection, $request->id))["body"]["metafield"];


        $boolean_conf = ["true" => true, "false" => false];
        $request->request->set('value', ($request->value == 'true' || $request->value == 'false') && $request->value_type == 'boolean' ? $boolean_conf[$request->value] : $request->value);
        $request->request->add(["key" => $collection_metafield_by_id["key"], "namespace" => $collection_metafield_by_id["namespace"]]);
        $type_validation = $request->value_type;
        $validation_rules = array(
            "key" => "required",
            "value_type" => 'required',
            "type" => "required",
            "collection" => "required",
            "namespace" => "required",
            "value" => "required|" . $type_validation
        );
        $validate = Validator::make($request->input(), $validation_rules);
        if ($validate->fails()) {
            return Response::json(array(
                'success' => false,
                'errors' => $validate->getMessageBag()->toArray(),
                "submitted" => $request->input()
            ), 400);
        } else {
            $shop = Auth::user();
            $metafield = [
                "metafield" => [
                    "id" => $request->id,
                    "namespace" => $request->namespace,
                    "key" => $request->key,
                    "value" => (string)$request->value,
                    "type" => $request->type,
                    "value_type" => $request->value_type == 'json' ? "json_string" : $request->value_type,
                    "description" => $request->description
                ],
            ];
            $product_metafield = $shop->api()->rest('PUT', $shop_endpoints["update_collection_metafield"]($request->collection, $request->id), $metafield);
            if ($product_metafield['errors']) {
                return Response::json(array(
                    'success' => false,
                    'errors' => $product_metafield["body"],
                    "submitted" => $request->input()
                ), 400);
            } else {
                return Response::json(array('success' => true, "shopify_response" => $product_metafield, "metafield" => $metafield), 200);
            }
        }
    }

    public function destroy(Request $request)
    {
        $shop_endpoints = Config::get('constants.api_endpoints');
        $shop = Auth::user();
        $delete_metafield = $shop->api()->rest('DELETE', $shop_endpoints["delete_collection_metafield"]($request->collection_id, $request->id), ["metafield_id" => $request->id]);
        return json_encode($delete_metafield);
    }
}
