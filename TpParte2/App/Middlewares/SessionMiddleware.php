<?php


class SessionMiddleware
{
    public static function loadUser($req)
    {
        $req->isLogged = isset($_SESSION['IS_LOGGED']) && $_SESSION['IS_LOGGED'] === true;
        $req->username = $_SESSION['USER_NAME'] ?? null;
    }
}
