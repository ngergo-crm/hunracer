<?php


namespace App\DataPersister;


use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use ApiPlatform\Core\DataPersister\DataPersisterInterface;
use App\Entity\User;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Security;

/**
 * A datapersister arra jó, hogy a DB-be mentés előtt vagy után futtatunk valamilyen kódot akár csak bizonyos helyzetekben / operációknál
 * Az object setelése és a DB-be való elmentése között fut le. Itt lehet még módosítani az adatokat, pl. a jelszót encode-olni
 * Post és Put operátoroknál fut le
 */
class UserDataPersister implements ContextAwareDataPersisterInterface
{
    private $decoratedDataPersister;
    private $userPasswordEncoder;
    private $security;

    public function __construct(DataPersisterInterface $decoratedDataPersister, UserPasswordEncoderInterface $userPasswordEncoder, Security $security)
    {
        $this->decoratedDataPersister = $decoratedDataPersister;
        $this->userPasswordEncoder = $userPasswordEncoder;
        $this->security = $security;
    }

    public function supports($data, array $context = []): bool
    {
        return $data instanceof User;
    }

    /**
     * @param User $data
     */
    public function persist($data, array $context = [])
    {
        if (($context['item_operation_name'] ?? null) === 'put') {
            //todo ez csak akkor fut le, ha valaki update-l egy user-t
            //todo ez lehet collection_operation_name = post is akär!!!
        }

        if(!$data->getId()) {
            //new user has been created
            //todo do our own logic for example sending email
        }

        if($data->getPlainPassword()) {
            $data->setPassword(
                $this->userPasswordEncoder->encodePassword($data, $data->getPlainPassword())
            );
            $data->eraseCredentials();
        }

       // $data->setIsMe($this->security->getUser() === $data);
        return $this->decoratedDataPersister->persist($data);
    }

    public function remove($data, array $context = [])
    {
        $this->decoratedDataPersister->remove($data);
    }


}