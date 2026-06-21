<?php


class CuotaView
{
    public function showCuotas($cuotas, $isLogged)
    {
        require TEMPLATES_PATH . 'lista_cuotas.phtml';
    }

    public function showAddForm($planes, $clientes)
    {
        $isLogged = true; // Esta vista siempre está protegida por AuthMiddleware::verify()
        require TEMPLATES_PATH . 'form_alta_cuota.phtml';
    }

    public function showEditForm($cuota, $planes, $clientes)
    {
        $isLogged = true; // Esta vista siempre está protegida por AuthMiddleware::verify()
        require TEMPLATES_PATH . 'form_editar_cuota.phtml';
    }
}
