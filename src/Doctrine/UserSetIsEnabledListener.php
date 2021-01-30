<?php


namespace App\Doctrine;

use App\Entity\User;
use Symfony\Component\Security\Core\Security;


class UserSetIsEnabledListener
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function prePersist(User $user)
    {
        if ($user->getIsEnabled()) {
            return;
        }

        if ($this->security->isGranted('ROLE_SUPER_ADMIN')) {
            $user->setIsEnabled(true)
//                ->setCreatedAt(new \DateTime())
//                ->setUpdatedAt(new \DateTime())
            ;
        }
    }
}