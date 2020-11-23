<?php 
    $content_dir = (__DIR__)."/../../content/";
    class Controller
    {
        private $controller_type, $controller_key;

        function __construct($controller_data)
        {
            $this->controller_type = $controller_data["type"];
            $this->controller_key  = $controller_data["key"];
        }

        private function interact(){
            global $content_dir;
            include $content_dir."/interactors\/".$this->controller_key;
        }
        private function render_prop(){
            global $content_dir;
            include $content_dir."/props\/".$this->controller_key;
        }
        private function render_view(){
            global $content_dir;
            include $content_dir."/views\/".$this->controller_key;
        }

        function execute()
        {
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
            }
        }
    }

    class Render {
        function view($_name, $_data = null){
            global $content_dir;
            include $content_dir."/views\/".$_name;
        }
        function prop($_name, $_data = null){
            global $content_dir;
            include $content_dir."/props\/".$_name;
        }
        function interactor($_name, $_data = null){
            global $content_dir;
            include $content_dir."/interactors\/".$_name;
        }
    }
    class Api_controller
    {
        private $api_command, $api_data_1, $api_data_2;
        function __construct()
        {   
            $api_request = (array) json_decode(req_var("_amvc_request_"));
            $this->api_command = $api_request["command"];
            @$this->api_data_1 = $api_request["data_1"];
            @$this->api_data_2 = $api_request["data_2"];  
        }

        function execute()
        {
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
    }

    class DOM
    {
        function interaction_api($api_key)
        {
            $_api = json_encode(["command"=> "_interaction","data_1"=> $api_key]);
            $_api = '<input type="hidden" name="_amvc_request_" value=\''.$_api.'\'>';
            return $_api;
        }
    }
?>