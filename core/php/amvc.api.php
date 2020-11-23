<?php
    include (__DIR__).'/amvc.config.php';
    include (__DIR__).'/amvc.php';
    $index = new AMVC($amvc_config);
    $index->connect_database();

    include (__DIR__)."/amvc.functions.php";
    $api = new Api_controller();
    $api->execute(); 
?>