<?php

namespace App\Entity;

use App\Repository\BotInfosRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BotInfosRepository::class)]
class BotInfos
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'bigint', nullable: true)]
    private $users;

    #[ORM\Column(type: 'bigint', nullable: true)]
    private $guilds;

    #[ORM\Column(type: 'bigint', nullable: true)]
    private $uptime;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsers(): ?string
    {
        return $this->users;
    }

    public function setUsers(?string $users): self
    {
        $this->users = $users;

        return $this;
    }

    public function getGuilds(): ?string
    {
        return $this->guilds;
    }

    public function setGuilds(?string $guilds): self
    {
        $this->guilds = $guilds;

        return $this;
    }

    public function getUptime(): ?string
    {
        return $this->uptime;
    }

    public function setUptime(?string $uptime): self
    {
        $this->uptime = $uptime;

        return $this;
    }
}
