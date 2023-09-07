<?php

namespace App\Entity;

use App\Repository\AssetRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: AssetRepository::class)]
class Asset
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $asset_id;

    #[ORM\Column(length: 255)]
    private $label;

    #[ORM\Column]
    private $user_id;

    #[ORM\Column]
    private $currency_id;

    #[ORM\Column]
    private $value;

    public function getId(): ?int
    {
        return $this->asset_id;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function getCurrency(): ?int
    {
        return $this->currency_id;
    }

    public function setCurrency(int $currency_id): self
    {
        $this->currency_id = $currency_id;

        return $this;
    }

    public function getUserId(): ?int
    {
        return $this->user_id;
    }

    public function setUserId(int $user_id): self
    {
        $this->user_id = $user_id;

        return $this;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function setValue($value): self
    {
        $this->value = $value;

        return $this;
    }
}
