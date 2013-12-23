<?php

namespace MyCLabs\UnitBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    public function localeRedirectionAction(Request $request)
    {
        $lang = locale_get_primary_language($request->getPreferredLanguage());

        if (! in_array($lang, $this->container->getParameter('available_locales'))) {
            $lang = $this->container->getParameter('default_locale');
        }

        return $this->redirect($this->generateUrl('homepage', ['_locale' => $lang]));
    }

    public function homeAction()
    {
        return $this->render('UnitBundle:Default:home.html.twig');
    }
}
