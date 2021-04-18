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
    header("Content-Type: text/css");

    //define relative path
    $relative_path = (__DIR__)."/../..";

    $config = (array) include_json("$relative_path/project.json");

    //initialise amvc class
    $amvc   = new AMVC($config);
    
    //define the scripts an their paths
    $amvc_styles = [
        "amvc.handlers.js"  => [
            "title" => "main amvc stylesheet",
            "code"  => file_get_contents("$relative_path/amvc-files/css/amvc.main.css")
        ]
    ];

    //printing out the defined scripts
    foreach ($amvc_styles as $style) {

        if($style["title"]){
            json_format($style["code"]);
        }
        print "/*";
        print $style["title"];
        print "*/";
        print "\n";
        print $style["code"];
        print "\n";
        print "\n";
    }

?>