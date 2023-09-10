<?php
abstract class Controller
{
    public array $viewData = [];
    public static array $reservedEndpoints = [
        "",
        "sign-in",
        "sign-out",
        "sign-up",
        "forgot-password",
        "reset-password",
    ];


    public final function __construct(string $view)
    {
        $this->view = $view;
    }

    private function loadModel(): void
    {
    }

    private function prepareView(): void
    {
    }

    private function callEndpointMethod(): void
    {
    }

    private function setView(?string $view = null): void
    {
    }

    protected function renderView(array $viewData = []): void
    {
        ob_clean();

        $this->validateViewData($viewData);
        foreach ($viewData as $key => $value) {
            $$key = $value;
        }

        require_once(__COMPONENTS__ . "header.php");
        require_once($this->view);
        require_once(__COMPONENTS__ . "footer.php");
    }

    private function validateViewData(array $viewData): void
    {
        if(!in_array("title", array_keys($viewData))) {
            die("View data must contain a 'title' key");
        }
    }

    private string $view;
    protected string $requestMethod;
}
