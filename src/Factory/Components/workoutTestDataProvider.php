<?php


namespace App\Factory\Components;


use App\Entity\User;
use App\Entity\Workouts;
use Doctrine\ORM\EntityManagerInterface;

class workoutTestDataProvider
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function addWorkoutsToUsers() {
        $users = $this->entityManager->getRepository(User::class)->findBy(['isEnabled' => true]);
        if($users) {
            /** @var User $user */
            foreach ($users as $user) {
                $this->addWorkout($user);
            }
        }
    }

    private function addWorkout(User $user)
    {
        $workoutNumber = rand(1, 50);
        $workoutsToAssign = array_rand(workoutTestData::WORKOUT_TEST_DATA, $workoutNumber);
        foreach ($workoutsToAssign as $key) {
            $workoutData = json_decode(workoutTestData::WORKOUT_TEST_DATA[$key], true);
            if ($workoutData['Completed'] and $workoutData['TssActual']) {
                $workoutId = $workoutData['Id'];
                $workoutDay = $workoutData['WorkoutDay'];
                $distance = $workoutData['Distance'];
                $totalTime = $workoutData['TotalTime'];
                $energy = $workoutData['Energy'];
                $tssActual = $workoutData['TssActual'];
                $elevationGain = $workoutData['ElevationGain'];
                $workoutType = $workoutData['WorkoutType'];
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

}
