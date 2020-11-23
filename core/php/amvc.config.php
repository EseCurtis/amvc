<?php
    $amvc_config = [
        "request_url" => $_SERVER["SCRIPT_URI"],

        "website_url" => "http://localhost/newamvc",

        "queries" => ["q1" => "SELECT * FROM `flat`"],

        "database" => ["host" => "localhost", "username" => "root", "password" => "", "name" => "xeep"],

        "views"=>[
            "init" => "home",
            "home" => [
                "title" => "Home",
                "source" => "home.php",
            ],
            "404" => [
                "title" => "Home",
                "source"=>"404.php"
            ],
            "apepe"=>[
                "title" => "ape",
                "source" => "home.php",
                "views"=>[
                    "home" => [
                        "title" => "homex",
                        "source" => "home.php",
                    ],
                ]
            ]
        ]
    ];
?>