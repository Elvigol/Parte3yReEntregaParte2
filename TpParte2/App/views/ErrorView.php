<?php


class ErrorView
{
    public function renderError($mensaje)
    {
        require TEMPLATES_PATH . 'error.phtml';
        die();
    }
}
