<?php

namespace Database\Entities;

use Core\Support\Crypt\Crypt;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'storages')]
#[ORM\HasLifecycleCallbacks]
class Storage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'storages')]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', nullable: false)]
    private User $user;

    #[ORM\Column(type: 'string', length: 255)]
    private string $name;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $encryptedDescription = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $pin = null;

    #[ORM\OneToMany(targetEntity: File::class, mappedBy: 'storage')]
    private Collection $files;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $updatedAt = null;

    public function __construct()
    {
        $this->files = new ArrayCollection();
    }

    // ======= Accessors =======

    public function getId(): int
    {
        return $this->id;
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

    public function getDescription(): string
    {
        if ($this->encryptedDescription === null) {
            return '';
        }

        try {
            return Crypt::decrypt($this->encryptedDescription);
        } catch (\Exception $e) {
            return '';
        }
    }

    public function setDescription(string $description): void
    {
        $this->encryptedDescription = Crypt::encrypt($description);
    }

    public function getPin(): ?string
    {
        return $this->pin;
    }

    public function setPin(?string $pin): void
    {
        $this->pin = $pin;
    }

    public function getFiles(): Collection
    {
        return $this->files;
    }

    public function addFile(File $file): void
    {
        if (!$this->files->contains($file)) {
            $this->files[] = $file;
            $file->setStorage($this);
        }
    }

    public function removeFile(File $file): void
    {
        if ($this->files->removeElement($file)) {
            if ($file->getStorage() === $this) {
                $file->setStorage(null);
            }
        }
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function updateTimestamp(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }
}