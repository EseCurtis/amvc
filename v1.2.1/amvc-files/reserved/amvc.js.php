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

    //define header for javascript files
    header("Content-Type: application/javascript");

    //define relative path
    $r_path = (__DIR__)."/../..";

    $config = (array) include_json("$r_path/project.json");

    //initialise amvc class
    $amvc   = new AMVC($config);

    
    
    //define the scripts an their paths
    $amvc_scripts = [
        "page_data" => [
            "title" =>"Page data",
            "code"  =>"let amvcPageData = {view:'".end($amvc->values)."', siteUrl :'"._URL_."', projectData : ".json_encode($amvc->project_info)."};"
        ],
        "amvc.settings.js"  => [
            "title" => "Page settings [amvc.settings.js]",
            "code"  => file_get_contents("$r_path/bin/js/amvc.settings.js")
        ],
        "amvc.global.js"  => [
            "title" => "Global variables [amvc.global.js]",
            "code"  => file_get_contents("$r_path/bin/js/amvc.global.js")
        ],
        "amvc.functions.js"  => [
            "title" => "Main AMVC functions [amvc.functions.js]",
            "code"  => file_get_contents("$r_path/bin/js/amvc.functions.js")
        ],
        "amvc.main.js"  => [
            "title" => "Main AMVC class [amvc.main.js]",
            "code"  => file_get_contents("$r_path/bin/js/amvc.main.js")
        ],
        "amvc.handlers.js"  => [
            "title" => "AMVC Page Handlers [amvc.handlers.js]",
            "code"  => file_get_contents("$r_path/bin/js/amvc.handlers.js")
        ]
    ];

    //printing out the defined scripts
    foreach ($amvc_scripts as $script) {

        if($script["title"]){
            json_format($script["code"]);
        }
        print "//";
        print $script["title"];
        print "\n";
        print $script["code"];
        print "\n";
        print "\n";
    }

?>