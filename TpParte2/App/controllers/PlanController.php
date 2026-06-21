<?php
require_once __DIR__ . '/../models/PlanModel.php';
require_once __DIR__ . '/../models/CuotaModel.php';
require_once __DIR__ . '/../views/PlanView.php';
require_once __DIR__ . '/../views/ErrorView.php';
require_once __DIR__ . '/../Middlewares/AuthMiddleware.php';


class PlanController
{
    private $model;
    private $view;
    private $errorView;
    private $req;

    public function __construct($req)
    {
        $this->model = new PlanModel();
        $this->view = new PlanView();
        $this->errorView = new ErrorView();
        $this->req = $req;
    }

    /** (B) Listado público de categorías */
    public function showAllPlanes()
    {
        $planes = $this->model->getAllPlanes();
        $this->view->showPlanes($planes, $this->req->isLogged);
    }

    /** (B) Listado de ítems (cuotas) que pertenecen a una categoría (plan) seleccionada */
    public function showPlanById($id)
    {
        $plan = $this->model->getPlanById($id);
        if (!$plan) {
            return $this->errorView->renderError("No existe la categoría solicitada.");
        }

        $cuotaModel = new CuotaModel();
        $cuotas = $cuotaModel->getCuotasByPlan($id);

        $this->view->showPlanDetalle($plan, $cuotas, $this->req->isLogged);
    }

    public function showFormularioAlta()
    {
        AuthMiddleware::verify($this->req);
        $this->view->showAddForm();
    }

    public function add()
    {
        AuthMiddleware::verify($this->req);

        if (empty($_POST['nombre']) || empty($_POST['descripcion']) || empty($_POST['monto_total'])) {
            return $this->errorView->renderError("Por favor, complete todos los campos obligatorios.");
        }

        $nombre = trim($_POST['nombre']);
        $descripcion = trim($_POST['descripcion']);
        $montoTotal = $_POST['monto_total'];
        $imagenUrl = !empty($_POST['imagen_url']) ? trim($_POST['imagen_url']) : null;

        $this->model->insertPlan($nombre, $descripcion, $montoTotal, $imagenUrl);

        header("Location: " . BASE_URL . "categorias");
    }

    public function showFormularioEditar($id)
    {
        AuthMiddleware::verify($this->req);

        $plan = $this->model->getPlanById($id);
        if (!$plan) {
            return $this->errorView->renderError("La categoría que intentás editar no existe.");
        }

        $this->view->showEditForm($plan);
    }

    public function update($id)
    {
        AuthMiddleware::verify($this->req);

        if (empty($_POST['nombre']) || empty($_POST['descripcion']) || empty($_POST['monto_total'])) {
            return $this->errorView->renderError("Por favor, complete todos los campos obligatorios.");
        }

        $nombre = trim($_POST['nombre']);
        $descripcion = trim($_POST['descripcion']);
        $montoTotal = $_POST['monto_total'];
        $imagenUrl = !empty($_POST['imagen_url']) ? trim($_POST['imagen_url']) : null;

        $this->model->updatePlan($id, $nombre, $descripcion, $montoTotal, $imagenUrl);

        header("Location: " . BASE_URL . "categorias");
    }

    public function delete($id)
    {
        AuthMiddleware::verify($this->req);

        $plan = $this->model->getPlanById($id);
        if (!$plan) {
            return $this->errorView->renderError("No existe la categoría con id=$id");
        }

        $this->model->deletePlan($id);
        header("Location: " . BASE_URL . "categorias");
    }
}
