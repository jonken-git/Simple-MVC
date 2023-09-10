<?php

class Server
{
    use Singleton;

    private function __construct() {}

    public static function serve(): void
    {
        try {
            self::createInstance();
            self::createRequest();
            self::instantiateController();
            self::callEndpoint(); 
        } catch (RouteException $e) {
            // Re-route to error page
            require_once(__CONTROLLERS__ . "Errors.php");
            $endpoint = $e->getEndpointName();
            $view = self::$instance->createViewPath("Errors", $endpoint);
            self::$instance->controller = new Errors($view);
            self::$instance->controller->request = self::$instance->request;
            self::$instance->controller->$endpoint();
        } catch(Error $e) {
            dd(["FATAL ERROR" => $e]);
        }
        self::close();
    }

    private static function close(): void
    {
        self::$instance = null;
        die();
    }

    private static function instantiateController()
    {
        $controller = self::$instance->request->getControllerName();
        $controller = ucfirst($controller);
        require_once(__CONTROLLERS__ . "Controller.php");
        try {
            require_once(__CONTROLLERS__ . $controller . ".php");
        } catch(Error $e) {
            throw new NotFoundException($e);
        }
        $viewFile = self::createView();
        self::$instance->controller = new $controller($viewFile);
    }

    private static function createView(): string
    {
        $viewFile = self::createViewPath();
        if(!file_exists($viewFile))
        {
            throw new NotFoundException();
        }
        return $viewFile;
    }

    private static function createViewPath(?string $controller = null, ?string $viewName = null): string
    {
        $controller = $controller ?? self::$instance->request->getControllerName();
        $viewName = $viewName ?? self::$instance->request->getEndpointName();
        $viewPath = __VIEWS__ . $controller . "/" . $viewName . ".php";
        return $viewPath;
    }

    private static function callEndpoint()
    {
        $endpoint = self::$instance->request->getEndpointName();
        $param = self::$instance->request->getParam();
        if(!method_exists(self::$instance->controller, $endpoint)) {
            throw new NotFoundException();
        }
        try {
            self::$instance->controller->$endpoint($param);
        } catch (TypeError $e) {
            throw new BadRequestException($e);
        }
    } 

    private static function createRequest(): void
    {
        self::$instance->request = new Request();
    }

    private static function createInstance(): static
    {
        if(self::$instance == null)
        {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private Request $request;
    private Controller $controller;
}

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
        array_shift($request);
        if(in_array($request[0], self::$reservedEndpoints))
        {
            $request[2] = $request[1]; // Move param from endpoint name to param position
            $request[1] = $request[0] == "" ? "index" : $request[0]; 
            $request[0] = "home";
        }
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