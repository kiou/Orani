<?php

	namespace GlobalBundle\Service;

	class Agenda{

        /**
         * @var array
         * La liste des jours en FR
         */
        private $jours = array('LU','MA','ME','JE','VE','SA','DI');

        /**
         * @var array
         * La liste des mois en FR
         */
        private $mois = array('Janvier','Février','Mars','Avril','Mai','Juin','Juillet','Août','Septembre','Octobre','Novembre','Décembre');

        /**
         * Retourne toutes les dates sur une année
         * @param int l'année
         * @return array
         */
        public function getCalendrier($annee){

            $r = array();

            $date = new \DateTime($annee.'-01-01');

            while($date->format('Y') <= $annee){

                $y = $date->format('Y');
                $m = $date->format('n');
                $d = $date->format('j');
                $w = str_replace('0','7',$date->format('w'));
                $r[$y][$m][$d] = $w;
                $date->add(new \DateInterval('P1D'));

            }

            return $r;

        }

        /**
         * Retourne le jour
         * @return array
         */
        public function getJours(){
            return $this->jours;
        }

        /**
         * Retourne le mois
         * @param $mois
         * @return mixed
         */
        public function getMois(){
            return $this->mois;
        }
	}

?>