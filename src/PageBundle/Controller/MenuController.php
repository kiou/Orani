<?php

namespace PageBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use PageBundle\Form\MenuType;
use PageBundle\Entity\Menu;

class MenuController extends Controller
{

    private $return = [];

    /**
     * Ajouter
     */
    public function ajouterAdminAction(Request $request)
    {
        $menu = new Menu;
        $form = $this->get('form.factory')->create(MenuType::class, $menu);

        /* Récéption du formulaire */
        if ($form->handleRequest($request)->isValid()){
            $em = $this->getDoctrine()->getManager();
            $em->persist($menu);
            $em->flush();

            $request->getSession()->getFlashBag()->add('succes', 'Onglet menu enregistré avec succès');
            return $this->redirect($this->generateUrl('admin_menu_manager'));
        }

        return $this->render('PageBundle:Admin/Menu:ajouter.html.twig',
            array(
                'form' => $form->createView()
            )
        );
    }

    /**
     * Publication
     */
    public function publierAdminAction(Request $request, Menu $menu){

        if($request->isXmlHttpRequest()){
            $state = $menu->reverseState();
            $menu->setIsActive($state);

            $em = $this->getDoctrine()->getManager();
            $em->persist($menu);
            $em->flush();

            return new JsonResponse(array('state' => $state));
        }

    }

    /**
     * Supprimer
     */
    public function supprimerAdminAction(Request $request, Menu $menu)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($menu);
        $em->flush();

        $request->getSession()->getFlashBag()->add('succes', 'Onglet menu supprimé avec succès');
        return $this->redirect($this->generateUrl('admin_menu_manager'));
    }

    /**
     * Modifier
     */
    public function modifierAdminAction(Request $request, Menu $menu)
    {
        $form = $this->get('form.factory')->create(MenuType::class, $menu);

        /* Récéption du formulaire */
        if ($form->handleRequest($request)->isValid()){
            $em = $this->getDoctrine()->getManager();
            $em->persist($menu);
            $em->flush();

            $request->getSession()->getFlashBag()->add('succes', 'Onglet menu enregistré avec succès');
            return $this->redirect($this->generateUrl('admin_menu_manager'));
        }

        /* BreadCrumb */
        $breadcrumb = array(
            'Accueil' => $this->generateUrl('admin_page_index'),
            'Gestion du menu' => $this->generateUrl('admin_menu_manager'),
            'Modifier un onglet' => ''
        );

        return $this->render('PageBundle:Admin/Menu:modifier.html.twig',
            array(
                'breadcrumb' => $breadcrumb,
                'menu' => $menu,
                'form' => $form->createView()
            )
        );

    }

    /**
     * Gestion
     */
    public function managerAdminAction()
    {
        return $this->render('PageBundle:Admin/Menu:manager.html.twig', array(
                'menus' => $this->getRecursiveMenu(3, null, true)
            )
        );
    }

    /**
     * Gestion client
     */
    public function managerClientAction()
    {
        return $this->render('PageBundle:Client/Menu:manager.html.twig', array(
                'menus' => $this->getRecursiveMenu(3, null, false)
            )
        );
    }

    /**
     * Gestion update
     */
    public function managerUpdateAdminAction(Request $request)
    {
        if($request->isXmlHttpRequest()){

            $count = 1;
            foreach ($request->request->get('data') as $data){
                $menu = $this->getDoctrine()
                             ->getRepository('PageBundle:Menu')
                             ->find($data['id']);

                $menu->setParent(empty($data['parent_id']) ? 0 : $data['parent_id']);
                $menu->setPoid($count);

                $em = $this->getDoctrine()->getManager();
                $em->persist($menu);
                $em->flush();

                $count ++;
            }

            return new JsonResponse(array('state' => 'OK'));
        }
    }

    /**
     * Créer le menu sous forme de tableau récursif
     */
    public function getRecursiveMenu($recursive ,$parent, $admin){

        $recursive --;

        if($recursive >= 0){

            $menus = $this->getDoctrine()
                          ->getRepository('PageBundle:Menu')
                          ->getAllMenuAdmin($parent, $admin);

            foreach ($menus as $menu) {

                $datas = array(
                    'id' => $menu->getId(),
                    'titre' => $menu->getTitre(),
                    'lien' => $menu->getLien(),
                    'parent' => $menu->getParent(),
                    'isActive' => $menu->getIsActive(),
                    'destination' => $menu->getDestination(),
                );

                if(empty($menu->getParent())){
                    $this->return[$menu->getId()] = $datas;
                }else{
                    $found_key = $this->get('tool.service')->recursive_array_search($menu->getParent(), $this->return);

                    if($found_key == $menu->getParent()){
                        if(!isset($this->return[$found_key]['enfants'])) $this->return[$found_key]['enfants'] = [];
                        $this->return[$found_key]['enfants'][$menu->getId()] = $datas;
                    }else{
                        if(!isset($this->return[$found_key]['enfants'][$menu->getParent()]['enfants'])) $this->return[$found_key]['enfants'][$menu->getParent()]['enfants'] = [];
                        $this->return[$found_key]['enfants'][$menu->getParent()]['enfants'][$menu->getId()] = $datas;
                    }
                }

                $this->getRecursiveMenu($recursive, $menu->getId(), $admin);

            }

        }

        return $this->return;

    }

}
