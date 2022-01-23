<?php

namespace App\Entity\Logs;

use App\Entity\Logs\UserLog;
use App\Entity\User;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class SecurityLog extends UserLog
{
    public function __construct(?User $user)
    {
        parent::__construct($user);
    }

}
