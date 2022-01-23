<?php


namespace App\DataProvider;


use ApiPlatform\Core\DataProvider\CollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\ContextAwareCollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\DenormalizedIdentifiersAwareItemDataProviderInterface;
use ApiPlatform\Core\DataProvider\ItemDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\Entity\User;
use App\Repository\UserLogRepository;
use Doctrine\ORM\EntityManagerInterface;
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
    private $manager;

    public function __construct(CollectionDataProviderInterface $collectionDataProvider, ItemDataProviderInterface $itemDataProvider, Security $security, EntityManagerInterface $manager)
    {
        $this->security = $security;
        $this->collectionDataProvider = $collectionDataProvider;
        $this->itemDataProvider = $itemDataProvider;
        $this->manager = $manager;
    }

    /**
     * Itt állítjuk be az IsMe field-et!
     */
    public function getCollection(string $resourceClass, string $operationName = null, array $context = [])
    {
        /** @var User $currentUser */
        $currentUser = $this->security->getUser();
        if (($context['collection_operation_name'] ?? null) === 'get_me') {
            return $currentUser;
        }

        /** @var User[] $users */
        $users = $this->collectionDataProvider->getCollection($resourceClass, $operationName, $context);

        foreach ($users as $user) {
            $user->setIsMe($currentUser === $user);
            if($currentUser->getRoles()[0] === 'ROLE_SUPER_ADMIN') {
                $latestLogin = $this->getLatestLogin($user);
                $user->setLogs(['latestLogin' => $latestLogin]);
            }
        }

        return $users;
    }

    public function getItem(string $resourceClass, $id, string $operationName = null, array $context = [])
    {
        if(isset($context['fetch_data']) and $operationName = 'get') {
            $context['fetch_data'] = true;
        }
       // dd($resourceClass, $id, $operationName, $context);
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

    private function getLatestLogin($user)
    {
        $query = $this->manager->createQuery("SELECT logs FROM App\Entity\Logs\UserLog logs WHERE logs INSTANCE OF App\Entity\Logs\SecurityLog AND logs.user = :userid ORDER BY logs.actionTime DESC")
            ->setParameter('userid', $user)
            ->setMaxResults(1);
        $result = $query->getResult();
        if(count($result) > 0) {
            $result = $result[0];
        } else {
            $result = null;
        }
        return $result;
    }
}
