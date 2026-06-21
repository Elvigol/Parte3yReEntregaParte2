<?php


class AuthView
{
    public function showLogin($error = null)
    {
        $isLogged = false;
        require TEMPLATES_PATH . 'form_login.phtml';
    }
}
