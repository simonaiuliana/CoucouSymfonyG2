<?php

namespace App\Entity;

use App\Repository\CommentRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CommentRepository::class)]
class Comment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(
        options:
            [
                'unsigned' => true,
            ]
    )]
    private ?int $id = null;

    #[ORM\Column(length: 2500)]
    private ?string $commentMessage = null;

    #[ORM\Column(
        type: Types::DATETIME_MUTABLE,
        options: [
            'default' => 'CURRENT_TIMESTAMP',
        ]
    )]
    private ?\DateTimeInterface $commentDateCreated = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCommentMessage(): ?string
    {
        return $this->commentMessage;
    }

    public function setCommentMessage(string $commentMessage): static
    {
        $this->commentMessage = $commentMessage;

        return $this;
    }

    public function getCommentDateCreated(): ?\DateTimeInterface
    {
        return $this->commentDateCreated;
    }

    public function setCommentDateCreated(\DateTimeInterface $commentDateCreated): static
    {
        $this->commentDateCreated = $commentDateCreated;

        return $this;
    }
}
