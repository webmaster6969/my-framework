<?php

declare(strict_types=1);

namespace App\domain\Task\Domain\Model\Entities;

use App\domain\Auth\Domain\Model\Entities\User;
use App\domain\Common\Domain\Exceptions\EncryptionKeyIsNotFindException;
use Core\Support\Crypt\Crypt;
use Core\Support\Env\Env;
use DateTime;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "tasks")]
#[ORM\Index(name: "idx_task_user_date", columns: ["user_id", "start_task", "end_task"])]
#[ORM\HasLifecycleCallbacks]
class Task
{
    /**
     * @var string
     */
    public const string STATUS_PENDING = 'pending';

    /**
     * @var string
     */
    public const string STATUS_IN_PROGRESS = 'in_progress';

    /**
     * @var string
     */
    public const string STATUS_DONE = 'done';

    /**
     * @var string
     */
    public const string STATUS_CANCELED = 'canceled';

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
    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: "tasks")]
    #[ORM\JoinColumn(name: "user_id", referencedColumnName: "id", nullable: false)]
    private User $user;

    /**
     * @var string
     */
    #[ORM\Column(type: "string", length: 255)]
    private string $title;

    /**
     * @var string|null
     */
    #[ORM\Column(type: "text", nullable: true)]
    private ?string $description;

    /**
     * @var DateTime
     */
    #[ORM\Column(type: "datetime")]
    private DateTime $start_task;

    /**
     * @var DateTime
     */
    #[ORM\Column(type: "datetime")]
    private DateTime $end_task;

    /**
     * @var string
     */
    #[ORM\Column(type: "string", length: 20)]
    private string $status;

    /**
     * @var bool
     */
    #[ORM\Column(type: "boolean")]
    private bool $notified = false;

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
     * @param User $user
     * @param string $title
     * @param string $description
     * @param DateTime $start_task
     * @param DateTime $end_task
     */
    public function __construct(
        User     $user,
        string   $title,
        string   $description,
        string   $status,
        DateTime $start_task,
        DateTime $end_task,
    )
    {
        $encryptionKey = Env::get('ENCRYPTION_KEY');
        if (empty($encryptionKey) || !is_string($encryptionKey)) {
            throw new EncryptionKeyIsNotFindException('ENCRYPTION_KEY environment variable is not set');
        }

        $this->user = $user;
        $this->title = $title;
        $this->description = !empty($description) ? Crypt::encrypt($description, $encryptionKey) : null;
        $this->start_task = $start_task;
        $this->end_task = $end_task;
        $this->status = $status;
        $this->notified = false;
    }

    /**
     * @param int $id
     * @return void
     */
    public function setId(int $id): void
    {
        $this->id = $id;
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
     * @param User $user
     * @return void
     */
    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return void
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        $encryptionKey = Env::get('ENCRYPTION_KEY');
        if (empty($encryptionKey) || !is_string($encryptionKey)) {
            throw new EncryptionKeyIsNotFindException('ENCRYPTION_KEY environment variable is not set');
        }

        return !empty($this->description) ? Crypt::decrypt($this->description, $encryptionKey) : null;
    }

    /**
     * @param string|null $desc
     * @return void
     */
    public function setDescription(?string $desc): void
    {
        $encryptionKey = Env::get('ENCRYPTION_KEY');
        if (empty($encryptionKey) || !is_string($encryptionKey)) {
            throw new EncryptionKeyIsNotFindException('ENCRYPTION_KEY environment variable is not set');
        }

        $this->description = !empty($desc) ? Crypt::encrypt($desc, $encryptionKey) : null;
    }

    /**
     * @return DateTime
     */
    public function getStartTask(): DateTime
    {
        return $this->start_task;
    }

    /**
     * @param DateTime $start_task
     * @return void
     */
    public function setStartTask(DateTime $start_task): void
    {
        $this->start_task = $start_task;
    }

    /**
     * @return DateTime
     */
    public function getEndTask(): DateTime
    {
        return $this->end_task;
    }

    /**
     * @param DateTime $end_task
     * @return void
     */
    public function setEndTask(DateTime $end_task): void
    {
        $this->end_task = $end_task;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @param string $status
     * @return void
     */
    public function setStatus(string $status): void
    {
        if (!in_array($status, [
            self::STATUS_PENDING,
            self::STATUS_IN_PROGRESS,
            self::STATUS_DONE,
            self::STATUS_CANCELED
        ])) {
            throw new \InvalidArgumentException("Invalid status");
        }
        $this->status = $status;
    }

    /**
     * @return bool
     */
    public function isNotified(): bool
    {
        return $this->notified;
    }

    /**
     * @return void
     */
    public function markNotified(): void
    {
        $this->notified = true;
    }

    /**
     * @return void
     */
    public function markDone(): void
    {
        $this->setStatus(self::STATUS_DONE);
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
     * @return string[]
     */
    public static function getAllStatuses(): array
    {
        return [
            self::STATUS_PENDING,
            self::STATUS_IN_PROGRESS,
            self::STATUS_DONE,
            self::STATUS_CANCELED
        ];
    }
}