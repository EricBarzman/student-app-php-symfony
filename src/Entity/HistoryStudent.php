<?php

namespace App\Entity;

use App\Repository\HistoryStudentRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HistoryStudentRepository::class)]
class HistoryStudent
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $birthdate = null;

    #[ORM\Column(length: 64, nullable: true)]
    private ?string $studentFirstname = null;

    #[ORM\Column(length: 64, nullable: true)]
    private ?string $studentLastname = null;

    #[ORM\Column(length: 8, nullable: true)]
    private ?string $studentGender = null;

    #[ORM\Column(length: 8)]
    private ?string $className = null;

    #[ORM\Column(length: 8, nullable: true)]
    private ?string $promotionYear = null;

    #[ORM\Column(length: 64, nullable: true)]
    private ?string $mainTeacherName = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateTransfer = null;

    public function getId(): ?int
    {
        return $this->id;
    }
    
    public function getDateTransfer(): ?\DateTimeInterface
    {
        return $this->dateTransfer;
    }

    public function setDateTransfer(\DateTimeInterface $dateTransfer): static
    {
        $this->dateTransfer = $dateTransfer;

        return $this;
    }

    public function getStudentFirstname(): ?string
    {
        return $this->studentFirstname;
    }

    public function setStudentFirstname(?string $studentFirstname): static
    {
        $this->studentFirstname = $studentFirstname;

        return $this;
    }

    public function getStudentLastname(): ?string
    {
        return $this->studentLastname;
    }

    public function setStudentLastname(?string $studentLastname): static
    {
        $this->studentLastname = $studentLastname;

        return $this;
    }

    public function getStudentGender(): ?string
    {
        return $this->studentGender;
    }

    public function setStudentGender(?string $studentGender): static
    {
        $this->studentGender = $studentGender;

        return $this;
    }

    public function getBirthdate(): ?\DateTimeInterface
    {
        return $this->birthdate;
    }

    public function setBirthdate(?\DateTimeInterface $birthdate): static
    {
        $this->birthdate = $birthdate;

        return $this;
    }

    public function getClassName(): ?string
    {
        return $this->className;
    }

    public function setClassName(string $className): static
    {
        $this->className = $className;

        return $this;
    }

    public function getPromotionYear(): ?string
    {
        return $this->promotionYear;
    }

    public function setPromotionYear(?string $promotionYear): static
    {
        $this->promotionYear = $promotionYear;

        return $this;
    }

    public function getMainTeacherName(): ?string
    {
        return $this->mainTeacherName;
    }

    public function setMainTeacherName(?string $mainTeacherName): static
    {
        $this->mainTeacherName = $mainTeacherName;

        return $this;
    }
}
