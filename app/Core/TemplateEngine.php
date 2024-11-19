<?php

namespace App\Core;

class TemplateEngine
{
    protected $sections = [];
    protected $currentSection = null;
    protected $layout;

    public function startSection($name)
    {
        $this->currentSection = $name;
        ob_start(); // Inicializa o buffer de saída
    }

    public function endSection()
    {
        if ($this->currentSection !== null) {
            $this->sections[$this->currentSection] = ob_get_clean();
            $this->currentSection = null;
        } else {
            throw new \Exception("Section end called without a corresponding start.");
        }
    }

    public function yieldSection($name)
    {
        return $this->sections[$name] ?? ''; // Retorna o conteúdo da seção ou uma string vazia
    }

    public function extend($layout)
    {
        $this->layout = $layout;
    }

    public function render($view, $data = [])
    {
        extract($data);

        $viewPath = __DIR__ . "/../../resources/views/{$view}.php";
        if (file_exists($viewPath)) {
            require $viewPath; // Carrega a view principal
            if ($this->layout) {
                $layoutPath = __DIR__ . "/../../resources/views/{$this->layout}.php";
                require $layoutPath; // Carrega o layout
            }
        } else {
            throw new \Exception("View not found: {$viewPath}");
        }
    }
}
