<?php
    include (__DIR__)."/amvc.classes.php";
    include (__DIR__)."/amvc.global.php";

    class AMVC{
        public $values, $javascript_enabled = true, $database;
        private $views, $matched_view, $current_view, $request_url, $website_url, $compared_view, $view_inheritance, $value_count = 0;

        function __construct($amvc_configuration)
        {
            //declare the variables for executuion
            $this->request_url = $amvc_configuration["request_url"];
            $this->website_url = $amvc_configuration["website_url"];
            $this->views       = $amvc_configuration["views"];
            $this->database    = $amvc_configuration["database"];

            //subtraction of the main url from request url 
            $this->values        = explode("/", trim(str_ireplace($this->website_url, "", $this->request_url), "/"));
            $this->compared_view = $this->values[0];
            define("_URL_", $this->website_url);
        }

        private function insert_js()
        {
            global $newline, $tab;
            print('<script>'.$newline.$tab.'let _VIEW_  = "'.end($this->values).'";'.$newline.$tab.'const _URL_ = "'._URL_.'"; '.$newline.'</script>'.$newline.'<script src="'._URL_.'/core/js/amvc.js"></script>'.$newline);
        }
        public function connect_database()
        {
            global $DB;
            if(!empty($this->database)){
                $DB = new mysqli($this->database['host'], $this->database['username'], $this->database['password'], $this->database['name']);
            }
        }
        private function check_matched()
        {
            if(@$this->views[$this->compared_view])
            {
                //verify if the other part of request url is valid 
                $this->view_inheritance = $this->views[$this->compared_view];
                if(@$this->view_inheritance["views"])
                {
                    //iteration throungh values (the other part of request url)
                    foreach ($this->values as $value)
                    {
                        if($this->values_count > 0){
                            if(@$this->view_inheritance["views"][$value])
                            {
                                $this->view_inheritance = $this->view_inheritance["views"][$value];
                            }else{
                                $this->view_inheritance = null;
                            }
                        }
                        $this->values_count = 1;
                    }
                }
            }
            //verify if there is any match else redirect to 404
            if($this->view_inheritance){
                $this->matched_view = $this->view_inheritance;
            }else{
                $this->matched_view = $this->views["404"];
            }
        }
        function execute()
        {
            global $DB;
            
            $this->check_matched();
            $this->connect_database();
            $this->javascript_enabled == true ? $this->insert_js() : "";
            $load_view = new Controller(["type"=>"view", "key"=>$this->matched_view["source"]]);
            $load_view->execute();
        }
    }
?>