<?php

namespace UserBundle\Redirection;


use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Doctrine\ORM\EntityManagerInterface;
use UserBundle\Entity\Historique;

class AfterLoginRedirection implements AuthenticationSuccessHandlerInterface
{

    private $router;
    private $em;

    public function __construct(RouterInterface $router, EntityManagerInterface $em)
    {
        $this->router = $router;
        $this->em = $em;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token)
    {
        $roles = $token->getRoles();

        $rolesTab = array_map(function($role){
            return $role->getRole();
        }, $roles);

        if (in_array('ROLE_ADMIN', $rolesTab, true))
            $redirection = new RedirectResponse($this->router->generate('admin_page_index'));
        else
            $redirection = new RedirectResponse($this->router->generate('compte_user_manager'));

        /* Ajout de l'historique de l'utilisateur */
        $historique = new Historique;

        $historique->setContenu('Connexion réussie');
        $historique->setUtilisateur($token->getUser());
        $historique->setIp($request->getClientIp());
        $this->em->persist($historique);
        $this->em->flush();

        return $redirection;
    }
}