<?php
/**
 * AMVC
 *
 * A lightweight framework for working with multiple Inter APIs
 *
 *
 * Copyright (c) 2020 - 2021, Esecodes Tech
 *
 * Any owner of this copy of the AMVC package is licenced to make 
 * changes to code and to give feed back about any errors on the
 * package
 *
 *
 * @package	AMVC
 * @author	Ese Curtis .A.
 * @copyright	Copyright (c) 2020 - 2021, Ese Curtis .A.
 * @link	https://amvc-framework.netlify.com
 * @since	Version 1.2.0
 */ 


    //root path 
    $path = explode('\\', __DIR__);
    array_pop($path);
    $path = implode('/', $path);
    define('_PATH_', "$path/..");

    //intializing $DB variable 
    $DB = null;

    //initializing $PROJECT_INFO variable
    $PROJECT_INFO = null;

    //setting the reserved paths.
    $reserved = [
        "amvc.api"=>[
            "source"=>"amvc.api.php"
        ],
        "amvc.js"=>[
            "source"=>"amvc.js.php"
        ],
        "amvc.css"=>[
            "source"=>"amvc.css.php"
        ]
    ];

    //setting the dom globals.
    $dom_globals = [
       "tab" => "   ",
       "newline"=> "\n",
    ];

    #[BUG]
    $js_inc = "false";
?>