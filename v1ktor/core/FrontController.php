<?php

class FrontController
{
    const DEFAULT_CONTROLLER = "Index";
    const DEFAULT_ACTION     = "index";
    
    protected $controller    = self::DEFAULT_CONTROLLER;
    protected $action        = self::DEFAULT_ACTION;
    protected $params        = array();
    protected $basePath      = "v1/";
    
    public function __construct() {
        $this->parseUri();
    }
    
    protected function parseUri() {
        $path = trim(parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH), "/");
        $path = preg_replace('/[^a-zA-Z0-9]\//', "", $path);
        //if (strpos($path, $this->basePath) === 0) {
            $path = substr($path, strlen($this->basePath));
        //}

        @list($controller, $action, $params) = explode("/", $path, 3);

        if (isset($controller) && !empty($controller)) {
            $this->setController($controller);
        } else {
            $this->setController($this->controller);
        }
        if (isset($action)) {
            $this->setAction($action);
        }
        if (isset($params)) {
            $this->setParams(explode("/", $params));
        }

        /*
        $path = $_SERVER["REQUEST_URI"];
        $path = trim($path, '/');
        $path = substr($path, strlen($this->basePath));
        $segments = explode('/', $path);
        print_r($segments);
        $this->controller = $segments[0];
        if (isset($this->controller) && !empty($this->controller)) {
            echo 'yes';
        } else {
            echo 'nope';
        }
        /*
        $path = trim(parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH), "/");
        $path = preg_replace('/[^a-zA-Z0-9]\//', "", $path);
        if (strpos($path, $this->basePath) === 0) {
            $path = substr($path, strlen($this->basePath));
        }
        echo $path; */
            //$this->setParams(explode("/", $params));

    }

    public function setController($controller) {
        $controller = ucfirst(strtolower($controller)) . "Controller";
        if (!class_exists($controller) || $controller === "FrontController") {
            throw new InvalidArgumentException(
                "The action controller '$controller' has not been defined.");
        }
        $this->controller = $controller;
        return $this;
    }
    
    public function setAction($action) {
        $reflector = new ReflectionClass($this->controller);
        if (!$reflector->hasMethod($action)) {
            throw new InvalidArgumentException(
                "The controller action '$action' has been not defined.");
        }
        $this->action = $action;
        return $this;
    }
    
    public function setParams(array $params) {
        $this->params = $params;
        return $this;
    }
    
    public function run() {
        call_user_func_array(array(new $this->controller, $this->action), $this->params);
    }

}