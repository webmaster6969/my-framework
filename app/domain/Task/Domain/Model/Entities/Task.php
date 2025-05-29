<?php

declare(strict_types=1);

namespace App\domain\Task\Domain\Model\Entities;

use App\domain\Auth\Domain\Model\Entities\User;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "tasks")]
#[ORM\Index(name: "idx_task_user_date", columns: ["user_id", "start_task", "end_task"])]
#[ORM\HasLifecycleCallbacks]
class Task
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_DONE = 'done';
    public const STATUS_CANCELED = 'canceled';

    #[ORM\Id]
    #[ORM\Column(type: "integer")]
    #[ORM\GeneratedValue]
    private int $id;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: "tasks")]
    #[ORM\JoinColumn(name: "user_id", referencedColumnName: "id", nullable: false)]
    private User $user;

    #[ORM\Column(type: "string", length: 255)]
    private string $title;

    #[ORM\Column(type: "text", nullable: true)]
    private ?string $description;

    #[ORM\Column(type: "datetime")]
    private \DateTime $start_task;

    #[ORM\Column(type: "datetime")]
    private \DateTime $end_task;

    #[ORM\Column(type: "string", length: 20)]
    private string $status;

    #[ORM\Column(type: "boolean")]
    private bool $notified = false;

    #[ORM\Column(type: "datetime_immutable")]
    private \DateTimeImmutable $created_at;

    #[ORM\Column(type: "datetime_immutable")]
    private \DateTimeImmutable $updated_at;

    public function __construct(
        User      $user,
        string    $title,
        string    $description,
        \DateTime $start_task,
        \DateTime $end_task,
    )
    {
        $this->user = $user;
        $this->title = $title;
        $this->description = $description;
        $this->start_task = $start_task;
        $this->end_task = $end_task;
        $this->status = self::STATUS_PENDING;
        $this->notified = false;
    }

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

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $desc): void
    {
        $this->description = $desc;
    }

    public function getStartTask(): \DateTime
    {
        return $this->start_task;
    }

    public function setStartTask(\DateTime $start_task): void
    {
        $this->start_task = $start_task;
    }

    public function getEndTask(): \DateTime
    {
        return $this->end_task;
    }

    public function setEndTask(\DateTime $end_task): void
    {
        $this->end_task = $end_task;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): void
    {
        if (!in_array($status, [self::STATUS_PENDING, self::STATUS_DONE, self::STATUS_CANCELED])) {
            throw new \InvalidArgumentException("Invalid status");
        }
        $this->status = $status;
    }

    public function isNotified(): bool
    {
        return $this->notified;
    }

    public function markNotified(): void
    {
        $this->notified = true;
    }

    public function markDone(): void
    {
        $this->setStatus(self::STATUS_DONE);
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->created_at;
    }

    public function getUpdatedAt(): \DateTimeImmutable
    {
        return $this->updated_at;
    }

    #[ORM\PrePersist]
    public function onPrePersist(): void
    {
        $now = new \DateTimeImmutable();
        $this->created_at = $now;
        $this->updated_at = $now;
    }

    #[ORM\PreUpdate]
    public function onPreUpdate(): void
    {
        $this->updated_at = new \DateTimeImmutable();
    }
}