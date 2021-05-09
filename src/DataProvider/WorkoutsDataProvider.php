<?php


namespace App\DataProvider;


use ApiPlatform\Core\DataProvider\CollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\ContextAwareCollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\DenormalizedIdentifiersAwareItemDataProviderInterface;
use ApiPlatform\Core\DataProvider\ItemDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\Entity\User;
use App\Entity\Workouts;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class WorkoutsDataProvider implements ContextAwareCollectionDataProviderInterface, DenormalizedIdentifiersAwareItemDataProviderInterface, RestrictedDataProviderInterface
{
    private $collectionDataProvider;
    private $security;
    private $itemDataProvider;
    private $entityManager;

    public function __construct(CollectionDataProviderInterface $collectionDataProvider, ItemDataProviderInterface $itemDataProvider, Security $security, EntityManagerInterface $entityManager)
    {
        $this->security = $security;
        $this->collectionDataProvider = $collectionDataProvider;
        $this->itemDataProvider = $itemDataProvider;
        $this->entityManager = $entityManager;
    }

    public function getCollection(string $resourceClass, string $operationName = null, array $context = [])
    {
        if (($context['collection_operation_name'] ?? null) === 'getWorkoutDays') {
            /** @var User $user */
            $user = $this->entityManager->getRepository(User::class)->findOneBy(['uuid' => $context['filters']['user']]);
            $timeRange = $context['filters']['workoutDay'];
            return $this->entityManager->getRepository(Workouts::class)->getWorkoutDays($user, $timeRange);
        }

        /** @var Workouts[] $workouts */
        $workouts = $this->collectionDataProvider->getCollection($resourceClass, $operationName, $context);

        return $workouts;
    }

    public function getItem(string $resourceClass, $id, string $operationName = null, array $context = [])
    {
        /** @var Workouts|null $item */
        $item = $this->itemDataProvider->getItem($resourceClass, $id, $operationName, $context);
        if (!$item) {
            return null;
        }
        return $item;
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return $resourceClass === Workouts::class;
    }
}
