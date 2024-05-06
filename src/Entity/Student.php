<?php

namespace App\Entity;

use App\Repository\StudentRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Uid\UuidV6 as uuid;

#[ORM\Table(name: '`student`')]
#[ORM\Entity(repositoryClass: StudentRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_ID', fields: ['id'])]
#[UniqueEntity(fields: ['id'], message: 'There is already an account with this id')]
class Student
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?string $id = null;

    #[ORM\Column]
    private ?string $student_id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $firstname = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $lastname = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $gender = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $birthdate = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $photo_path = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $address = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $confidential_comments = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $card_comments = null;

    #[ORM\Column(length: 128, nullable: true)]
    private ?string $emergency_contact_phone_number = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $emergency_contact_firstname = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $emergency_contact_lastname = null;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    private ?int $emergency_contact_gender = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $emergency_contact_relationship_to_student = null;

    #[ORM\ManyToOne(inversedBy: 'students')]
    #[ORM\JoinColumn(nullable: true)]
    private ?SchoolClass $school_class = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getStudentId(): ?string
    {
        return $this->student_id;
    }

    public function setStudentId(string $student_id): static
    {
        $this->student_id = $student_id;

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(?string $firstname): static
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(?string $lastname): static
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getGender(): ?int
    {
        return $this->gender;
    }

    public function setGender(int $gender): static
    {
        $this->gender = $gender;

        return $this;
    }

    public function getBirthdate(): ?\DateTimeInterface
    {
        return $this->birthdate;
    }

    public function setBirthdate(\DateTimeInterface $birthdate): static
    {
        $this->birthdate = $birthdate;

        return $this;
    }

    public function getPhotoPath(): ?string
    {
        return $this->photo_path;
    }

    public function setPhotoPath(?string $photo_path): static
    {
        $this->photo_path = $photo_path;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): static
    {
        $this->address = $address;

        return $this;
    }

    public function getConfidentialComments(): ?string
    {
        return $this->confidential_comments;
    }

    public function setConfidentialComments(?string $confidential_comments): static
    {
        $this->confidential_comments = $confidential_comments;

        return $this;
    }

    public function getCardComments(): ?string
    {
        return $this->card_comments;
    }

    public function setCardComments(?string $card_comments): static
    {
        $this->card_comments = $card_comments;

        return $this;
    }

    public function getEmergencyContactPhoneNumber(): ?string
    {
        return $this->emergency_contact_phone_number;
    }

    public function setEmergencyContactPhoneNumber(string $emergency_contact_phone_number): static
    {
        $this->emergency_contact_phone_number = $emergency_contact_phone_number;

        return $this;
    }

    public function getEmergencyContactFirstname(): ?string
    {
        return $this->emergency_contact_firstname;
    }

    public function setEmergencyContactFirstname(?string $emergency_contact_firstname): static
    {
        $this->emergency_contact_firstname = $emergency_contact_firstname;

        return $this;
    }

    public function getEmergencyContactLastname(): ?string
    {
        return $this->emergency_contact_lastname;
    }

    public function setEmergencyContactLastname(?string $emergency_contact_lastname): static
    {
        $this->emergency_contact_lastname = $emergency_contact_lastname;

        return $this;
    }

    public function getEmergencyContactGender(): ?int
    {
        return $this->emergency_contact_gender;
    }

    public function setEmergencyContactGender(int $emergency_contact_gender): static
    {
        $this->emergency_contact_gender = $emergency_contact_gender;

        return $this;
    }

    public function getEmergencyContactRelationshipToStudent(): ?string
    {
        return $this->emergency_contact_relationship_to_student;
    }

    public function setEmergencyContactRelationshipToStudent(string $emergency_contact_relationship_to_student): static
    {
        $this->emergency_contact_relationship_to_student = $emergency_contact_relationship_to_student;

        return $this;
    }

    public function getSchoolClass(): ?SchoolClass
    {
        return $this->school_class;
    }

    public function setSchoolClass(?SchoolClass $school_class): static
    {
        $this->school_class = $school_class;

        return $this;
    }

    #[ORM\PrePersist]
    public function setCreatedAtValue(): void
    {
        $this->created_at = new \DateTimeImmutable();
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }
}
