<?php

namespace App\MessageHandler;

use App\Entity\WorkoutDetail;
use App\Entity\Workouts;
use App\Message\WorkoutDetailForDownload;
use App\Repository\WorkoutsRepository;
use App\Services\TrainingPeaksService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

/**
 *  bin/console messenger:setup-transports - ez csinálja meg a messenger_messager táblát a DB-ben
 *  bin/console debug:messenger
 *  bin/console messenger:consume -vv
 *  bin/console messenger:stop-workers
 * bin/console messenger:failed:show
 * bin/console messenger:failed:show 115 -vv
 */
class SaveWorkoutDetailHandler implements MessageHandlerInterface
{
    private $tpService;
    private $manager;
    private $workoutsRepository;

    public function __construct(EntityManagerInterface $manager, TrainingPeaksService $tpService, WorkoutsRepository $workoutsRepository)
    {
        $this->tpService = $tpService;
        $this->manager = $manager;
        $this->workoutsRepository = $workoutsRepository;
    }

    public function __invoke(WorkoutDetailForDownload $workoutDetail)
    {
        $response = $this->tpService->apiRequest($workoutDetail->getEndpoint(), $workoutDetail->getToken(), $workoutDetail->getIsTest());
        /** @var Workouts $workout */
        $workout = $this->workoutsRepository->findOneBy(['id' => $workoutDetail->getWishlistId()]);
        if(!is_array($response)) {
            $response['response'] = $response;
        }
//        $id = $workoutDetail->getWishlistId();
//        $str = print_r($response, true);
//        file_put_contents("data_$id.txt", $str);

        $workoutDetailForSave = new WorkoutDetail();
        $workoutDetailForSave->setWorkout($workout)
            ->setData($response['response']);
        $this->manager->persist($workoutDetailForSave);
        $this->manager->flush();
    }
}
