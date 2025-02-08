<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\TimeLogRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\PrePersist;
use Doctrine\ORM\Mapping\PreUpdate;
use Doctrine\ORM\Mapping\Table;
use Symfony\Component\Serializer\Attribute\Context;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Serializer\Attribute\Ignore;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;

#[Groups('timelog_details')]
#[Entity(repositoryClass: TimeLogRepository::class), Table('timelog')]
#[HasLifecycleCallbacks]
class TimeLog
{
    #[Groups(['timelog_details', 'user_details'])]
    #[Id, Column(name: 'id', type: 'integer'), GeneratedValue(strategy: 'IDENTITY')]
    private int $id;

    #[Context([DateTimeNormalizer::FORMAT_KEY => 'Y-m-d H:i \G\M\T'])]
    #[Column(type: Types::DATETIME_IMMUTABLE)]
    private DateTimeImmutable $created;

    #[Context([DateTimeNormalizer::FORMAT_KEY => 'Y-m-d H:i \G\M\T'])]
    #[Column(type: Types::DATETIME_IMMUTABLE)]
    private DateTimeImmutable $updated;

    #[Groups(['timelog_details', 'user_details'])]
    #[Context([DateTimeNormalizer::FORMAT_KEY => 'Y-m-d H:i \G\M\T'])]
    #[Column(type: Types::DATETIME_IMMUTABLE)]
    private DateTimeImmutable $start;

    #[Groups(['timelog_details', 'user_details'])]
    #[Context([DateTimeNormalizer::FORMAT_KEY => 'Y-m-d H:i \G\M\T'])]
    #[Column(name: '`end`', type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?DateTimeImmutable $end = null;

    #[ManyToOne(targetEntity: User::class, inversedBy: 'timeLogs')]
    private ?User $user;

    #[Column(type: 'boolean', options: ['default' => 1])]
    private bool $valid = true;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getStart(): DateTimeImmutable
    {
        return $this->start;
    }

    /**
     * @param DateTimeImmutable $start
     */
    public function setStart(DateTimeImmutable $start): void
    {
        $this->start = $start;
    }

    /**
     * @return DateTimeImmutable|null
     */
    public function getEnd(): ?DateTimeImmutable
    {
        return $this->end;
    }

    /**
     * @param DateTimeImmutable|null $end
     */
    public function setEnd(?DateTimeImmutable $end): void
    {
        $this->end = $end;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getCreated(): DateTimeImmutable
    {
        return $this->created;
    }

    /**
     * @param DateTimeImmutable $created
     */
    public function setCreated(DateTimeImmutable $created): void
    {
        $this->created = $created;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getUpdated(): DateTimeImmutable
    {
        return $this->updated;
    }

    /**
     * @param DateTimeImmutable $updated
     */
    public function setUpdated(DateTimeImmutable $updated): void
    {
        $this->updated = $updated;
    }

    #[PrePersist]
    public function prePersist(): void
    {
        $this->created = new DateTimeImmutable();
        $this->updated = new DateTimeImmutable();
    }

    #[PreUpdate]
    public function preUpdate(): void
    {
        $this->updated = new DateTimeImmutable();
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): void
    {
        $this->user = $user;
    }

    public function isValid(): bool
    {
        return $this->valid;
    }

    public function setValid(bool $valid): void
    {
        $this->valid = $valid;
    }
}