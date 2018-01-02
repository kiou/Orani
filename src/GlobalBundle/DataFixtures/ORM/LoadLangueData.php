<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use GlobalBundle\Entity\Langue;

class LoadLangueData implements FixtureInterface, ContainerAwareInterface
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
        $langue = new Langue();

        /* Infos de base */
        $langue->setNom('Français');
        $langue->setCode('fr');

        $manager->persist($langue);
        $manager->flush();
    }
}

?>