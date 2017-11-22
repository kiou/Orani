<?php

namespace PageBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use PageBundle\Form\PageType;
use PageBundle\Entity\Page;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PageController extends Controller
{
    /**
     * Ajouter
     */
    public function ajouterAdminAction(Request $request)
    {
        $page = new Page;
        $form = $this->get('form.factory')->create(PageType::class, $page);

        /* Récéption du formulaire */
        if ($form->handleRequest($request)->isValid()){
            $page->getReferencement()->uploadOgimage();

            $em = $this->getDoctrine()->getManager();
            $em->persist($page);
            $em->flush();

            $request->getSession()->getFlashBag()->add('succes', 'Page enregistrée avec succès');
            return $this->redirect($this->generateUrl('admin_page_manager'));
        }

        return $this->render('PageBundle:Admin/Page:ajouter.html.twig',
            array(
                'form' => $form->createView()
            )
        );
    }

    /**
     * Gestion
     */
    public function managerAdminAction(Request $request)
    {
        /* Services */
        $rechercheService = $this->get('recherche.service');
        $recherches = $rechercheService->setRecherche('page_manager', array(
                'recherche'
            )
        );

        /* La liste des pages */
        $pages = $this->getDoctrine()
                      ->getRepository('PageBundle:Page')
                      ->getAllPages($recherches['recherche']);

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $pages, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            50/*limit per page*/
        );

        return $this->render( 'PageBundle:Admin/Page:manager.html.twig', array(
                'pagination' => $pagination,
                'recherches' => $recherches
            )
        );

    }

    /**
     * Publication
     */
    public function publierAdminAction(Request $request, Page $page){

        if($request->isXmlHttpRequest()){
            $state = $page->reverseState();
            $page->setIsActive($state);

            $em = $this->getDoctrine()->getManager();
            $em->persist($page);
            $em->flush();

            return new JsonResponse(array('state' => $state));
        }

    }

    /**
     * Supprimer
     */
    public function supprimerAdminAction(Request $request, Page $page)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($page);
        $em->flush();

        $request->getSession()->getFlashBag()->add('succes', 'Page supprimée avec succès');
        return $this->redirect($this->generateUrl('admin_page_manager'));
    }

    /**
     * Modifier
     */
    public function modifierAdminAction(Request $request, Page $page)
    {
        $form = $this->get('form.factory')->create(PageType::class, $page);

        /* Récéption du formulaire */
        if ($form->handleRequest($request)->isValid()){
            $page->getReferencement()->uploadOgimage();

            $em = $this->getDoctrine()->getManager();
            $em->persist($page);
            $em->flush();

            $request->getSession()->getFlashBag()->add('succes', 'Page enregistrée avec succès');
            return $this->redirect($this->generateUrl('admin_page_manager'));
        }

        /* BreadCrumb */
        $breadcrumb = array(
            'Accueil' => $this->generateUrl('admin_page_index'),
            'Gestion des pages' => $this->generateUrl('admin_page_manager'),
            'Modifier une page' => ''
        );

        return $this->render('PageBundle:Admin/Page:modifier.html.twig',
            array(
                'breadcrumb' => $breadcrumb,
                'page' => $page,
                'form' => $form->createView()
            )
        );

    }

    /**
     * Poid
     */
    public function poidAdminAction(Request $request, Page $page, $poid){

        if($request->isXmlHttpRequest()){
            $page->setPoid($poid);

            $em = $this->getDoctrine()->getManager();
            $em->persist($page);
            $em->flush();

            return new JsonResponse(array('status' => 'succes'));
        }

    }

    /*
     * View
     */
    public function viewClientAction(Page $page)
    {
        if(!$page->getIsActive()) throw new NotFoundHttpException('Cette page n\'est pas disponible');

        return $this->render( 'PageBundle:Client/Page:view.html.twig',array(
                'page' => $page
            )
        );
    }
}
