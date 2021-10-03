<?php

namespace App\Command;

use App\Entity\Gender;
use App\Entity\WorkoutDetail;
use App\Entity\Workouts;
use App\Repository\WorkoutsRepository;
use App\Services\TrainingPeaksService;
use Doctrine\ORM\EntityManagerInterface;
use Gedmo\Mapping\Annotation\Language;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class WorkoutDetailImporterCommand extends Command
{
    protected static $defaultName = 'importworkoutDetail';

    private $manager;
    private $tpService;
    private $workoutsRepository;

    public function __construct(EntityManagerInterface $manager, TrainingPeaksService $tpService, WorkoutsRepository $workoutsRepository)
    {
        parent::__construct();
        $this->manager = $manager;
        $this->tpService = $tpService;
        $this->workoutsRepository = $workoutsRepository;
    }

    protected function configure()
    {
        $this
            ->setDescription('Imports workoutDetails')
            ->addArgument('endpoints', InputArgument::REQUIRED)
            ->addArgument('token', InputArgument::REQUIRED)
            ->addArgument('test', InputArgument::REQUIRED)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $endpoints = json_decode($input->getArgument('endpoints'), true);
        $token = json_decode($input->getArgument('token'), true);
        $isTest = $input->getArgument('test');

        foreach ($endpoints as $wishlistId => $endpoint) {
            $response = $this->tpService->apiRequest($endpoint, $token, $isTest);
            $workoutDetail = new WorkoutDetail();
            /** @var Workouts $workout */
            $workout = $this->workoutsRepository->findOneBy(['id' => $wishlistId]);
            $workoutDetail->setWorkout($workout)
                ->setData($response['response']);
            $this->manager->persist($workoutDetail);
            $this->manager->flush();
        }

//        $test = new Gender();
//        $test->setDescription('test');
//        $this->manager->persist($test);
//        $this->manager->flush();
        dd($endpoints, $token, $isTest);

        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');

        return 0;
    }
}
