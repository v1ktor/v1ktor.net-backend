<?php
namespace v1ktor\core;

class FrontController
{
    const DEFAULT_CONTROLLER = "Index";
    const DEFAULT_ACTION     = "index";

    private $controller    = self::DEFAULT_CONTROLLER;
    private $action        = self::DEFAULT_ACTION;
    private $params        = array();
    private $basePath      = "v1ktor.net/";
    
    public function __construct()
    {
        $this->parseUri();
    }
    
    private function parseUri()
    {
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

    }

    public function getController()
    {
        return $this->controller;
    }

    public function getAction()
    {
        return $this->action;
    }

    public function getParams()
    {
        return $this->params;
    }

    public function setController($controller)
    {
        $controller = ucfirst(strtolower($controller)) . "Controller";
        $class = '\\v1ktor\\app\\controllers\\'.$controller;
        if (!class_exists($class) || $controller === "FrontController") {
            throw new \InvalidArgumentException(
                "The action controller '$controller' has not been defined.");
        }
        $this->controller = $controller;
        return $this;
    }
    
    public function setAction($action)
    {
        $reflector = new \ReflectionClass($this->controller);
        if (!$reflector->hasMethod($action)) {
            throw new \InvalidArgumentException(
                "The controller action '$action' has been not defined.");
        }
        $this->action = $action;
        return $this;
    }
    
    public function setParams(array $params)
    {
        $this->params = $params;
        return $this;
    }


    public function run()
    {
        $class = '\\v1ktor\\app\\controllers\\'.$this->controller;
        call_user_func_array(array(new $class, $this->action), $this->params);
    }

}