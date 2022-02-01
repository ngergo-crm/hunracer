<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\ConfigRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=ConfigRepository::class)
 * @ApiResource(
 *     accessControl="is_granted('IS_AUTHENTICATED_FULLY')",
 *     collectionOperations={
 *          "get"={
 *              "normalization_context"={"groups"={"config:read"}}
 *          }
 *     },
 *     itemOperations={
 *          "get",
 *          "put"={
 *              "access_control"="is_granted('ROLE_SUPER_ADMIN')"
 *          }
 *     }
 * )
 *
 */
class Config
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"admin:write", "user:read", "config:read"})
     */
    private $settingKey;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"admin:write", "user:read", "config:read"})
     */
    private $settingValue;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSettingKey(): ?string
    {
        return $this->settingKey;
    }

    public function setSettingKey(string $settingKey): self
    {
        $this->settingKey = $settingKey;

        return $this;
    }

    public function getSettingValue(): ?string
    {
        return $this->settingValue;
    }

    public function setSettingValue(string $settingValue): self
    {
        $this->settingValue = $settingValue;

        return $this;
    }
}
