<?php


namespace App\DataProvider;


use ApiPlatform\Core\DataProvider\CollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\ContextAwareCollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\DenormalizedIdentifiersAwareItemDataProviderInterface;
use ApiPlatform\Core\DataProvider\ItemDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\Entity\User;
use Symfony\Component\Security\Core\Security;

/**
 * Amikor kiszedjük a DB-ből a user adatait, akkor lép akcióba
 * Class UserDataProvider
 * @package App\DataProvider
 */
class UserDataProvider implements ContextAwareCollectionDataProviderInterface, DenormalizedIdentifiersAwareItemDataProviderInterface, RestrictedDataProviderInterface
{

    private $collectionDataProvider;
    private $security;
    private $itemDataProvider;

    public function __construct(CollectionDataProviderInterface $collectionDataProvider, ItemDataProviderInterface $itemDataProvider, Security $security)
    {
        $this->security = $security;
        $this->collectionDataProvider = $collectionDataProvider;
        $this->itemDataProvider = $itemDataProvider;
    }

    /**
     * Itt állítjuk be az IsMe field-et!
     */
    public function getCollection(string $resourceClass, string $operationName = null, array $context = [])
    {
        /** @var User $currentUser */
        $currentUser = $this->security->getUser();
        if (($context['collection_operation_name'] ?? null) === 'get_me') {
            //$currentUser->setIsMe(true);
            return $currentUser;
        }

        /** @var User[] $users */
        $users = $this->collectionDataProvider->getCollection($resourceClass, $operationName, $context);

        foreach ($users as $user) {
            $user->setIsMe($currentUser === $user);
        }

//        $IsMeFilter = $context[UserIsMeFilter::ISME_FILTER_CONTEXT] ?? null;
//        if ($IsMeFilter) {
//            return $currentUser;
//        }


        return $users;
    }

    public function getItem(string $resourceClass, $id, string $operationName = null, array $context = [])
    {
        /** @var User|null $item */
        $item = $this->itemDataProvider->getItem($resourceClass, $id, $operationName, $context);
        if (!$item) {
            return null;
        }
        //$item->setIsMe($this->security->getUser() === $item);
        return $item;
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return $resourceClass === User::class;
    }
}
