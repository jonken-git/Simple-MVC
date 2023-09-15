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
            self::$instance->controller = new Errors($view, $e);
            self::$instance->controller->request = self::$instance->request;
            self::$instance->controller->$endpoint();
        } catch(Error $e) {
            $endpoint = "index";
            $view = self::$instance->createViewPath("Errors", $endpoint);
            self::$instance->controller = new Errors($view);
            self::$instance->controller->request = self::$instance->request;
            self::$instance->controller->$endpoint($e);
        }
        self::close();
    }

    private static function prepareErrorView(): void
    {

    }

    private static function close(): void
    {
        self::$instance = null;
        die();
    }

    private static function instantiateController(): void
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

