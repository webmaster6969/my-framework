<?php

declare(strict_types=1);

namespace App\domain\Notification\Domain\Model\Entities;

use App\domain\Auth\Domain\Model\Entities\User;
use App\domain\Task\Domain\Model\Entities\Task;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "notifications")]
#[ORM\HasLifecycleCallbacks]
class Notification
{
    /**
     * @var int
     */
    #[ORM\Id]
    #[ORM\Column(type: "integer")]
    #[ORM\GeneratedValue]
    private int $id;

    /**
     * @var User
     */
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: "user_id", referencedColumnName: "id", nullable: false)]
    private User $user;

    /**
     * @var Task
     */
    #[ORM\ManyToOne(targetEntity: Task::class)]
    #[ORM\JoinColumn(name: "task_id", referencedColumnName: "id", nullable: false)]
    private Task $task;

    /**
     * @var \DateTimeInterface|\DateTimeImmutable
     */
    #[ORM\Column(type: "datetime")]
    private \DateTimeInterface $sentAt;

    /**
     * @var \DateTimeImmutable
     */
    #[ORM\Column(type: "datetime_immutable")]
    private \DateTimeImmutable $created_at;

    /**
     * @var \DateTimeImmutable
     */
    #[ORM\Column(type: "datetime_immutable")]
    private \DateTimeImmutable $updated_at;

    /**
     * @param User $user
     * @param Task $task
     */
    public function __construct(User $user, Task $task)
    {
        $this->user = $user;
        $this->task = $task;
        $this->sentAt = new \DateTimeImmutable();
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @return Task
     */
    public function getTask(): Task
    {
        return $this->task;
    }

    /**
     * @return \DateTimeInterface
     */
    public function getSentAt(): \DateTimeInterface
    {
        return $this->sentAt;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->created_at;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getUpdatedAt(): \DateTimeImmutable
    {
        return $this->updated_at;
    }

    /**
     * @return void
     */
    #[ORM\PrePersist]
    public function onPrePersist(): void
    {
        $now = new \DateTimeImmutable();
        $this->created_at = $now;
        $this->updated_at = $now;
    }

    /**
     * @return void
     */
    #[ORM\PreUpdate]
    public function onPreUpdate(): void
    {
        $this->updated_at = new \DateTimeImmutable();
    }

    /**
     * @param int $id
     * @return void
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }
}