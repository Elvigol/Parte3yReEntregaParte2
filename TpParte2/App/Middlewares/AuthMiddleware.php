<?php


class AuthMiddleware
{
    public static function verify($req)
    {
        if (empty($req->isLogged)) {
            header('Location: ' . BASE_URL . 'login');
            exit();
        }
    }
}
