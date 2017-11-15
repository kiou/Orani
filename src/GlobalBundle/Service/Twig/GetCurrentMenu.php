<?php

    namespace GlobalBundle\Service\Twig;

    use Symfony\Component\HttpFoundation\RequestStack;

    class GetCurrentMenu extends \Twig_Extension
    {

        private $request;

        public function __construct(RequestStack $request)
        {
            $this->request = $request;
        }

        public function getFunctions()
        {
            return array(
                new \Twig_SimpleFunction('getCurrentMenu', array($this, 'getCurrentMenu')),
            );
        }

        public function getCurrentMenu($pages)
        {
   
            $current = $this->request->getCurrentRequest()->attributes->get('_route');
            if(in_array($current, $pages)) return 'current';

        }
        
        public function getName()
        {
            return 'getCurrentMenu';
        }

    }
