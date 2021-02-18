<?php
    //execute all neccessary functions
    include 'bin/php/amvc.php';
    
    $config = (array) include_json("project.json");
    $app    = new AMVC($config);

    //uncomment to connect to database when credentials in project.json are correct
    $app->connect_database();
    $app->run();  
?>
