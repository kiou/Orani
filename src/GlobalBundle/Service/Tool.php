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

        /**
         * Truncate a string
         */
        public function truncate($value,$max_caracteres){

            if (strlen($value)>$max_caracteres){
                $value = substr($value, 0, $max_caracteres);
                $position_espace = strrpos($value, " ");

                if($position_espace == false)
                    $position_espace = strrpos($value, "-");

                $value = substr($value, 0, $position_espace);
                $value = $value."...";
            }

            return $value;

        }

	}

?>