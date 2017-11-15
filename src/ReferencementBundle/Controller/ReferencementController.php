<?php

namespace ReferencementBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use ReferencementBundle\Entity\Referencement;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class ReferencementController extends Controller
{

    /**
     * Supprimer l'ogimage
     */
    public function supprimerOgimageAdminAction(Request $request, Referencement $referencement)
    {
        if($request->isXmlHttpRequest()){
            $em = $this->getDoctrine()->getManager();
            $referencement->setOgimage(null);
            $em->flush();

            return new JsonResponse(array('state' => 'ok'));
        }
    }

}
