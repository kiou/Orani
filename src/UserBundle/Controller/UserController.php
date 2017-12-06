<?php

namespace UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use UserBundle\Entity\User;
use UserBundle\Entity\Reinitialisation;
use UserBundle\Entity\Historique;
use UserBundle\Entity\Newsletter;
use UserBundle\Form\ReinitialisationType;
use UserBundle\Form\UserType;
use UserBundle\Form\CompteType;
use UserBundle\Form\RegisterType;
use UserBundle\Form\UserPasswordType;
use UserBundle\Form\NewsletterType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UserController extends Controller
{

    /**
     * Connexion
     */
    public function LoginAction(Request $request)
    {

        $authenticationUtils = $this->get('security.authentication_utils');
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();


        /* Historique utilisateur */
        if(!empty($error)){

            $em = $this->getDoctrine()->getManager();

            $historique = new Historique;
            $historique->setContenu('Erreur de connexion avec l\'email suivant: '.$lastUsername);
            $historique->setIp($request->getClientIp());
            $em->persist($historique);
            $em->flush();


        }

        return $this->render(
            'UserBundle:Client:login.html.twig',
            array(
                // last username entered by the user
                'last_username' => $lastUsername,
                'error'         => $error,
            )
        );

    }

    /**
     * Inscription
     */
    public function RegisterAction(Request $request)
    {
        $user = new User;
        $form = $this->get('form.factory')->create(RegisterType::class, $user);

        /* Récéption du formulaire */
        if ($form->handleRequest($request)->isValid()){
            $password = $this->container->get('security.password_encoder')
                                        ->encodePassword($user, $form->get('password')->getData());
            $user->setPassword($password);

            $user->uploadAvatar();

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $request->getSession()->getFlashBag()->add('succes', 'Vous êtes maintenant inscrit, votre compte est en attente de validation');
            return $this->redirect($this->generateUrl('login'));
        }

        /* BreadCrumb */
        $breadcrumb = array(
            'Connexion' => $this->generateUrl('login'),
            'Inscription' => ''
        );

        return $this->render( 'UserBundle:Client:register.html.twig', array(
                'form' => $form->createView(),
                'breadcrumb' => $breadcrumb
            )
        );
    }

    /**
     * Réinitialisation
     */
    public function ReinitialisationAction(Request $request)
    {
        $base_url = $request->getScheme() . '://' . $request->getHttpHost();

        $reinitialisation = new Reinitialisation;
        $form = $this->get('form.factory')->create(ReinitialisationType::class, $reinitialisation);

        /* Récéption du formulaire */
        if ($form->handleRequest($request)->isValid()){
            $token = md5(uniqid(rand(), true));
            $reinitialisation->setToken($token);

            $em = $this->getDoctrine()->getManager();
            $em->persist($reinitialisation);
            $em->flush();

            /* Notification*/
            $message = \Swift_Message::newInstance()
                ->setSubject('Réinitialisation de mot de passe')
                ->setFrom('contact@colocarts.com')
                ->setTo($form->getData()->getEmail())
                ->setBody(
                    $this->renderView('GlobalBundle:Mail:simple.html.twig', array(
                            'titre' => 'Réinitialisation de mot de passe',
                            'contenu' => 'Votre demande de réinitialisation à bien été prise en compte. Pour compléter le processus veuillez cliquer sur le lien suivant :<br><a href="'.$base_url.$this->generateUrl('reinitialisation_password',['token' => $token]).'">Réinitialiser mon mot de passe</a>'
                        )
                    ),
                    'text/html'
                );

            /* Envoyer le message */
            $this->get('mailer')->send($message);

            $request->getSession()->getFlashBag()->add('succes', 'Demande de réinitialisation effectuée avec succès. Veuillez consulter vos emails pour compléter le processus');
            return $this->redirect($this->generateUrl('login'));
        }

        /* BreadCrumb */
        $breadcrumb = array(
            'Connexion' => $this->generateUrl('login'),
            'Demande de réinitialisation' => ''
        );

        return $this->render('UserBundle:CLient:reinitialisation.html.twig', array(
                'form' => $form->createView(),
                'breadcrumb' => $breadcrumb
            )
        );
    }

    /**
     * Réinitialisation password
     */
    public function ReinitialisationPasswordAction(Request $request, $token)
    {

        $reinitialisation = $this->getDoctrine()
                                 ->getRepository('UserBundle:Reinitialisation')
                                 ->findOneBy(['token' => $token, 'isActive' => true],['id' => 'DESC']);

        if(empty($reinitialisation)) throw new NotFoundHttpException('Cette page n\'est pas disponible');

        $user = $this->getDoctrine()
                     ->getRepository('UserBundle:User')
                     ->findOneBy(['email' => $reinitialisation->getEmail()],[]);

        $form = $this->get('form.factory')->create(UserPasswordType::class, $user);

        /* Récéption du formulaire */
        if ($form->handleRequest($request)->isValid()){
            $em = $this->getDoctrine()->getManager();

            /* Modifier le password */
            $password = $this->container->get('security.password_encoder')
                ->encodePassword($user, $form->get('password')->getData());
            $user->setPassword($password);

            $em->persist($user);
            $em->flush();

            /* Modifier la/les reinitialisation(s) */
            $reinitialisations = $this->getDoctrine()
                                      ->getRepository('UserBundle:Reinitialisation')
                                      ->findBy(['email' => $user->getEmail(), 'isActive' => true],['id' => 'DESC']);

            foreach ($reinitialisations as $reinitialisation){
                $reinitialisation->setIsActive(false);

                $em->persist($reinitialisation);
                $em->flush();
            }

            $request->getSession()->getFlashBag()->add('succes', 'Mot de passe réinitialisé avec succès, vous pouvez maintenant vous connecter avec votre nouveau mot de passe');
            return $this->redirect($this->generateUrl('login'));
        }

        /* BreadCrumb */
        $breadcrumb = array(
            'Connexion' => $this->generateUrl('login'),
            'Réinitialisation de mot de passe' => ''
        );

        return $this->render('UserBundle:Client:reinitialisation_password.html.twig', array(
                'breadcrumb' => $breadcrumb,
                'form' => $form->createView()
            )
        );

    }

    /**
     * Modification compte
     */
    public function AdminCompteModifierAction(Request $request)
    {
        /* Création du fomulaire */
        $user = $this->getUser();
        $form = $this->get('form.factory')->create(CompteType::class, $user);

        /* Récéption du formulaire */
        if ($form->handleRequest($request)->isValid()){
            $user->uploadAvatar();

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $request->getSession()->getFlashBag()->add('succes', 'Informations modifiés avec succès');
            return $this->redirect($this->generateUrl('admin_page_index'));
        }

        /* BreadCrumb */
        $breadcrumb = array(
            'Accueil' => $this->generateUrl('admin_page_index'),
            'Mes informations' => ''
        );

        return $this->render( 'UserBundle:Admin/Compte:modifier.html.twig',
            array(
                'breadcrumb' => $breadcrumb,
                'utilisateur' => $user,
                'form' => $form->createView()
            )
        );
    }

    /**
     * Modification compte password
     */
    public function AdminComptePasswordAction(Request $request)
    {
        /* Création du fomulaire */
        $user = $this->getUser();
        $form = $this->get('form.factory')->create(UserPasswordType::class, $user);

        /* Récéption du formulaire */
        if ($form->handleRequest($request)->isValid()){
            $password = $this->container->get('security.password_encoder')
                                        ->encodePassword($user, $form->get('password')->getData());
            $user->setPassword($password);

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $request->getSession()->getFlashBag()->add('succes', 'Mot de passe modifié avec succès');
            return $this->redirect($this->generateUrl('admin_page_index'));
        }

        /* BreadCrumb */
        $breadcrumb = array(
            'Accueil' => $this->generateUrl('admin_page_index'),
            'Mes informations' => ''
        );

        return $this->render( 'UserBundle:Admin/Compte:modifierPassword.html.twig',
            array(
                'breadcrumb' => $breadcrumb,
                'form' => $form->createView()
            )
        );

    }

    /**
     * Gestion
     */
    public function AdminManagerAction(Request $request)
    {
        /* Services */
        $rechercheService = $this->get('recherche.service');
        $recherches = $rechercheService->setRecherche('user_manager', array(
                'username'
            )
        );

        /* La liste des utilisateurs */
        $utilisateurs = $this->getDoctrine()
                             ->getRepository('UserBundle:User')
                             ->getAllUser($recherches['username']);

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $utilisateurs, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            50/*limit per page*/
        );

        return $this->render( 'UserBundle:Admin:manager.html.twig', array(
                'pagination' => $pagination,
                'recherches' => $recherches
            )
        );

    }

    /**
     * Ajouter
     */
    public function AdminAjouterAction(Request $request)
    {
        $user = new User;
        $form = $this->get('form.factory')->create(UserType::class, $user);

        /* Récéption du formulaire */
        if ($form->handleRequest($request)->isValid()){
            $password = $this->container->get('security.password_encoder')
                                        ->encodePassword($user, $form->get('password')->getData());
            $user->setPassword($password);

            $user->uploadAvatar();

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $request->getSession()->getFlashBag()->add('succes', 'Utilisateur enregistré avec succès');
            return $this->redirect($this->generateUrl('admin_user_manager'));
        }

        return $this->render( 'UserBundle:Admin:ajouter.html.twig',
            array(
                'form' => $form->createView()
            )
        );

    }

    /**
     * Publication
     */
    public function AdminPublierAction(Request $request, User $user){

        if($request->isXmlHttpRequest()){
            $state = $user->reverseState();
            $user->setIsActive($state);

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            return new JsonResponse(array('state' => $state));
        }

    }

    /**
     * Supprimer l'avatar
     */
    public function AdminSupprimerAvatarAction(Request $request, User $user)
    {
        if($request->isXmlHttpRequest()){
            $em = $this->getDoctrine()->getManager();
            $user->setAvatar(null);
            $em->flush();

            return new JsonResponse(array('state' => 'ok'));
        }
    }

    /**
     * Modifier
     */
    public function AdminModifierAction(Request $request, User $user)
    {
        $form = $this->get('form.factory')->create(CompteType::class, $user);

        /* Récéption du formulaire */
        if ($form->handleRequest($request)->isValid()){
            $user->uploadAvatar();

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $request->getSession()->getFlashBag()->add('succes', 'Utilisateur enregistré avec succès');
            return $this->redirect($this->generateUrl('admin_user_manager'));
        }

        /* BreadCrumb */
        $breadcrumb = array(
            'Accueil' => $this->generateUrl('admin_page_index'),
            'Gestion des utilisateurs' => $this->generateUrl('admin_user_manager'),
            'Modifier un utilisateur' => ''
        );

        return $this->render( 'UserBundle:Admin:modifier.html.twig',
            array(
                'breadcrumb' => $breadcrumb,
                'form' => $form->createView(),
                'utilisateur' => $user
            )
        );

    }

    /**
     * Modifier le mot de passe
     */
    public function AdminModifierPasswordAction(Request $request, User $user)
    {
        $form = $this->get('form.factory')->create(UserPasswordType::class, $user);

        /* Récéption du formulaire */
        if ($form->handleRequest($request)->isValid()){
            $password = $this->container->get('security.password_encoder')
                                        ->encodePassword($user, $form->get('password')->getData());
            $user->setPassword($password);

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $request->getSession()->getFlashBag()->add('succes', 'Utilisateur enregistré avec succès');
            return $this->redirect($this->generateUrl('admin_user_manager'));
        }

        /* BreadCrumb */
        $breadcrumb = array(
            'Accueil' => $this->generateUrl('admin_page_index'),
            'Gestion es utilisateurs' => $this->generateUrl('admin_user_manager'),
            'Modifier un mot de passe' => ''
        );

        return $this->render( 'UserBundle:Admin:modifierPassword.html.twig',
            array(
                'breadcrumb' => $breadcrumb,
                'form' => $form->createView(),
                'utilisateur' => $user
            )
        );

    }

    /**
     * Gestion historique
     */
    public function AdminHistoriqueAction()
    {
        $historiques = $this->getDoctrine()
                            ->getRepository('UserBundle:Historique')
                            ->findBy(array(),array('id' => 'DESC'));

        return $this->render( 'UserBundle:Admin:historique.html.twig', array(
                'historiques' => $historiques
            )
        );
    }

    /**
     * Compte
     */
    public function ClientCompteAction()
    {
        return $this->render('UserBundle:Client/Compte:dashboard.html.twig');
    }

    /**
     * Bloc newsletter
     */
    public function ClientNewsletterBlocAction()
    {
        /* Création du fomulaire*/
        $newsletter = new Newsletter;
        $form = $this->get('form.factory')->create(NewsletterType::class, $newsletter);

        return $this->render('UserBundle:Client/Newsletter:bloc.html.twig',array(
                'form' => $form->createView()
            )
        );
    }

    /**
     * Inscription newsletter
     */
    public function ClientNewsletterAjouterAction(Request $request)
    {
        if($request->isXmlHttpRequest()) {

            /* Création du fomulaire */
            $newsletter = new Newsletter;
            $form = $this->get('form.factory')->create(NewsletterType::class, $newsletter);

            /* Récéption du formulaire */
            if ($form->handleRequest($request)->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($newsletter);
                $em->flush();

                return new JsonResponse(array(
                        'succes' => 'Votre message à été envoyé avec succès'
                    )
                );
            }

            return new JsonResponse(array(
                    'error' => $this->getErrorsAsArray($form)
                )
            );

        }
    }

    /**
     * Export
     */
    public function AdminNewsletterExportAction()
    {
        $file = __DIR__.'/../../../web/file/export/newsletter.csv';

        $fp = fopen($file,'w');

        fputcsv($fp,array('Date','Email'),';');

        $newsletters = $this->getDoctrine()
                            ->getRepository('UserBundle:Newsletter')
                            ->findBy([],['id' => 'DESC']);

        foreach ($newsletters as $newsletter){

            $row = array(
                'Date' => $newsletter->getCreated()->format('d-m-Y H:i:s'),
                'Email' => $newsletter->getEmail(),
            );

            $row = array_map("utf8_decode", $row);
            fputcsv($fp, $row,';');

        }

        fclose($fp);

        $response = new Response();
        $response->headers->set('Content-type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="newsletter.csv";');
        $response->sendHeaders();
        $response->setContent(file_get_contents($file));
        return $response;

    }

    /**
     * Retourne un tableau d'erreur pour le formulaired
     * @param Object le formulaire
     */
    protected function getErrorsAsArray($form)
    {
        $errors = array();
        foreach ($form->getErrors() as $error)
            $errors[] = $error->getMessage();

        foreach ($form->all() as $key => $child) {
            if ($err = $this->getErrorsAsArray($child))
                $errors[$key] = $err;
        }
        return $errors;
    }

}
