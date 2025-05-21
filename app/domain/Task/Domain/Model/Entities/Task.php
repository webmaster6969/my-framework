<?php

namespace App\domain\Task\Domain\Model\Entities;
use App\domain\Auth\Domain\Model\Entities\User;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "tasks")]
#[ORM\Index(name: "idx_task_user_date", columns: ["user_id", "date"])]
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

    #[ORM\Column(type: "date")]
    private \DateTimeInterface $date;

    #[ORM\Column(type: "time", nullable: true)]
    private ?\DateTimeInterface $time;

    #[ORM\Column(type: "string", length: 20)]
    private string $status;

    #[ORM\Column(type: "boolean")]
    private bool $notified = false;

    #[ORM\Column(type: "datetime")]
    private \DateTime $created_at;

    #[ORM\Column(type: "datetime")]
    private \DateTime $updated_at;

    public function __construct(User $user, string $title, \DateTimeInterface $date)
    {
        $this->user = $user;
        $this->title = $title;
        $this->date = $date;
        $this->status = self::STATUS_PENDING;
        $this->notified = false;
        $this->description = null;
        $this->time = null;
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

    public function getDate(): \DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): void
    {
        $this->date = $date;
    }

    public function getTime(): ?\DateTimeInterface
    {
        return $this->time;
    }

    public function setTime(?\DateTimeInterface $time): void
    {
        $this->time = $time;
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

    public function getCreatedAt(): \DateTime
    {
        return $this->created_at;
    }

    public function getUpdatedAt(): \DateTime
    {
        return $this->updated_at;
    }
}