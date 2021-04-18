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

    global $DB;

    //for escaping string when database is connected.
    function esc_str($value){
        global $DB;
        if(@mysqli_real_escape_string()){
            return mysqli_real_escape_string($DB, trim($value));
        }else{
            return trim($value);
        }
        
    }

    //for sanitizing url.
    function sanitize_url($url){
        $url = trim($url, "/");
        return filter_var($url, FILTER_SANITIZE_URL);
    }

    //for including json files as arrays.
    function include_json($url){
        return json_decode(file_get_contents($url));
    }

    //for hybrid hashing.
    function md52($str){
        return md5((bin2hex(md5($str))*bin2hex(md5($str)))/50);
    }

    //for getting request variables.
    function req_var($request_key){
        global $DB;
        return str_replace("\\", "", esc_str($_REQUEST[$request_key]));
    }

    //for  making easy sql queries.
    function db_query($query_script){
        global $DB;
        return mysqli_query($DB, $query_script);
    }

    //for validating request variables.
    function multiple_isset($request_keys){
        $is_set = 0;
        foreach ($request_keys as $key => $value) {
            if(req_var($value)){
                $is_set++;
            }
        }
        if($is_set == count($request_keys)){
            return 1;
        }
        return 0;
    }

    //for escaping a string to become a valid url value.
    function special_escape($str){
        $str = preg_replace('~[^\\pL0-9_]+~u', '_', $str);
        $str = trim($str, '-');
        $str = iconv("utf-8", "us-ascii//TRANSLIT", $str);
        $str = strtolower($str);
        $str = preg_replace('~[^-a-z0-9_]+~', '', $str);
        return $str;
    }

    //for adding php code.
    function add_code($path){
        include _PATH_."/$path";
    }

    //for catching errors.
    function error_catch($func, $error = null){
        echo !$func ? $error: "";
    }

    function json_format($json_code){
        $json_code = str_replace("{","{\n", $json_code);
        $json_code = str_replace("}","}\n", $json_code);
        $json_code = str_replace("[","[\n", $json_code);
        $json_code = str_replace(",",",\n", $json_code);

        return($json_code);
    }
?>