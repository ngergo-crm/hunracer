<?php

namespace App\Controller;

use App\Entity\Config;
use App\Entity\User;
use App\Repository\WorkoutsRepository;
use App\Services\TcxParser\Parser;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
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
    public function homepage(ParameterBagInterface $parameterBag, Request $request, WorkoutsRepository $workoutsRepository): Response
    {
//        $document = new \DOMDocument();
//        $document->loadXml(file_get_contents($parameterBag->get('kernel.project_dir').'\public\tcxtest.tcx'));
//         //dd($document->saveHTML());
//         $test = new Parser($document->saveHTML());
//
        //dd($test->getJson());
//        dd( $parameterBag->get('kernel.project_dir').'\public\tcxtest.tcx');
        //new Parser()

        if (!$this->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirectToRoute('app_login');
        }
        /** @var User $user */
        $user = $this->getUser();
        $athletes = [];
        // $isTrainer = $this->getUser()->getRoles()[0] === 'ROLE_TRAINER'?? false;
        if ($this->isGranted('ROLE_TRAINER') /*and !$this->isGranted('ROLE_ADMIN')*/) {
            if(!$this->isGranted('ROLE_ADMIN')) {
                $athletes = $this->getAthletesByTrainercode($user->getTrainerCode());
            } else {
                $athletes = $this->getAthletesByTrainercode(null);
            }
            $defaultAthlete = $athletes[0]['uuid'] ?? null;
            $user = $defaultAthlete ? $this->getDoctrine()->getRepository(User::class)->findOneBy(['uuid' => $defaultAthlete]) : null;
        }
        $athleteHasWorkout = $workoutsRepository->findOneBy(['user' => $user]);
        $workoutYearStart = $this->getDoctrine()->getRepository(Config::class)->findOneBy(["settingKey" => 'workoutYearStart']);

        //dd($request->cookies);
        //dd($request->getSession()->get('autoTpQuickRefresh'), (bool)$athleteHasWorkout);
        return $this->render('home/homepage.html.twig', [
            'tokenAvailable' => $request->cookies->get($this->getTokenName()) ? true : false,
            'autoRefresh' => $request->getSession()->get('autoTpQuickRefresh'),
            'hasWorkouts' => (bool)$athleteHasWorkout,
            'athletes' => $athletes,
            'workoutYearStart' => $workoutYearStart->getSettingValue()
        ]);
    }

    private function getAthletesByTrainercode(?string $trainerCode): array
    {
        return $this->getDoctrine()->getRepository(User::class)->getAthletesByTrainer($trainerCode);
    }

    /**
     * @Route("/getSource/{sourceName}", name="getPicture", options={"expose"=true})
     * sourceName: name of the file
     */
    public function fileHandler(Request $request, $sourceName)
    {
        $sourceName = urldecode($sourceName);
        $destination = "images/accountPhoto/";
        if (is_file($destination . $sourceName)) {
            return $this->file($destination . $sourceName);
        }
        return new Response('error', 404);
    }

}
