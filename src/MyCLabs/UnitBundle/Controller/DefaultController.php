<?php

namespace MyCLabs\UnitBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function homeAction()
    {
        return $this->render('UnitBundle:Default:home.html.twig');
    }
}
