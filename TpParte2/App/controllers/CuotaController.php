<?php
require_once __DIR__ . '/../models/CuotaModel.php';
require_once __DIR__ . '/../models/PlanModel.php';
require_once __DIR__ . '/../models/ClienteModel.php';
require_once __DIR__ . '/../views/CuotaView.php';
require_once __DIR__ . '/../views/ErrorView.php';
require_once __DIR__ . '/../Middlewares/AuthMiddleware.php';

class CuotaController
{
    private $model;
    private $view;
    private $errorView;
    private $req;

    public function __construct($req)
    {
        $this->model = new CuotaModel();
        $this->view = new CuotaView();
        $this->errorView = new ErrorView();
        $this->req = $req;
    }

    /** (A) Listado de ítems */
    public function showAllCuotas()
    {
        $cuotas = $this->model->getAllCuotas();
        $this->view->showCuotas($cuotas, $this->req->isLogged);
    }

    /** (A) Detalle de ítem */
    public function showCuotaById($id)
    {
        $cuota = $this->model->getCuotaById($id);
        if (!$cuota) {
            return $this->errorView->renderError("No existe la cuota solicitada.");
        }
        $isLogged = $this->req->isLogged;
        require TEMPLATES_PATH . 'detalle_cuota.phtml';
    }

    /** Listado de ítems de un cliente puntual (funcionalidad adicional del dominio) */
    public function showCuotasByCliente($idCliente)
    {
        $cuotas = $this->model->getCuotasByCliente($idCliente);
        $this->view->showCuotas($cuotas, $this->req->isLogged);
    }

    public function showFormularioAlta()
    {
        AuthMiddleware::verify($this->req);

        $planModel = new PlanModel();
        $clienteModel = new ClienteModel();

        $planes = $planModel->getAllPlanes();
        $clientes = $clienteModel->getAllClientes();

        $this->view->showAddForm($planes, $clientes);
    }

    public function add()
    {
        AuthMiddleware::verify($this->req);

        if (empty($_POST['id_plan']) || empty($_POST['id_cliente']) ||
            empty($_POST['nro_cuota']) || $_POST['monto'] === '') {
            return $this->errorView->renderError("Por favor, complete todos los campos obligatorios.");
        }

        $idPlan = $_POST['id_plan'];
        $idCliente = $_POST['id_cliente'];
        $nroCuota = $_POST['nro_cuota'];
        $monto = $_POST['monto'];
        $estadoPago = isset($_POST['estado_pago']) ? 1 : 0;
        $imagenUrl = !empty($_POST['imagen_url']) ? trim($_POST['imagen_url']) : null;

        $this->model->insertCuota($idPlan, $idCliente, $nroCuota, $monto, $estadoPago, $imagenUrl);

        header("Location: " . BASE_URL . "items");
    }

    public function showFormularioEditar($id)
    {
        AuthMiddleware::verify($this->req);

        $cuota = $this->model->getCuotaById($id);
        if (!$cuota) {
            return $this->errorView->renderError("La cuota que intentás editar no existe.");
        }

        $planModel = new PlanModel();
        $clienteModel = new ClienteModel();

        $planes = $planModel->getAllPlanes();
        $clientes = $clienteModel->getAllClientes();

        $this->view->showEditForm($cuota, $planes, $clientes);
    }

    public function update($id)
    {
        AuthMiddleware::verify($this->req);

        if (empty($_POST['id_plan']) || empty($_POST['id_cliente']) ||
            empty($_POST['nro_cuota']) || $_POST['monto'] === '') {
            return $this->errorView->renderError("Por favor, complete todos los campos obligatorios.");
        }

        $idPlan = $_POST['id_plan'];
        $idCliente = $_POST['id_cliente'];
        $nroCuota = $_POST['nro_cuota'];
        $monto = $_POST['monto'];
        $estadoPago = isset($_POST['estado_pago']) ? 1 : 0;
        $imagenUrl = !empty($_POST['imagen_url']) ? trim($_POST['imagen_url']) : null;

        $this->model->updateCuota($id, $idPlan, $idCliente, $nroCuota, $monto, $estadoPago, $imagenUrl);

        header("Location: " . BASE_URL . "items");
    }

    public function delete($id)
    {
        AuthMiddleware::verify($this->req);

        $cuota = $this->model->getCuotaById($id);
        if (!$cuota) {
            return $this->errorView->renderError("No existe la cuota con id=$id");
        }

        $this->model->deleteCuota($id);
        header("Location: " . BASE_URL . "items");
    }
}
