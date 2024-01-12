<?php

namespace App\Entity;

use App\Repository\TaskRepository;
use App\Validator\CkEditorLengthConstraint;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TaskRepository::class)]
#[UniqueEntity(fields: ['title'], message: 'validator.unique_entity')]
#[ORM\Table('task')]
class Task
{
    #[ORM\Column]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'SEQUENCE')]
    private ?int $id = null;

    #[ORM\Column(type: 'uuid')]
    private ?Uuid $uuid = null;

    #[ORM\Column]
    private \DateTime $createdAt;

    #[Assert\Length(min: 2, max: 255, minMessage: 'validator.task.title.length_min_message', maxMessage: 'validator.task.title.length_max_message')]
    #[Assert\NotBlank(message: 'validator.task.title.not_blank')]
    #[ORM\Column]
    private ?string $title = null;

    #[CkEditorLengthConstraint(
        min: 10,
        max: 6000,
        maxMessageSingular: 'validator.task.content.length_max_message_singular',
        maxMessagePlural: 'validator.task.content.length_max_message_plural',
        minMessageSingular: 'validator.task.content.length_min_message_singular',
        minMessagePlural: 'validator.task.content.length_min_message_plural'
    )]
    #[Assert\NotBlank(message: 'validator.task.content.not_blank')]
    #[ORM\Column(type: 'text')]
    private ?string $content = '';

    #[ORM\Column]
    private bool $isDone = false;

    #[ORM\ManyToOne(inversedBy: 'tasks')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $owner;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Assert\GreaterThan(value: new \DateTime(), message: 'validator.task.deadline.greater_than')]
    private ?\DateTimeInterface $deadLine = null;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUuid(): ?Uuid
    {
        return $this->uuid;
    }

    public function setUuid(?Uuid $uuid): void
    {
        $this->uuid = $uuid;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    public function isDone(): bool
    {
        return $this->isDone;
    }

    public function toggle(bool $flag): void
    {
        $this->isDone = $flag;
    }

    public function setIsDone(bool $isDone): self
    {
        $this->isDone = $isDone;

        return $this;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): static
    {
        $this->owner = $owner;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getDeadLine(): ?\DateTimeInterface
    {
        return $this->deadLine;
    }

    public function setDeadLine(?\DateTimeInterface $deadLine): static
    {
        $this->deadLine = $deadLine;

        return $this;
    }
}
