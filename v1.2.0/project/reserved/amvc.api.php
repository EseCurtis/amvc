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
    global $amvc_configuration;

    if(@req_var("_amvc_request_")){
        $api = new Api_controller();
        $api->execute(); 
    }else{
        $amvc   = new AMVC($amvc_configuration);
        $amvc->insert_js();
        $render = new Render();
        $render->view("404.php");
    }
?>