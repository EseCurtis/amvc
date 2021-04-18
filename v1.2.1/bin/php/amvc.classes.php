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

    $content_dir = "project";
    class Controller
    {
        private $controller_type, $controller_key, $current_view;

        //initialising controller data
        function __construct($controller_data)
        {
            $this->controller_type = $controller_data["type"];
            $this->controller_key  = $controller_data["key"];
            $this->current_view    = @$controller_data["view"];
        }

        //setting up interactors
        private function interact(){
            global $content_dir;
            error_catch(@include $content_dir."/interactors\/".$this->controller_key, "<b>Error:</b> The source file ($this->controller_key) for the api_key ($this->controller_key) is not found found in the interactors directory");
        }
        //setting up props
        private function render_prop(){
            global $content_dir;
            error_catch(@include $content_dir."/props\/".$this->controller_key, "<b>Error:</b> The source file ($this->controller_key) for the prop ($this->controller_key) is not found in the props directory");
        }
        //setting up views
        private function render_view(){
            global $content_dir;
            error_catch(@include $content_dir."/views\/".$this->controller_key, "<b>Error:</b> The source file ($this->controller_key) for the view ($this->current_view) is not found found in the views directory"); 
        }
        //setting up reserved
        private function render_reserved(){
            global $content_dir;
            include $content_dir."/../amvc-files/reserved\/".$this->controller_key;
        }

        //setting up execution
        function execute()
        {
            global $DB, $PROJECT_INFO;

            switch($this->controller_type){
                case "interactor":
                    $this->interact();
                break;
                case "prop":
                    $this->render_prop();
                break;
                case "view":
                    $this->render_view();
                break;
                case "reserved":
                    $this->render_reserved();
                break;
            }
        }
    }

    class Render {
        private $relative_path, $file_type;

        //initializing the relative path and file type for render context
        function __construct($relative_path = "/", $file_type = null)
        {
            $this->relative_path = $relative_path;
            $this->file_type = $file_type ? ".$file_type" : null;
        }

        //rendering views
        function view($_key, $_data = null)
        {
            global $DB, $PROJECT_INFO;

            global $content_dir;
            $current_render = $content_dir."/views/$this->relative_path/$_key$this->file_type";
            include $current_render;
        }

       //rendering props
        function prop($_key, $_data = null)
        {
            global $DB, $PROJECT_INFO;

            global $content_dir;
            $current_render = $content_dir."/props/$this->relative_path/$_key$this->file_type";
            include $current_render;
        }

        //rendering interactors
        function interactor($_key, $_data = null)
        {
            global $DB, $PROJECT_INFO;

            global $content_dir;
            $current_render = $content_dir."/interactors/$this->relative_path/$_key$this->file_type";
            include $current_render;
        }
    }

    //for handling/controlling apis in amvc
    class Api_controller
    {
        private $api_command, $api_data_1, $api_data_2;

        //intializing api data
        function __construct()
        {   
            $api_request = (array) json_decode(req_var("_amvc_request_"));
            @$this->api_command = @$api_request["command"];
            @$this->api_data_1  = @$api_request["data_1"];
            @$this->api_data_2  = @$api_request["data_2"];  
        }

        //check api data errors
        private function check_errors()
        {
            if(empty(@$this->api_command)){
                echo "no command specified!";
            }
        }

        //process request
        private function process_request()
        {
            global $DB, $PROJECT_INFO;

            switch($this->api_command){
                #interactor
                    case '_interaction':
                        $controller_data = [
                            "type" => "interactor",
                            "key"  => $this->api_data_1
                        ];
                        $controller = new Controller($controller_data);
                        $controller->execute();
                    break;
                #sql
                    case '_sql':
                        db_query($this->api_data_1);
                    break;
                }
        }

        //execute processed request
        function execute()
        {
            $this->check_errors();
            $this->process_request();
        }
    }

    //for creating DOM elements
    class DOM
    {
        //for creating script tags
        function script_tag($src = null, $script = null)
        {
            global $dom_globals;
            $nl  = $dom_globals["newline"];
            $src = $src ? ' src="'.$src.'"' : "";

            $script_tag = "<script$src>$script</script>$nl";
            return $script_tag;
        }

        //for creating style tags
        function style_tag($src = null, $style = null)
        {
            global $dom_globals;
            $nl  = $dom_globals["newline"];

            if($src){
                $style_tag = "<link rel=\"stylesheet\" href=\"$src\">$nl";
            }else{
                $style_tag = "<style>$nl $style $nl</style>$nl";
            }
            return $style_tag;
        }
    }
?>