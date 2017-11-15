<?php

namespace UserBundle\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Doctrine\ORM\EntityManagerInterface;

class UserByEmailValidator extends ConstraintValidator
{

    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function validate($value, Constraint $constraint)
    {

        if (empty($value)) $this->context->addViolation('Complétez le champ email');
        else{
            if(!filter_var($value, FILTER_VALIDATE_EMAIL)) $this->context->addViolation('Le format de l\'email n\'est pas bon');
            else{
                $user = $this->em->getRepository('UserBundle:User')
                                 ->findBy(['email' => $value, 'isActive' => true],[]);
                if(empty($user)) $this->context->addViolation('Cet utilisateur n\'existe pas');
            }
        }

    }
}