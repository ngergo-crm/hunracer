<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @IsGranted("ROLE_SUPER_ADMIN")
 */
class AdminController extends AbstractController
{
    /**
     * @Route("/felhasznalok", name="userManager")
     */
    public function userManager(): Response
    {
        return $this->render('admin/users.html.twig', [

        ]);
    }

    /**
     * @Route("/szakagak", name="sections")
     */
    public function sections(): Response
    {
        return $this->render('admin/sections.html.twig', [

        ]);
    }

    /**
     * @Route("/csapatok", name="teams")
     */
    public function teams(): Response
    {
        return $this->render('admin/teams.html.twig', [

        ]);
    }


}
