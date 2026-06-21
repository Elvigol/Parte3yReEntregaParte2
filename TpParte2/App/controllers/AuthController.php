<?php
require_once __DIR__ . '/../models/AuthModel.php';
require_once __DIR__ . '/../views/AuthView.php';


class AuthController
{
    private $model;
    private $view;
    private $req;

    public function __construct($req)
    {
        $this->model = new AuthModel();
        $this->view = new AuthView();
        $this->req = $req;
    }

    public function showLogin()
    {
        // Si ya está logueado, no tiene sentido mostrarle el login otra vez.
        if ($this->req->isLogged) {
            header("Location: " . BASE_URL . "categorias");
            return;
        }
        $this->view->showLogin();
    }

    public function auth()
    {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';

        if (empty($username) || empty($password)) {
            $this->view->showLogin("Faltan completar datos.");
            return;
        }

        $user = $this->model->getUsuarioByUsername($username);

        if ($user && password_verify($password, $user->password)) {
            $_SESSION['USER_ID'] = $user->id_usuario;
            $_SESSION['USER_NAME'] = $user->username;
            $_SESSION['IS_LOGGED'] = true;

            header("Location: " . BASE_URL . "categorias");
        } else {
            $this->view->showLogin("Usuario o contraseña incorrectos.");
        }
    }

    public function logout()
    {
        session_unset();
        session_destroy();
        header("Location: " . BASE_URL . "home");
    }
}
