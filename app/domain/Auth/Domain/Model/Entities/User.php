<?php

declare(strict_types=1);

namespace App\domain\Auth\Domain\Model\Entities;

use App\domain\Task\Domain\Model\Entities\Task;
use DateMalformedStringException;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "users")]
class User
{
    #[ORM\Id]
    #[ORM\Column(type: "integer")]
    #[ORM\GeneratedValue]
    private int $id;

    #[ORM\Column(type: "string", length: 100)]
    private string $name;

    #[ORM\Column(type: "string", length: 150, unique: true)]
    private string $email;

    #[ORM\Column(type: "string", length: 255)]
    private string $password;

    #[ORM\Column(type: "string", length: 50, nullable: true)]
    private ?string $telegramChatId;

    #[ORM\OneToMany(mappedBy: "user", targetEntity: Task::class, cascade: ["persist", "remove"])]
    private Collection $tasks;

    #[ORM\Column(type: "datetime")]
    private \DateTime $created_at;

    #[ORM\Column(type: "datetime")]
    private \DateTime $updated_at;

    /**
     * @throws DateMalformedStringException
     */
    public function __construct(string $name, string $email, string $password, string $created_at, string $updated_at)
    {
        $this->name = $name;
        $this->email = $email;
        $this->password   = $password;
        $this->telegramChatId = null;
        $this->tasks = new ArrayCollection();
        $this->created_at = new \DateTime($created_at);
        $this->updated_at = new \DateTime($updated_at);
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getTelegramChatId(): ?string
    {
        return $this->telegramChatId;
    }

    public function setTelegramChatId(?string $chatId): void
    {
        $this->telegramChatId = $chatId;
    }

    public function getTasks(): Collection
    {
        return $this->tasks;
    }

    public function addTask(Task $task): void
    {
        if (!$this->tasks->contains($task)) {
            $this->tasks->add($task);
            $task->setUser($this);
        }
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->created_at;
    }

    public function getUpdatedAt(): \DateTime
    {
        return $this->updated_at;
    }

    public function getPassword(): string
    {
        return $this->password;
    }
}