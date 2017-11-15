<?php

	namespace GlobalBundle\Service;

	class Tool{

        /**
         * Retourne l'index du parent dans un tableau récursif
         */
        public function recursive_array_search($needle,$haystack)
        {
            foreach($haystack as $key=>$value) {
                $current_key = $key;
                if($needle === $value OR (is_array($value) && $this->recursive_array_search($needle,$value) !== false)) {
                    return $current_key;
                }
            }

            return false;
        }

	}

?>