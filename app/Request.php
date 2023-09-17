<?php

class Request
{
    public function __construct()
    {
        $this->getRequestData();
        $this->setGetParams();
        $this->controllerName = $this->createControllerName();
        $this->endpointName = $this->createEndpointName();
        $this->param = $this->createParam();        
    }
    
    public function getControllerName(): string
    {
        return $this->controllerName;
    }

    public function getEndpointName(): string
    {
        return $this->endpointName;
    }

    public function getParam(): null|string|int
    {
        return $this->param;
    }

    private function getRequestData(): void
    {
        $request = $_SERVER['REQUEST_URI'];
        $getParamsStart = strpos($request, "?");
        $request = $getParamsStart === false ? $request : substr($request, 0, $getParamsStart);
        $request = explode("/", $request);
        $request = array_values(array_filter($request));
        if(in_array($request[0], self::$reservedEndpoints))
        {
            $request[2] = $request[1]; // Move param from endpoint name to param position
            $request[1] = $request[0] == "" ? "index" : $request[0]; 
            $request[0] = "home";
        }
        $request[1] ??= "index";
        $this->request = $request;
    }

    private function setGetParams(): void
    {
        $this->getParams = $_GET;
    }

    private function createControllerName(): string
    {
        $controller = $this->request[0];
        
        return $controller;
    }

    private function createEndpointName(): string
    {
        $endpoint = str_replace("-", "_", $this->request[1]);
        return $endpoint;
    }

    private function createParam(): null|string|int
    {
        $param = $this->request[2] ?? null;
        if($param == null)
        {
            return null;
        }
        return $param;
    }

    private readonly string $controllerName;
    private readonly ?string $endpointName;
    private readonly null|string|int $param;
    private readonly array $getParams;
    private readonly array $request;
    private static array $reservedEndpoints = [
        "",
        "sign-in",
        "sign-out",
        "sign-up",
        "forgot-password",
    ];
}
