<?php

namespace App\Services;

class AssetsService
{
    protected $css = [];
    protected $js = [];

    public function __construct()
    {
        $this->css = [
            '/css/root.css',
            '/css/header.css',
            '/css/footer.css'
        ];

        $this->js = [
            '/js/header.js',
        ];
    }

    public function add_css($css)
    {
        $this->css[] = $css;
    }

    public function add_js($js)
    {
        $this->js[] = $js;
    }

    public function get_css()
    {
        return $this->css;
    }

    public function get_js()
    {
        return $this->js;
    }

    public function load($view)
    {
        // Get the view name from the path
        $viewName = explode('/', strtolower($view));
        $viewName = end($viewName);

        // Search for the specific CSS file
        $cssPath = "/css/{$viewName}.css";
        if (file_exists(public_path() . $cssPath)) {
            $this->add_css($cssPath);
        }

        // Search for the specific JS file
        $jsPath = "/js/{$viewName}.js";
        if (file_exists( public_path() . $jsPath)) {
            $this->add_js($jsPath);
        }

        return [$this->css, $this->js];
    }
}