<?php

namespace App\Validator;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class CheckOldPasswordBeforeChangeValidator extends ConstraintValidator
{
    private $passwordEncoder;
    private $userRepository;
    private $security;

    public function __construct(Security $security, UserPasswordEncoderInterface $passwordEncoder, UserRepository $userRepository)
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->userRepository = $userRepository;
        $this->security = $security;
    }

    public function validate($value, Constraint $constraint)
    {
        /* @var $constraint \App\Validator\CheckOldPasswordBeforeChange */

        $user = $this->security->getUser();
        if (!$user instanceof User) {
            $this->context->buildViolation($constraint->anonymousMessage)
                ->addViolation();
            return;
        }

        if($this->passwordEncoder->isPasswordValid($user, $value))
            return;

        $this->context->buildViolation($constraint->message)
            ->addViolation();
    }
}
