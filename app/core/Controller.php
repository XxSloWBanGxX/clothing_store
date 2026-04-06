<?php

class Controller
{
    public function view($view, $data = [])
    {
        $viewPath = __DIR__ . '/../views/' . $view . '.php';

        if (file_exists($viewPath)) {
            require_once $viewPath;
        } else {
            die('View not found: ' . $view);
        }
    }

    public function redirect($url)
    {
        header('Location: ' . $url);
        exit;
    }
}