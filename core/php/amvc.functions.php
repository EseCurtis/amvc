<?php
global $DB;

    function esc_str($value){
        global $DB;
        return mysqli_real_escape_string($DB, trim($value));
    }

    function md52($str){
        return md5((bin2hex(md5($str))*bin2hex(md5($str)))/50);
    }

    function req_var($request_key){
        global $DB;
        return str_replace("\\", "", esc_str($_REQUEST[$request_key]));
    }

    function db_query($query_script){
        global $DB;
        return mysqli_query($DB, $query_script);
    }

    function multiple_isset($request_keys){
        $is_set = 0;
        foreach ($request_keys as $key => $value) {
            if(req_var($value)){
                $is_set++;
            }
        }
        if($is_set == count($req_indexes)){
            return 1;
        }
        return 0;
    }

    function special_escape($str){
        $str = preg_replace('~[^\\pL0-9_]+~u', '_', $str);
        $str = trim($str, '-');
        $str = iconv("utf-8", "us-ascii//TRANSLIT", $str);
        $str = strtolower($str);
        $str = preg_replace('~[^-a-z0-9_]+~', '', $str);
        return $str;
    }

    function add_code($path){
        include(_PATH_.$path);
    }
?>