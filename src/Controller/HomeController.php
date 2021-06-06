<?php

namespace App\Controller;

use App\Entity\User;
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
        /** @var User $user */
        $user = $this->getUser();
        $athletes = [];
       // $isTrainer = $this->getUser()->getRoles()[0] === 'ROLE_TRAINER'?? false;
        if ($this->isGranted('ROLE_TRAINER')) {
            $athletes = $this->getAthletesByTrainercode($user->getTrainerCode());
            $defaultAthlete = $athletes[0]['uuid'] ?? null;
            $user = $defaultAthlete? $this->getDoctrine()->getRepository(User::class)->findOneBy(['uuid' => $defaultAthlete]) : null;
        }
        $athleteHasWorkout = $workoutsRepository->findOneBy(['user' => $user]);
        //dd($request->cookies->get($this->getParameter("cookieName")));
        return $this->render('home/homepage.html.twig', [
            'tokenAvailable' => $request->cookies->get($this->getParameter("cookieName")) ? true : false,
            'autoRefresh' => $request->getSession()->get('autoTpQuickRefresh'),
            'hasWorkouts' => (bool)$athleteHasWorkout,
            'athletes' => $athletes
        ]);
    }

    private function getAthletesByTrainercode(string $trainerCode): array
    {
        return $this->getDoctrine()->getRepository(User::class)->getAthletesByTrainer($trainerCode);
    }

}
