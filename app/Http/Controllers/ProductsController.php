<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;

class ProductsController extends Controller
{
    public function index()
    {
        $shop_endpoints = Config::get('constants.api_endpoints');

        /**
         * collecting all product ids
         */
        $shop = Auth::user();
        $products = $shop->api()->rest('GET', $shop_endpoints['products'])["body"]["products"];

        /**
         * collecting all metafield of all products
         */
        $products_metafields = [];
        $products_that_has_metafield = [];
        foreach ($products as $product) {
            $product_metafield = $shop->api()->rest('GET', $shop_endpoints['product_metafield']($product["id"]))["body"]["metafields"];
            if (count($product_metafield) > 0) {
                $products_metafields[$product["id"]] = $product_metafield;
                $products_metafields[$product["id"]]["product_title"] = $product["title"];
                $products_metafields[$product["id"]]["product_id"] = $product["id"];
                // array_push($products_that_has_metafield, $product);
            }
        }

        /**
         * returning view with products and metafield
         */
        return view('dashboard.product_metafield', [
            "products_metafields" => $products_metafields,
            "products" => $products_that_has_metafield
        ]);
    }

    public function create()
    {
        $shop_endpoints = Config::get('constants.api_endpoints');

        /**
         * collecting all product ids
         */
        $shop = Auth::user();
        $products = $shop->api()->rest('GET', $shop_endpoints['products'])["body"]["products"];

        /**
         * collecting all namespaces of all products metafields.
         */
        $namespaces = [];
        foreach ($products as $product) {
            $product_metafields = $shop->api()->rest('GET', $shop_endpoints['product_metafield']($product["id"]))["body"]["metafields"];
            if (count($product_metafields) > 0) {
                $products_metafields[$product["id"]] = $product_metafields;
                foreach ($product_metafields as $product_metafield) {
                    array_push($namespaces, $product_metafield['namespace']);
                }
            }
        }

        $value_types = get_metafield_value_types();
        return view('dashboard.create_product_metafield', [
            'namespaces' => array_unique($namespaces),
            'value_types' => $value_types,
            "products" => $products
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
            "product_id" => "required",
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
            $shop_metafield = $shop->api()->rest('POST', $shop_endpoints["store_product_metafield"]($request->product_id), $metafield);
            return Response::json(array('success' => true, "shopify_response" => $shop_metafield, "metafield" => $metafield), 200);
        }
    }

    public function edit(Request $request)
    {
        $shop_endpoints = Config::get('constants.api_endpoints');
        $shop = Auth::user();
        $product_metafield_by_id = $shop->api()->rest('GET', $shop_endpoints['single_product_metafield']($request->product_id, $request->id))["body"]["metafield"];
        if ($product_metafield_by_id['value_type'] == "json_string") {
            $product_metafield_by_id['value_type'] = "json";
        }
        $product_metafields = $shop->api()->rest('GET', $shop_endpoints["product_metafield"]($request->product_id))["body"]["metafields"];
        $namespaces = [];
        foreach ($product_metafields as $product_metafield) {
            array_push($namespaces, $product_metafield['namespace']);
        }
        $value_types = get_metafield_value_types();
        return view('dashboard.update_product_metafield', [
            "namespaces" => array_unique($namespaces),
            "product" => ["id" => $request->product_id],
            "product_metafield_by_id" => $product_metafield_by_id,
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
        $product_metafield_by_id = $shop->api()->rest('GET', $shop_endpoints['single_product_metafield']($request->product_id, $request->id))["body"]["metafield"];


        $boolean_conf = ["true" => true, "false" => false];
        $request->request->set('value', ($request->value == 'true' || $request->value == 'false') && $request->value_type == 'boolean' ? $boolean_conf[$request->value] : $request->value);
        $request->request->add(["key" => $product_metafield_by_id["key"], "namespace" => $product_metafield_by_id["namespace"]]);
        $type_validation = $request->value_type;
        $validation_rules = array(
            "key" => "required",
            "value_type" => 'required',
            "type" => "required",
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
                    "namespace" => $request->namespace,
                    "key" => $request->key,
                    "value" => $request->value,
                    "value_type" => $request->value_type == 'json' ? "json_string" : $request->value_type,
                    "type" => $request->type,
                    "description" => $request->description
                ],
            ];
            $shop_endpoints = Config::get('constants.api_endpoints');
            $product_metafield = $shop->api()->rest('PUT', $shop_endpoints["update_product_metafield"]($request->product_id, $request->id), $metafield);
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
        $delete_metafield = $shop->api()->rest('DELETE', $shop_endpoints["delete_product_metafield"]($request->product_id, $request->id), ["metafield_id" => $request->id]);
        return json_encode($delete_metafield);
    }
}
