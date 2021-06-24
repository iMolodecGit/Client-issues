<?php

namespace App\Entity;

use App\Repository\IssueRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Mapping\ClassMetadata;

//* @ORM\HasLifecycleCallbacks

/**
 * @ORM\Entity(repositoryClass=IssueRepository::class)
 */
class Issue
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=150)
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=3000)
     */
    private $text;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\IssueClient", inversedBy="clients")
     */
    private $client;

    /**
     * @ORM\Column(type="boolean")
     */
    private $in_work;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        $metadata->addPropertyConstraint('title', new Assert\Regex([
            'pattern' => '/[^\.\,\-\_\'\"\@\?\!\:\$ a-zA-Z0-9А-Яа-я()]/',
            'match' => false,
            'message' => 'Title can contain only letters and numbers',
        ]));
    }

    /**
     * @ORM\Column(type="datetime")
     */
    private $updated_at;

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

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(string $text): self
    {
        $this->text = $text;
        return $this;
    }

    public function getClient(): ?IssueClient
    {
        return $this->client;
    }

    public function setClient(IssueClient $client): self
    {
        $this->client = $client;
        return $this;
    }

    public function getInWork(): ?bool
    {
        return $this->in_work;
    }

    public function setInWork(bool $in_work): self
    {
        $this->in_work = $in_work;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at = null): self
    {
        $this->created_at = $created_at;
        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(\DateTimeInterface $updated_at = null): self
    {
        $this->updated_at = $updated_at;
        return $this;
    }

    public function toArray(): array
    {
        return [
          'id' => $this->getId(),
          'client_id' => $this->getClient()->getId(),
          'title' => $this->getTitle(),
          'text' => $this->getText(),
          'in_work' => $this->getInWork(),
          'created_at' => $this->getCreatedAt(),
          'updated_at' => $this->getUpdatedAt(),
        ];
    }
}
