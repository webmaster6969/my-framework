<?php

declare(strict_types=1);

namespace App\domain\Auth\Domain\Model\Entities;

use App\domain\Task\Domain\Model\Entities\Task;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "users")]
#[ORM\HasLifecycleCallbacks]
class User
{
    /**
     * @var ?int
     */
    #[ORM\Id]
    #[ORM\Column(type: "integer")]
    #[ORM\GeneratedValue]
    private ?int $id;

    /**
     * @var string
     */
    #[ORM\Column(type: "string", length: 100)]
    private string $name;

    /**
     * @var string
     */
    #[ORM\Column(type: "string", length: 150, unique: true)]
    private string $email;

    /**
     * @var string
     */
    #[ORM\Column(type: "string", length: 255)]
    private string $password;

    /**
     * @var ?string
     */
    #[ORM\Column(type: "string", length: 255)]
    private ?string $encryption_key;

    /**
     * @var string|null
     */
    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private ?string $google2faSecret;

    /**
     * @var string|null
     */
    #[ORM\Column(type: "string", length: 50, nullable: true)]
    private ?string $telegramChatId;

    /**
     * @var Collection<int, Task>
     */
    #[ORM\OneToMany(targetEntity: Task::class, mappedBy: "user", cascade: ["persist", "remove"])]
    private Collection $tasks;

    /**
     * @var DateTimeImmutable
     */
    #[ORM\Column(type: "datetime_immutable")]
    private DateTimeImmutable $created_at;

    /**
     * @var DateTimeImmutable
     */
    #[ORM\Column(type: "datetime_immutable")]
    private DateTimeImmutable $updated_at;

    /**
     * @param string $name
     * @param string $email
     * @param string $password
     * @param string|null $encryptionKey
     */
    public function __construct(string $name, string $email, string $password, ?string $encryptionKey = null)
    {
        $this->name = $name;
        $this->email = $email;
        $this->password = $password;
        $this->encryption_key = $encryptionKey;
        $this->telegramChatId = null;
        $this->tasks = new ArrayCollection();
    }

    /**
     * @param int|null $id
     * @return void
     */
    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return ?int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @return string|null
     */
    public function getTelegramChatId(): ?string
    {
        return $this->telegramChatId;
    }

    /**
     * @param string|null $chatId
     * @return void
     */
    public function setTelegramChatId(?string $chatId): void
    {
        $this->telegramChatId = $chatId;
    }

    /**
     * @return Collection<int, Task>
     */
    public function getTasks(): Collection
    {
        return $this->tasks;
    }

    /**
     * @param Task $task
     * @return void
     */
    public function addTask(Task $task): void
    {
        if (!$this->tasks->contains($task)) {
            $this->tasks->add($task);
            $task->setUser($this);
        }
    }

    /**
     * @return DateTimeImmutable
     */
    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->created_at;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updated_at;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @return string|null
     */
    public function getGoogle2faSecret(): ?string
    {
        return $this->google2faSecret;
    }

    /**
     * @param string $name
     * @return void
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @param string|null $google2faSecret
     * @return void
     */
    public function setGoogle2faSecret(?string $google2faSecret): void
    {
        $this->google2faSecret = $google2faSecret;
    }

    /**
     * @return void
     */
    #[ORM\PrePersist]
    public function onPrePersist(): void
    {
        $now = new DateTimeImmutable();
        $this->created_at = $now;
        $this->updated_at = $now;
    }

    /**
     * @return void
     */
    #[ORM\PreUpdate]
    public function onPreUpdate(): void
    {
        $this->updated_at = new DateTimeImmutable();
    }

    /**
     * @return ?string
     */
    public function getEncryptionKey(): ?string
    {
        return $this->encryption_key;
    }

    /**
     * @param ?string $encryptionKey
     * @return void
     */
    public function setEncryptionKey(?string $encryptionKey): void
    {
        $this->encryption_key = $encryptionKey;
    }
}