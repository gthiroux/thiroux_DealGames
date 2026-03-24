<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $lastname = null;

    #[ORM\Column(length: 255)]
    private ?string $firstname = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    private ?string $password = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $registered_date = null;

    /**
     * @var Collection<int, Role>
     */
   #[ORM\ManyToMany(targetEntity: Role::class, inversedBy: 'users')]
  private Collection $role;

    /**
     * @var Collection<int, Ad>
     */
    #[ORM\OneToMany(targetEntity: Ad::class, mappedBy: 'author', orphanRemoval: true)]
  private Collection $ads;

    public function __construct()
    {
        $this->role = new ArrayCollection();
        $this->ads = new ArrayCollection();
    }

    
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): static
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): static
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function getRegisteredDate(): ?\DateTime
    {
        return $this->registered_date;
    }

    public function setRegisteredDate(\DateTime $registered_date): static
    {
        $this->registered_date = $registered_date;

        return $this;
    }

    /**
     * @return Collection<int, Role>
    */
    
    public function getRole(): Collection{
      return $this->role;}


public function addRole(Role $role): static
{
    if (!$this->role->contains($role)) {
        $this->role->add($role);
    }

    return $this;
}

public function removeRole(Role $role): static
{
    $this->role->removeElement($role);

    return $this;
}

    /**
     * @return Collection<int, Ad>
     */
        public function getAds(): Collection{
          return $this->ads;}


    public function addAd(Ad $ad): static
    {
        if (!$this->ads->contains($ad)) {
            $this->ads->add($ad);
            $ad->setAuthor($this);
        }

        return $this;
    }

    public function removeAd(Ad $ad): static
    {
        if ($this->ads->removeElement($ad)) {
            // set the owning side to null (unless already changed)
            if ($ad->getAuthor() === $this) {
                $ad->setAuthor(null);
            }
        }

        return $this;
    }

}
