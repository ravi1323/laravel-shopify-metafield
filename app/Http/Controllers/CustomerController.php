<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator;

class CustomerController extends Controller
{
    public function index()
    {
        $shop = Auth::user();
        $shop_endpoints = Config::get('constants.api_endpoints');
        $customers = $shop->api()->rest('GET', $shop_endpoints["customers"])["body"]["customers"];

        $customers_metafields = [];
        foreach ($customers as $customer) {
            $customer_metafield = $shop->api()->rest("GET", $shop_endpoints["customer_metafields"]($customer["id"]))["body"]["metafields"];
            if (count($customer_metafield) > 0) {
                $customers_metafields[$customer["id"]] = $customer_metafield;
                $customers_metafields[$customer["id"]]["customer_id"] = $customer["id"];
                $customers_metafields[$customer["id"]]["customer_email"] = $customer["email"];
            }
        }

        return view('dashboard.customer_metafield', [
            "customers_metafields" => $customers_metafields
        ]);
    }

    public function create()
    {
        $shop_endpoints = Config::get('constants.api_endpoints');

        /**
         * collecting all customer ids
         */
        $shop = Auth::user();
        $customers = $shop->api()->rest('GET', $shop_endpoints['customers'])["body"]["customers"];

        /**
         * collecting all namespaces of all products metafields.
         */
        $namespaces = [];
        foreach ($customers as $customer) {
            $customer_metafields = $shop->api()->rest('GET', $shop_endpoints['customer_metafields']($customer["id"]))["body"]["metafields"];
            if (count($customer_metafields) > 0) {
                foreach ($customer_metafields as $customer_metafield) {
                    array_push($namespaces, $customer_metafield['namespace']);
                }
            }
        }

        $value_types = get_metafield_value_types();
        return view('dashboard.create_customer_metafield', [
            'namespaces' => array_unique($namespaces),
            'value_types' => $value_types,
            "customers" => $customers
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
            "customer" => "required",
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
            $customer_metafield = $shop->api()->rest('POST', $shop_endpoints["store_customer_metafield"]($request->customer), $metafield);
            if ($customer_metafield['errors']) {
                return Response::json(array(
                    'success' => false,
                    'errors' => $customer_metafield["body"],
                    "submitted" => $request->input()
                ), 400);
            } else {
                return Response::json(array('success' => true, "shopify_response" => $customer_metafield, "metafield" => $metafield), 200);
            }
        }
    }

    public function edit(Request $request)
    {
        $shop_endpoints = Config::get('constants.api_endpoints');
        $shop = Auth::user();
        $customer_metafield_by_id = $shop->api()->rest('GET', $shop_endpoints['single_customer_metafield']($request->customer_id, $request->id))["body"]["metafield"];
        if ($customer_metafield_by_id['value_type'] == "json_string") {
            $customer_metafield_by_id['value_type'] = "json";
        }
        $customer_metafields = $shop->api()->rest('GET', $shop_endpoints["customer_metafields"]($request->customer_id))["body"]["metafields"];
        $namespaces = [];
        foreach ($customer_metafields as $customer_metafield) {
            array_push($namespaces, $customer_metafield['namespace']);
        }
        $value_types = get_metafield_value_types();
        return view('dashboard.update_customer_metafield', [
            "namespaces" => array_unique($namespaces),
            "customer" => ["id" => $request->customer_id],
            "customer_metafield_by_id" => $customer_metafield_by_id,
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
        $customer_metafield_by_id = $shop->api()->rest('GET', $shop_endpoints['single_product_metafield']($request->customer, $request->id))["body"]["metafield"];


        $boolean_conf = ["true" => true, "false" => false];
        $request->request->set('value', ($request->value == 'true' || $request->value == 'false') && $request->value_type == 'boolean' ? $boolean_conf[$request->value] : $request->value);
        $request->request->add(["key" => $customer_metafield_by_id["key"], "namespace" => $customer_metafield_by_id["namespace"]]);
        $type_validation = $request->value_type;
        $validation_rules = array(
            "key" => "required",
            "value_type" => 'required',
            "type" => "required",
            "customer" => "required",
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
            $customer_metafield = $shop->api()->rest('PUT', $shop_endpoints["update_product_metafield"]($request->customer, $request->id), $metafield);
            if ($customer_metafield['errors']) {
                return Response::json(array(
                    'success' => false,
                    'errors' => $customer_metafield["body"],
                    "submitted" => $request->input()
                ), 400);
            } else {
                return Response::json(array('success' => true, "shopify_response" => $customer_metafield, "metafield" => $metafield), 200);
            }
        }
    }

    public function destroy(Request $request)
    {
        $shop_endpoints = Config::get('constants.api_endpoints');
        $shop = Auth::user();
        $delete_metafield = $shop->api()->rest('DELETE', $shop_endpoints["delete_customer_metafield"]($request->customer_id, $request->id), ["metafield_id" => $request->id]);
        return json_encode($delete_metafield);
    }
}
