<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator;

class VariantController extends Controller
{
    public function index()
    {
        $shop_endpoints = Config::get('constants.api_endpoints');

        $shop = Auth::user();
        $products = $shop->api()->rest('GET', $shop_endpoints["products"])["body"]["products"];

        $variants_metafields = [];
        foreach ($products as $product) {
            $variants = $shop->api()->rest('GET', $shop_endpoints["variants"]($product["id"]))["body"]["variants"];
            foreach ($variants as $variant) {
                $variant_metafields = $shop->api()->rest('GET', $shop_endpoints["variant_metafields"]($variant["id"]))["body"]["metafields"];
                if (count($variant_metafields) > 0) {
                    $variants_metafields[$variant["id"]] = $variant_metafields;
                    $variants_metafields[$variant["id"]]["variant_id"] = $variant["id"];
                    $variants_metafields[$variant["id"]]["variant_title"] = $variant["title"];
                    $variants_metafields[$variant["id"]]["product_id"] = $product["id"];
                    $variants_metafields[$variant["id"]]["product_title"] = $product["title"];
                }
            }
        }

        return view('dashboard.variant_metafield', [
            "variants_metafields" => $variants_metafields
        ]);
    }

    public function create()
    {
        $shop_endpoints = Config::get('constants.api_endpoints');

        /**
         * collecting all products
         */
        $shop = Auth::user();
        $products = $shop->api()->rest('GET', $shop_endpoints["products"])["body"]["products"];


        /**
         * collecting all namespaces of all product's variant => metafield's namespace.
         */
        $namespaces = [];
        foreach ($products as $product) {
            $variants = $shop->api()->rest('GET', $shop_endpoints['variants']($product["id"]))["body"]["variants"];
            if (count($variants) > 0) {
                foreach ($variants as $variant) {
                    $variant_metafields = $shop->api()->rest('GET', $shop_endpoints["variant_metafields"]($variant['id']))["body"]["metafields"];
                    foreach ($variant_metafields as $variant_metafield) {
                        array_push($namespaces, $variant_metafield["namespace"]);
                    }
                }
            }
        }

        $value_types = get_metafield_value_types();
        return view('dashboard.create_variant_metafield', [
            'namespaces' => array_unique($namespaces),
            'value_types' => $value_types,
            "products" => $products
        ]);
    }

    public function get_product_variants(Request $request)
    {
        $shop_endpoints = Config::get("constants.api_endpoints");

        $shop = Auth::user();
        $validation_rules = array(
            "product_id" => "required"
        );
        $validate = Validator::make($request->input(), $validation_rules);
        if ($validate->fails()) {
            return Response::json(array(
                'success' => false,
                'errors' => $validate->getMessageBag()->toArray(),
                "submitted" => $request->input()
            ), 400);
        } else {
            $variants = $shop->api()->rest('GET', $shop_endpoints["variants"]($request->product_id));
            if ($variants['errors']) {
                return Response::json(array(
                    'success' => false,
                    'errors' => $variants["body"],
                    "submitted" => $request->input()
                ), 400);
            } else {
                return Response::json(array('success' => true, "shopify_response" => $variants["body"]["variants"]), 200);
            }
        }
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
            "product" => "required",
            "variant" => "required",
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
            $variant_metafield = $shop->api()->rest('POST', $shop_endpoints["store_variant_metafield"]($request->variant), $metafield);
            if ($variant_metafield['errors']) {
                return Response::json(array(
                    'success' => false,
                    'errors' => $variant_metafield["body"],
                    "submitted" => $request->input()
                ), 400);
            } else {
                return Response::json(array('success' => true, "shopify_response" => $variant_metafield, "metafield" => $metafield), 200);
            }
        }
    }

    public function edit(Request $request)
    {
        $shop_endpoints = Config::get('constants.api_endpoints');
        $shop = Auth::user();
        $variant_metafield_by_id = $shop->api()->rest('GET', $shop_endpoints['single_variant_metafield']($request->variant_id, $request->id))["body"]["metafield"];
        if ($variant_metafield_by_id['value_type'] == "json_string") {
            $variant_metafield_by_id['value_type'] = "json";
        }
        $variant_metafields = $shop->api()->rest('GET', $shop_endpoints["variant_metafields"]($request->variant_id))["body"]["metafields"];
        $namespaces = [];
        foreach ($variant_metafields as $variant_metafield) {
            array_push($namespaces, $variant_metafield['namespace']);
        }
        $value_types = get_metafield_value_types();
        return view('dashboard.update_variant_metafield', [
            "namespaces" => array_unique($namespaces),
            "variant" => ["id" => $request->variant_id],
            "variant_metafield_by_id" => $variant_metafield_by_id,
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
        $variant_metafield_by_id = $shop->api()->rest('GET', $shop_endpoints['single_variant_metafield']($request->variant, $request->id))["body"]["metafield"];


        $boolean_conf = ["true" => true, "false" => false];
        $request->request->set('value', ($request->value == 'true' || $request->value == 'false') && $request->value_type == 'boolean' ? $boolean_conf[$request->value] : $request->value);
        $request->request->add(["key" => $variant_metafield_by_id["key"], "namespace" => $variant_metafield_by_id["namespace"]]);
        $type_validation = $request->value_type;
        $validation_rules = array(
            "key" => "required",
            "value_type" => 'required',
            "type" => "required",
            "variant" => "required",
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
            $variant_metafield = $shop->api()->rest('PUT', $shop_endpoints["update_variant_metafield"]($request->variant, $request->id), $metafield);
            if ($variant_metafield['errors']) {
                return Response::json(array(
                    'success' => false,
                    'errors' => $variant_metafield["body"],
                    "submitted" => $request->input()
                ), 400);
            } else {
                return Response::json(array('success' => true, "shopify_response" => $variant_metafield, "metafield" => $metafield), 200);
            }
        }
    }

    public function destroy(Request $request)
    {
        $shop_endpoints = Config::get('constants.api_endpoints');
        $shop = Auth::user();
        $delete_metafield = $shop->api()->rest('DELETE', $shop_endpoints["delete_variant_metafield"]($request->variant_id, $request->id), ["metafield_id" => $request->id]);
        return json_encode($delete_metafield);
    }
}
