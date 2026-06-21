<?php
require_once __DIR__ . '/../models/ClienteModel.php';
require_once __DIR__ . '/../views/ClienteView.php';
require_once __DIR__ . '/../views/ErrorView.php';
require_once __DIR__ . '/../Middlewares/AuthMiddleware.php';


class ClienteController
{
    private $model;
    private $view;
    private $errorView;
    private $req;

    public function __construct($req)
    {
        $this->model = new ClienteModel();
        $this->view = new ClienteView();
        $this->errorView = new ErrorView();
        $this->req = $req;
    }

    public function showAllClientes()
    {
        $clientes = $this->model->getAllClientes();
        $this->view->showClientes($clientes, $this->req->isLogged);
    }

    public function showFormularioAlta()
    {
        AuthMiddleware::verify($this->req);
        $this->view->showAddForm();
    }

    public function add()
    {
        AuthMiddleware::verify($this->req);

        if (empty($_POST['nombre']) || empty($_POST['email'])) {
            return $this->errorView->renderError("Por favor, complete el nombre y el email.");
        }

        $nombre = trim($_POST['nombre']);
        $email = trim($_POST['email']);
        $imagenUrl = !empty($_POST['imagen_url']) ? trim($_POST['imagen_url']) : null;

        $id = $this->model->insertCliente($nombre, $email, $imagenUrl);

        if (empty($id)) {
            return $this->errorView->renderError("Error al agregar el cliente.");
        }

        header("Location: " . BASE_URL . "clientes");
    }

    public function showFormularioEditar($id)
    {
        AuthMiddleware::verify($this->req);

        $cliente = $this->model->getClienteById($id);
        if (!$cliente) {
            return $this->errorView->renderError("El cliente que intentás editar no existe.");
        }

        $this->view->showEditForm($cliente);
    }

    public function update($id)
    {
        AuthMiddleware::verify($this->req);

        if (empty($_POST['nombre']) || empty($_POST['email'])) {
            return $this->errorView->renderError("Por favor, complete el nombre y el email.");
        }

        $nombre = trim($_POST['nombre']);
        $email = trim($_POST['email']);
        $imagenUrl = !empty($_POST['imagen_url']) ? trim($_POST['imagen_url']) : null;

        $this->model->updateCliente($id, $nombre, $email, $imagenUrl);

        header("Location: " . BASE_URL . "clientes");
    }

    public function delete($id)
    {
        AuthMiddleware::verify($this->req);

        $cliente = $this->model->getClienteById($id);
        if (!$cliente) {
            return $this->errorView->renderError("No existe el cliente con id=$id");
        }

        $this->model->deleteCliente($id);
        header("Location: " . BASE_URL . "clientes");
    }
}
