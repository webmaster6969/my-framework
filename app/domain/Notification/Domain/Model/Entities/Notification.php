<?php

declare(strict_types=1);

namespace App\domain\Notification\Domain\Model\Entities;

use App\domain\Auth\Domain\Model\Entities\User;
use App\domain\Task\Domain\Model\Entities\Task;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "notifications")]
class Notification
{
    #[ORM\Id]
    #[ORM\Column(type: "integer")]
    #[ORM\GeneratedValue]
    private int $id;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: "user_id", referencedColumnName: "id", nullable: false)]
    private User $user;

    #[ORM\ManyToOne(targetEntity: Task::class)]
    #[ORM\JoinColumn(name: "task_id", referencedColumnName: "id", nullable: false)]
    private Task $task;

    #[ORM\Column(type: "datetime")]
    private \DateTimeInterface $sentAt;

    #[ORM\Column(type: "datetime")]
    private \DateTime $created_at;

    #[ORM\Column(type: "datetime")]
    private \DateTime $updated_at;

    public function __construct(User $user, Task $task)
    {
        $this->user = $user;
        $this->task = $task;
        $this->sentAt = new \DateTimeImmutable();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getTask(): Task
    {
        return $this->task;
    }

    public function getSentAt(): \DateTimeInterface
    {
        return $this->sentAt;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->created_at;
    }

    public function getUpdatedAt(): \DateTime
    {
        return $this->updated_at;
    }
}