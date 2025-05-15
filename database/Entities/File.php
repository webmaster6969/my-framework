<?php

namespace Database\Entities;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'files')]
class File
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\ManyToOne(targetEntity: Storage::class, inversedBy: 'files')]
    #[ORM\JoinColumn(name: 'storage_id', referencedColumnName: 'id', nullable: false)]
    private Storage $storage;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', nullable: false)]
    private User $user;

    #[ORM\Column(type: 'string', length: 255)]
    private string $name;

    #[ORM\Column(name: 'name_origin', type: 'string', length: 255)]
    private string $nameOrigin;

    #[ORM\Column(type: 'string', length: 255)]
    private string $hash;

    // ======= Accessors =======

    public function getId(): int
    {
        return $this->id;
    }

    public function getStorage(): Storage
    {
        return $this->storage;
    }

    public function setStorage(?Storage $storage): void
    {
        $this->storage = $storage;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getNameOrigin(): string
    {
        return $this->nameOrigin;
    }

    public function setNameOrigin(string $nameOrigin): void
    {
        $this->nameOrigin = $nameOrigin;
    }

    public function getHash(): string
    {
        return $this->hash;
    }

    public function setHash(string $hash): void
    {
        $this->hash = $hash;
    }
}
