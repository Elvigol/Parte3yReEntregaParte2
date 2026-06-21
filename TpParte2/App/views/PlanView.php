<?php

class PlanView
{
    public function showPlanes($planes, $isLogged)
    {
        require TEMPLATES_PATH . 'lista_planes.phtml';
    }

    public function showPlanDetalle($plan, $cuotas, $isLogged)
    {
        require TEMPLATES_PATH . 'detalle_plan.phtml';
    }

    public function showAddForm()
    {
        $isLogged = true; // Esta vista siempre está protegida por AuthMiddleware::verify()
        require TEMPLATES_PATH . 'form_alta_plan.phtml';
    }

    public function showEditForm($plan)
    {
        $isLogged = true; // Esta vista siempre está protegida por AuthMiddleware::verify()
        require TEMPLATES_PATH . 'form_editar_plan.phtml';
    }
}
