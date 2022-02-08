<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator;

class ShopifyController extends Controller
{
    public function index()
    {
        // fetching all shop metafield
        $shop = Auth::user();
        $shop_endpoints = Config::get('constants.api_endpoints');
        $shop_metafields = $shop->api()->rest('GET', $shop_endpoints["shop_metafield"])["body"]["metafields"];

        return view('dashboard.shop_metafield', [
            "shop_metafields" => $shop_metafields
        ]);
    }

    public function create()
    {
        // fetching all shop metafield
        $shop = Auth::user();
        $shop_endpoints = Config::get('constants.api_endpoints');
        $shop_metafields = $shop->api()->rest('GET', $shop_endpoints["shop_metafield"])["body"]["metafields"];

        // collecting all namespace in simple array in order to send unique array to the view.
        $namespaces = [];
        foreach ($shop_metafields as $shop_metafield) {
            array_push($namespaces, $shop_metafield['namespace']);
        }

        // getting all datatypes and examples of metafield.
        $value_types = get_metafield_value_types();

        // sending view file with all the data it needs.
        return view('dashboard.create_shop_metafield', [
            "value_types" => $value_types,
            "namespaces" => array_unique($namespaces)
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
            $shop_metafield = $shop->api()->rest('POST', $shop_endpoints["shop_metafield"], $metafield);
            if ($shop_metafield['errors']) {
                return Response::json(array(
                    'success' => false,
                    'errors' => $shop_metafield["body"],
                    "submitted" => $request->input()
                ), 400);
            } else {
                return Response::json(array('success' => true, "shopify_response" => $shop_metafield, "metafield" => $metafield), 200);
            }
        }
    }

    public function edit(Request $request)
    {
        $shop_endpoints = Config::get('constants.api_endpoints');
        $shop = Auth::user();
        $shop_metafield_by_id = $shop->api()->rest('GET', $shop_endpoints['single_shop_metafield']($request->id))["body"]["metafield"];
        if ($shop_metafield_by_id['value_type'] == "json_string") {
            $shop_metafield_by_id['value_type'] = "json";
        }
        $shop_metafields = $shop->api()->rest('GET', $shop_endpoints["shop_metafield"])["body"]["metafields"];
        $namespaces = [];
        foreach ($shop_metafields as $shop_metafield) {
            array_push($namespaces, $shop_metafield['namespace']);
        }
        $value_types = get_metafield_value_types();
        return view('dashboard.update_shop_metafield', [
            "namespaces" => array_unique($namespaces),
            "shop_metafields" => $shop_metafields,
            "shop_metafield_by_id" => $shop_metafield_by_id,
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
        $shop_metafield_by_id = $shop->api()->rest('GET', $shop_endpoints['single_shop_metafield']($request->id))["body"]["metafield"];


        $boolean_conf = ["true" => true, "false" => false];
        $request->request->set('value', ($request->value == 'true' || $request->value == 'false') && $request->value_type == 'boolean' ? $boolean_conf[$request->value] : $request->value);
        $request->request->add(["key" => $shop_metafield_by_id["key"], "namespace" => $shop_metafield_by_id["namespace"]]);
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
            $shop_metafield = $shop->api()->rest('PUT', $shop_endpoints["update_shop_metafield"]($request->id), $metafield);
            if ($shop_metafield['errors']) {
                return Response::json(array(
                    'success' => false,
                    'errors' => $shop_metafield["body"],
                    "submitted" => $request->input()
                ), 400);
            } else {
                return Response::json(array('success' => true, "shopify_response" => $shop_metafield, "metafield" => $metafield), 200);
            }
        }
    }

    public function destroy(Request $request)
    {
        $shop_endpoints = Config::get('constants.api_endpoints');
        $shop = Auth::user();
        $delete_metafield = $shop->api()->rest('DELETE', $shop_endpoints["delete_shop_metafield"]($request->id), ["metafield_id" => $request->id]);
        return json_encode($delete_metafield);
    }
}
