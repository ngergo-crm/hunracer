<?php

namespace App\Controller;

use App\Entity\Config;
use App\Entity\Workouts;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @IsGranted("ROLE_ADMIN")
 */
class AdminController extends AbstractController
{
    /**
     * @Route("/felhasznalok", name="userManager")
     */
    public function userManager(): Response
    {
        /** @var Config $workoutYearStart */
        $workoutYearStart = $this->getDoctrine()->getRepository(Config::class)->findOneBy(["settingKey" => 'workoutYearStart']);
        return $this->render('admin/users.html.twig', [
            'workoutYearStart' => $workoutYearStart->getSettingValue()
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

    /**
     * @Route("/beallitasok", name="settings")
     */
    public function settings(): Response
    {
        return $this->render('admin/settings.html.twig', [

        ]);
    }

    /**
     * @Route("/getAthletePerformance", name="getAthletePerformance", methods={"POST"}, options={"expose"=true})
     * @param Request $request
     * @return JsonResponse
     */
    public function getAthletePerformance(Request $request)
    {
        //todo make periods dynamic
        $userIds = json_decode($request->request->get('athleteIds'), true);
        $year = $request->request->get('year');
        $year = date("$year-m-d", strtotime($year));
        $period = date("Y-11-01", strtotime($year));
        if ($year < $period) {
            $nextPeriodEnd = $period;
            $lastPeriodStart = date("Y-11-01", strtotime('-1 year', strtotime($year)));
        } else {
            $lastPeriodStart = $period;
            $nextPeriodEnd = date("Y-11-01", strtotime('+1 year', strtotime($year)));
        }
        //dd($lastPeriodStart, $nextPeriodEnd);
        $data = $this->getDoctrine()->getRepository(Workouts::class)->getAthletePerformance($userIds, $lastPeriodStart, $nextPeriodEnd);
        $data = $this->addNoData($data, $userIds);
        return new JsonResponse(
            $data
        );
    }

    private function findUser(string $userId, array $data): bool
    {
        $N = count($data);
        $i = 0;
        while ($i < $N and !($data[$i]["uuid"]->toString() === $userId)) {
            //dump($userId, $data[$i]["uuid"]->toString(), $data[$i]["uuid"]->toString() == $userId);
            $i++;
        }
        return $i < $N;
    }

    private function addNoData(array $data, array $userIds): array
    {
        $hasData = count($data) > 0;
        $noData = [];
        foreach ($userIds as $userId) {
            $result = false;
            if($hasData) {
                $result = $this->findUser($userId, $data);
            }
            if(!$result) {
                $noData[] = [
                    'uuid' => $userId,
                    "distance" => "0",
                    "totalTime" => "0"
                ];
            }
        }
        foreach ($noData as $item) {
            $data[] = $item;
        }
        return $data;
    }

}
