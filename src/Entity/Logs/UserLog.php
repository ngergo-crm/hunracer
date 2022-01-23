<?php

namespace App\Entity\Logs;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\User;
use App\Repository\UserLogRepository;
use Doctrine\ORM\Mapping as ORM;
use DateTime;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Annotation\Groups;

// * @ApiResource()
/**
 * @ORM\Entity
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="Action", type="string", length=30)
 * @ORM\DiscriminatorMap({
 *     "userlog" = "Userlog",
 *     "security" = "SecurityLog"
 * })
 */
class UserLog
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"admin:read"})
     */
    private $actionTime;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"admin:read"})
     */
    private $ip;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="useLogs")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    public function __construct(?User $user)
    {
        $this->user = $user;
        $this->ip = Request::createFromGlobals()->getClientIp();
        $this->actionTime = new DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getActionTime(): ?\DateTimeInterface
    {
        return $this->actionTime;
    }

    public function setActionTime(\DateTimeInterface $actionTime): self
    {
        $this->actionTime = $actionTime;

        return $this;
    }

    public function getIp(): ?string
    {
        return $this->ip;
    }

    public function setIp(?string $ip): self
    {
        $this->ip = $ip;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }
}
