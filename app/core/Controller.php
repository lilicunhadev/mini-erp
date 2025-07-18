<?php
namespace App\Core;

class Controller
{
    protected function view(string $view, array $data = []): void
    {
        $viewFile = BASE_PATH . "/app/views/{$view}.php";
        if (file_exists($viewFile)) {
            extract($data);
            require $viewFile;
        } else {
            echo "View {$view} não encontrada.";
        }
    }
}
