<?php
class RouteException extends Exception
{
    public function __construct(string $message, int $code, Throwable $previous = null)
    {
        parent::__construct($message, $code);
        $this->previous = $previous;
        $this->endpointName = "index";
    }
    
    public function getEndpointName(): string
    {
        return $this->endpointName;
    }

    protected ?Throwable $previous;
    protected string $endpointName;
}

class BadRequestException extends RouteException
{
    public function __construct(Throwable $previous = null)
    {
        parent::__construct("400 Bad Request", 400, $previous);
        $this->endpointName = "bad_request";
    }
}

class UnauthorizedException extends RouteException
{
    public function __construct(Throwable $previous = null)
    {
        parent::__construct("401 Unauthorized", 401, $previous);
        $this->endpointName = "unauthorized";
    }
}

class PaymentRequiredException extends RouteException
{
    public function __construct(Throwable $previous = null)
    {
        parent::__construct("402 Payment Required", 402, $previous);
        $this->endpointName = "payment_required";
    }
}

class ForbiddenException extends RouteException
{
    public function __construct(Throwable $previous = null)
    {
        parent::__construct("403 Forbidden", 403, $previous);
        $this->endpointName = "forbidden";
    }
}

class NotFoundException extends RouteException
{
    public function __construct(Throwable $previous = null)
    {
        parent::__construct("404 Not Found", 404, $previous);
        $this->endpointName = "not_found";
    }
}

class MethodNotAllowedException extends RouteException
{
    public function __construct(Throwable $previous = null)
    {
        parent::__construct("405 Method Not Allowed", 405, $previous);
        $this->endpointName = "method_not_allowed";
    }
}