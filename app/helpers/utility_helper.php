<?php

function get_metafield_value_types()
{
    return [
        [
            "api_name" => "single_line_text_field",
            "example" => "product_reference",
            "type" => "string"
        ],
        [
            "api_name" => "multi_line_text_field",
            "example" => "Ingredients:\nFlour\nWater\nMilk\nEggs",
            "type" => "string"
        ],
        [
            "api_name" => "page_reference",
            "example" => "gid://shopify/OnlineStorePage/1",
            "type" => "string"
        ],
        [
            "api_name" => "product_reference",
            "example" => "gid://shopify/Product/1",
            "type" => "string"
        ],
        [
            "api_name" => "variant_reference",
            "example" => "gid://shopify/ProductVariant/1",
            "type" => "string"
        ],
        [
            "api_name" => "variant_reference",
            "example" => "gid://shopify/ProductVariant/1",
            "type" => "string"
        ],
        [
            "api_name" => "file_reference",
            "example" => "gid://shopify/MediaImage/123",
            "type" => "string"
        ],
        [
            "api_name" => "number_integer",
            "example" => "10",
            "type" => "integer"
        ],
        [
            "api_name" => "number_decimal",
            "example" => "10.4",
            "type" => "string"
        ],
        [
            "api_name" => "date",
            "example" => "2021-02-02",
            "type" => "string"
        ],
        [
            "api_name" => "date_time",
            "example" => "2021-01-01T12:30:00",
            "type" => "string"
        ],
        [
            "api_name" => "url",
            "example" => "https://www.shopify.com",
            "type" => "string"
        ],
        [
            "api_name" => "json",
            "example" => "[{ \"k\": \"v1\" }, { \"k\": \"v2\" }]",
            "type" => "json"
        ],
        [
            "api_name" => "boolean",
            "example" => "true",
            "type" => "boolean"
        ],
        [
            "api_name" => "color",
            "example" => "#fff123",
            "type" => "string"
        ],
        [
            "api_name" => "weight",
            "example" => "{ \"unit\": \"kg\", \"value\": 2.5 }",
            "unit" => [
                "oz", "lg", "g", "kg"
            ],
            "type" => "json"
        ],
        [
            "api_name" => "weight",
            "example" => "{ \"unit\": \"ml\", \"value\": 20.0 }",
            "unit" => [
                "ml", "cl", "l", "m3", "us_fl_oz", "us_pt", "us_qt", "us_gal", "imp_fl_oz", "imp_pt", "imp_qt", "imp_gal"
            ],
            "type" => "json"
        ],
        [
            "api_name" => "dimension",
            "example" => "{ \"unit\": \"cm\", \"value\": 25.0 }",
            "unit" => [
                "in", "ft", "yd", "mm", "cm", "m"
            ],
            "type" => "json"
        ],
        [
            "api_name" => "rating",
            "example" => "{ \"value\": \"3.5\", \"scale_min\": \"1.0\", \"scale_max\": \"5.0\" }",
            "type" => "json"
        ],
    ];
}
