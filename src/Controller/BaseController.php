<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BaseController extends AbstractController
{
    public const COOKIE_TOKEN_NAME = 'tpToken';

    public function getTokenName() {
        return sprintf('%s_%s', self::COOKIE_TOKEN_NAME, $this->getUser()->getUuid());
    }
}
