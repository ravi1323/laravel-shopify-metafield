<?php
$shopify_admin_api_version = env('SHOPIFY_API_VERSION', '2021-10');

return [
    "api_endpoints" => [
        "shop_metafield" => "/admin/metafields.json",
        "single_shop_metafield" => function ($id) use ($shopify_admin_api_version) {
            return "/admin/api/" . $shopify_admin_api_version . "/metafields/" . $id . ".json";
        },
        "update_shop_metafield" => function ($id) use ($shopify_admin_api_version) {
            return "/admin/api/" . $shopify_admin_api_version . "/metafields/" . $id . ".json";
        },
        "delete_shop_metafield" => function ($id) use ($shopify_admin_api_version) {
            return "/admin/api/" . $shopify_admin_api_version . "/metafields/" . $id . ".json";
        },
        "products" => "/admin/api/" . $shopify_admin_api_version . "/products.json",
        "product_metafield" => function ($id) use ($shopify_admin_api_version) {
            return "/admin/api/" . $shopify_admin_api_version . "/products/" . $id . "/metafields.json";
        },
        "store_product_metafield" => function ($id) use ($shopify_admin_api_version) {
            return "/admin/api/" . $shopify_admin_api_version . "/products/" . $id . "/metafields.json";
        },
        "single_product_metafield" => function ($product_id, $metafield_id) use ($shopify_admin_api_version) {
            return "/admin/api/" . $shopify_admin_api_version . "/products/" . $product_id . "/metafields/" . $metafield_id . ".json";
        },
        "update_product_metafield" => function ($product_id, $metafield_id) use ($shopify_admin_api_version) {
            return "/admin/api/" . $shopify_admin_api_version . "/products/" . $product_id . "/metafields/" . $metafield_id . ".json";
        },
        "delete_product_metafield" => function ($product_id, $metafield_id) use ($shopify_admin_api_version) {
            return "/admin/api/" . $shopify_admin_api_version . "/products/" . $product_id . "/metafields/" . $metafield_id . ".json";
        },
        "customers" => "/admin/api/" . $shopify_admin_api_version . "/customers.json",
        "customer_metafields" => function ($customer_id) use ($shopify_admin_api_version) {
            return "/admin/api/" . $shopify_admin_api_version . "/customers/" . $customer_id . "/metafields.json";
        },
        "store_customer_metafield" => function ($customer_id) use ($shopify_admin_api_version) {
            return "/admin/api/" . $shopify_admin_api_version . "/customers/" . $customer_id . "/metafields.json";
        },
        "single_customer_metafield" => function ($customer_id, $metafield_id) use ($shopify_admin_api_version) {
            return "/admin/api/" . $shopify_admin_api_version . "/customers/" . $customer_id . "/metafields/" . $metafield_id . ".json";
        },
        "delete_customer_metafield" => function ($customer_id, $metafield_id) use ($shopify_admin_api_version) {
            return "/admin/api/" . $shopify_admin_api_version . "/customers/" . $customer_id . "/metafields/" . $metafield_id . ".json";
        },
        "collections" => "/admin/api/" . $shopify_admin_api_version . "/collects.json",
        "collection_metafields" => function ($collection_id) use ($shopify_admin_api_version) {
            return "/admin/api/" . $shopify_admin_api_version . "/collections/" . $collection_id . "/metafields.json";
        },
        "single_collection" => function ($collection_id) use ($shopify_admin_api_version) {
            return "/admin/api/" . $shopify_admin_api_version . "/collections/" . $collection_id . ".json";
        }
    ],
];
