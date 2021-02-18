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

    include (__DIR__)."/amvc.classes.php";
    include (__DIR__)."/amvc.global.php";
    include (__DIR__)."/amvc.functions.php";

    $DB = null;

    class AMVC{

        public 
        $values, 
        $javascript_enabled = true, 
        $database;

        private 
        $controller_type = "view", 
        $views, 
        $matched_view, 
        $current_view, 
        $request_url, 
        $website_url, 
        $compared_view, 
        $view_inheritance, 
        $value_count = 0, 
        $project_info;

        function __construct($amvc_configuration)
        {
            global $reserved;
            //declare the variables for executuion
            $this->request_url   = @$_REQUEST["url"] ? sanitize_url($_REQUEST["url"]) : "";
            $this->website_url   = $amvc_configuration["website_url"];
            $this->views         = (array) $amvc_configuration["views"];
            $this->database      = (array) $amvc_configuration["database"];
            $this->project_info  = (array) @$amvc_configuration["project_info"];
            $this->reserved      = $reserved;

            //preparing comparison values
            $this->values        = explode("/", trim($this->request_url));
            $this->compared_view = $this->values[0];
        }

        //insertion of js link to amvc.js
        public function insert_js()
        {
            global $dom_globals, $js_inc;
            $nl  = $dom_globals["newline"];
            $tab = $dom_globals["tab"];
            if(@$this->javascript_enabled == true && @$this->controller_type !== "reserved"){                
                $dom    = new DOM();
                echo $dom->script_tag(_URL_."/amvc-files/amvc.js");
            }
        }

        //connecting database
        public function connect_database()
        {
            global $DB;
            if(!empty($this->database)){
                $DB = new mysqli($this->database['host'], $this->database['username'], $this->database['password'], $this->database['name']);
            }
        }

        //Updating project info
        private function update_project_info()
        {
            global $PROJECT_INFO;
            $PROJECT_INFO = $this->project_info;
            
        }

        //Checking if request url matches reserved links
        private function check_reserved(){
            foreach ($this->reserved as $reserve) {
                $reserve["source"] = "../".$reserve["source"];
            }
            if(@$this->compared_view == "amvc-files"){
                if(@$this->reserved[@$this->values[1]]){
                    $this->controller_type = "reserved"; 
                    $this->matched_view = @$this->reserved[@$this->values[1]];
                }  
            }
        }

        //defining global variables
        private function define_globals()
        {
            defined("_URL_") ? "" :define("_URL_", $this->website_url);; 
        }

        //comparison of request to views to see matches
        private function check_matched()
        {
            if(@$this->views[$this->compared_view])
            {
                //verify if the other part of request url is valid 
                $this->view_inheritance = (array) $this->views[$this->compared_view];
                if(@$this->view_inheritance["views"])
                {
                    $this->view_inheritance["views"] = (array) $this->view_inheritance["views"];

                    //iteration throungh values (the other part of request url)
                    foreach ($this->values as $value)
                    {
                        if(@$this->values_count > 0){
                            @$this->view_inheritance["views"] = (array) @$this->view_inheritance["views"];
                            if(@$this->view_inheritance["views"][$value])
                            {
                                $this->view_inheritance = (array) $this->view_inheritance["views"][$value];
                            }else{
                                $this->view_inheritance = null;
                            }
                        }
                        $this->values_count = 1;
                    }
                }
            }else if(@$this->views[$this->views["init"]] && strlen($this->compared_view) < 1){
                $this->view_inheritance = $this->views[$this->views["init"]];
                $this->compared_view    = $this->views["init"];
            }

            //verify if there is any match else redirect to 404
            if($this->view_inheritance){
                $this->matched_view = (array) $this->view_inheritance;
            }else{
                $this->matched_view = (array) $this->views["404"];
            }
        }

        //to execute proccessed info
        function run()
        {
            global $DB;

            //execute all neccessary functions
            $this->define_globals();
            $this->check_matched();
            $this->check_reserved();
            $this->update_project_info();
            $this->insert_js();
            
            //loading the view
            $load_view = new Controller(["type"=>$this->controller_type, "key"=>$this->matched_view["source"], "view"=>$this->compared_view]);
            $load_view->execute();
        }
    }
?>