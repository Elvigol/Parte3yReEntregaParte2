<?php


class ClienteView
{
    public function showClientes($clientes, $isLogged)
    {
        require TEMPLATES_PATH . 'lista_clientes.phtml';
    }

    public function showAddForm()
    {
        $isLogged = true; // Esta vista siempre está protegida por AuthMiddleware::verify()
        require TEMPLATES_PATH . 'form_alta_cliente.phtml';
    }

    public function showEditForm($cliente)
    {
        $isLogged = true; // Esta vista siempre está protegida por AuthMiddleware::verify()
        require TEMPLATES_PATH . 'form_editar_cliente.phtml';
    }
}
