<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use UserBundle\Entity\User;

class LoadUserData implements FixtureInterface, ContainerAwareInterface
{

	/**
	 * @var   ContainerInterface
	 */
	private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        $userAdmin = new User();

        /* Infos de base */
        $userAdmin->setUsername('admin');
        $userAdmin->setNom('nom');
        $userAdmin->setPrenom('prenom');
        $userAdmin->setEmail('admin@colocarts.com');
        $userAdmin->setRoles(array('ROLE_ADMIN'));
        $userAdmin->setIsActive(true);

        /* Mot de passe */
        $password = $this->container->get('security.password_encoder')
                         ->encodePassword($userAdmin, 'admin');
        $userAdmin->setPassword($password);

        $manager->persist($userAdmin);
        $manager->flush();
    }
}

?>