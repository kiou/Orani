<?php

	namespace GlobalBundle\Service;

	class Recherche{

		private $session;
		private $request;

		public function __construct(
				\Symfony\Component\HttpFoundation\Session\Session $session,
				\Symfony\Component\HttpFoundation\RequestStack $request
			)
		{
			$this->session = $session;
			$this->request = $request->getCurrentRequest();
		}

		public function setRecherche($prefix, $variables = array())
		{

			$return = array();

			/* Ajout des variables du formulaire en session */
			foreach ($variables as $value) {

				if(!is_null($this->request->request->get($value))){
					$this->session->set($prefix.''.$value, $this->request->request->get($value));
				}
					
				$return[$value] = $this->session->get($prefix.''.$value);

			}

			return $return;

		}

	}

?>