<?php

namespace App\Command;

use App\Entity\User;
use App\Entity\Workouts;
use App\Services\TrainingPeaksService;
use Doctrine\ORM\EntityManagerInterface;
use Nette\Utils\Json;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class WorkoutsImporterCommand extends Command
{
    private $entityManager;
    private $tpService;
    private $bag;
    private $test;

    public function __construct(EntityManagerInterface $entityManager, TrainingPeaksService $tpService, ParameterBagInterface $bag)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
        $this->tpService = $tpService;
        $this->bag = $bag;
        $this->test = $bag->get('APP_ENV') === 'dev';
    }

    protected static $defaultName = 'workouts:last-year';

    protected function configure()
    {
        $this
            ->setDescription('Add a short description for your command')
            ->addArgument('token', InputArgument::REQUIRED)
            ->addArgument('user', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $jsonResponse = new JsonResponse();
        //$io = new SymfonyStyle($input, $output);
        $token = json_decode($input->getArgument('token'), true);
        $user = $input->getArgument('user');
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['id' => $user]);
        if ($token and $user) {
            $endpoints = $this->getEndpoints();
            foreach ($endpoints as $endpoint) {
                $response = $this->tpService->apiRequest($endpoint, $token, $this->test);
                if ($response['response']) {
                    foreach ($response['response'] as $workoutData) {
                        if ($workoutData['Completed']) {
                            $workoutId = $workoutData['Id'];
                            $workoutDay = $workoutData['WorkoutDay'];
                            $distance = $workoutData['Distance'];
                            $totalTime = $workoutData['TotalTime'];
                            $energy = $workoutData['Energy'];
                            $tssActual = $workoutData['TssActual'];
                            $elevationGain = $workoutData['ElevationGain'];
                            $workoutType = $workoutData['WorkoutType'];
                            //todo user id? Athlete id?
                            $workout = new Workouts();
                            $workout->setUser($user)
                                ->setWorkoutId($workoutId)
                                ->setWorkoutDay(new \DateTime(date("Y-m-d", strtotime($workoutDay))))
                                ->setEnergy($energy)
                                ->setTotalTime($totalTime)
                                ->setDistance($distance)
                                ->setData($workoutData)
                                ->setTss($tssActual)
                                ->setElevation($elevationGain)
                                ->setType($workoutType)
                            ;
                            $this->entityManager->persist($workout);
                            $this->entityManager->flush();
                        }
                    }
                }
                $responseToken = $response['token'];
                if ($responseToken !== $token) {
                    $token = $responseToken;
                    $jsonResponse->headers->setCookie($this->tpService->createTpCookie($response['token']));
                }
            }
        }

        return $jsonResponse;
    }

    private function getEndpoints(): array
    {
        $endpoints = [];
        $periods = $this->getWorkoutPeriods();
        foreach ($periods as $period) {
            $endpoints[] = sprintf('v1/workouts/%s/%s', $period['end'], $period['start']);
        }
        return $endpoints;
    }

    private function getWorkoutPeriods(): array
    {
        $absoluteStartDate = date("Y-m-d", strtotime('last sunday', strtotime(date('Y-m-d'))));
        $absoluteEndDate = date("Y-m-d", strtotime('-1 year', strtotime($absoluteStartDate)));

        $lastPeriodEndDate = date("Y-m-d", strtotime('-45 days', strtotime($absoluteStartDate)));
        $interval = ['start' => $absoluteStartDate, 'end' => $lastPeriodEndDate];
        $intervals[] = $interval;
        while ($absoluteEndDate < $lastPeriodEndDate) {
            $lastPeriodStart = date("Y-m-d", strtotime('-1 day', strtotime($lastPeriodEndDate)));
            $lastPeriodEndDate = date("Y-m-d", strtotime('-45 days', strtotime($lastPeriodStart)));
            $intervals[] = ['start' => $lastPeriodStart, 'end' => $lastPeriodEndDate];
        }
        $intervals[(count($intervals) - 1)]['end'] = date("Y-m-d", strtotime('next monday', strtotime(end($intervals)['end'])));
        return $intervals;
    }
}
