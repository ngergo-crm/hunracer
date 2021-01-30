<?php

namespace App\Factory;

use App\Entity\User;
use App\Repository\UserRepository;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Zenstruck\Foundry\RepositoryProxy;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;

/**
 * @method static User|Proxy findOrCreate(array $attributes)
 * @method static User|Proxy random()
 * @method static User[]|Proxy[] randomSet(int $number)
 * @method static User[]|Proxy[] randomRange(int $min, int $max)
 * @method static UserRepository|RepositoryProxy repository()
 * @method User|Proxy create($attributes = [])
 * @method User[]|Proxy[] createMany(int $number, $attributes = [])
 */
final class UserFactory extends ModelFactory
{

    public const DEFAULT_PASSWORD = '123456';

    public function __construct()
    {
        parent::__construct();

    }

    protected function getDefaults(): array
    {
        return [
            'email' => self::faker()->unique()->email,
            'roles' => self::faker()->boolean(70) ? ['ROLE_USER'] : ['ROLE_ADMIN'],
            'password' => '$argon2id$v=19$m=65536,t=4,p=1$UWEyQ1JKTEZLYXJvL0RxYg$rzNbGntendsqRdTfLelS/DLAHuNQXNrQ9FyieZ6ZqY4',
            'isEnabled' => self::faker()->boolean,
            'name' => self::faker()->name,
            'phone' => self::faker()->phoneNumber,
            'createdAt' => new \DateTime('now'),
            'updatedAt' => new \DateTime('now')
        ];
    }

    protected function initialize(): self
    {
        // see https://github.com/zenstruck/foundry#initialization
        return $this
            // ->afterInstantiate(function(User $user) {})
        ;
    }

    protected static function getClass(): string
    {
        return User::class;
    }
}
