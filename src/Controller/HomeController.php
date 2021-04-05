<?php

namespace App\Controller;

use App\Repository\WorkoutsRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends BaseController
{
    /**
     * @Route("/", name="home")
     * @param Request $request
     * @return Response
     */
    public function homepage(Request $request, WorkoutsRepository $workoutsRepository): Response
    {
        if (!$this->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirectToRoute('app_login');
        }
        $hasWorkouts = $workoutsRepository->findOneBy(['user' => $this->getUser()]);
        //dd($request->cookies->get($this->getParameter("cookieName")));
        return $this->render('home/homepage.html.twig', [
            'tokenAvailable' => $request->cookies->get($this->getParameter("cookieName"))? true : false,
            'autoRefresh' => $request->getSession()->get('autoTpQuickRefresh'),
            'hasWorkouts' => $hasWorkouts? true : false,
        ]);
    }
}
