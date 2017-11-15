<?php

    namespace UserBundle\Validator;

    use Symfony\Component\Validator\Constraint;

    /**
     * @Annotation
     */
    class UserByEmail extends Constraint
    {
        public $message = '';

        public function validatedBy()
        {
            return 'userbyemail';
        }
    }

?>