<?php

namespace App\Entity;

use App\Repository\PageRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PageRepository::class)]
#[Gedmo\SoftDeleteable(fieldName: "deletedAt")]
class Page
{
    use TimestampableTrait;
    use SoftDeleteableEntity;

    const STATUS_ACTIVE = 'active';
    const STATUS_MODERATION = 'moderation';
    const STATUS_HIDDEN = 'hidden';
    const STATUS_REMOVED = 'removed';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['user'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['user'])]
    #[Assert\NotBlank]
    protected ?string $title = null;

    #[ORM\ManyToOne(inversedBy: 'pages')]
    #[Groups(['user'])]
    #[Assert\NotBlank]
    private ?Author $author = null;

    #[ORM\ManyToOne(inversedBy: 'pages')]
    private ?Category $category = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['user'])]
    #[Assert\NotBlank]
    private ?string $body = null;


    #[ORM\Column(length: 255)]
    #[Groups(['user'])]
    #[Assert\Choice(["active","moderation", "removed", "hidden"])]
    private ?string $status = null;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }


    public function getAuthor(): ?Author
    {
        return $this->author;
    }

    public function setAuthor(?Author $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getBody(): ?string
    {
        return $this->body;
    }

    public function setBody(?string $body): self
    {
        $this->body = $body;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;
        if ($status != 'removed') {
            $this->setDeletedAt(null);
        }

        return $this;
    }
}
