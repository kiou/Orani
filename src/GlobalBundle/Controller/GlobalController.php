<?php

namespace GlobalBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class GlobalController extends Controller
{
    /**
     * L'index du site
     */
    public function ClientIndexAction()
    {
        return $this->render('GlobalBundle:Client/Page:index.html.twig');
    }

    /**
     * L'index de l'administration
     */
    public function AdminIndexAction()
    {
        return $this->render('GlobalBundle:Admin/Page:index.html.twig');
    }

    /**
     * Selecteur de langue
     */
    public function SelecteurLangueAction()
    {
        $langues = $this->getDoctrine()->getRepository('GlobalBundle:Langue')->findAll();

        return $this->render('GlobalBundle:Langue:selecteur.html.twig',array(
                'langues' => $langues
            )
        );
    }

}
