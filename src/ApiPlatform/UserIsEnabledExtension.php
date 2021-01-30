<?php


namespace App\ApiPlatform;


use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use App\Entity\User;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Security\Core\Security;

/**
 * Automata filter
 * Két interface lett meghívva
 * 1. Az első a collection operációkra vonatkozik: a szuperadminon kívül más nem láthatja a nem engedélyezett user-eket
 * 2. Azoknál az operációknál sem, amelyek 1 item-et adnak vissza. Ez igaz az update-re és a delete-re is.
 * Class UserIsEnabledExtension
 * @package App\ApiPlatform
 */
class UserIsEnabledExtension implements QueryCollectionExtensionInterface, QueryItemExtensionInterface
{

    private $security;
    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function applyToCollection(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, string $operationName = null)
    {
        $this->addWhere($queryBuilder, $resourceClass);
    }


    public function applyToItem(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, array $identifiers, string $operationName = null, array $context = [])
    {
        $this->addWhere($queryBuilder, $resourceClass);
    }

    private function addWhere(QueryBuilder $queryBuilder, string $resourceClass): void
    {
        if ($resourceClass !== User::class) {
            return;
        }

        if ($this->security->isGranted('ROLE_SUPER_ADMIN')) {
            return;
        }

        $rootAlias = $queryBuilder->getRootAliases()[0];
        $queryBuilder->andWhere(sprintf('%s.isEnabled = :isEnabled', $rootAlias))
            ->setParameter('isEnabled', true);
    }
}