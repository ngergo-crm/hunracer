<?php

namespace App\Controller;

use App\Entity\Config;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @IsGranted("IS_AUTHENTICATED_FULLY")
 */
class AccountController extends AbstractController
{
    /**
     * @Route("/profile", name="account")
     */
    public function index(): Response
    {
        $workoutYearStart = $this->getDoctrine()->getRepository(Config::class)->findOneBy(["settingKey" => 'workoutYearStart']);
        return $this->render('account/account.html.twig', [
            'workoutYearStart' => $workoutYearStart->getSettingValue()
        ]);
    }
}
