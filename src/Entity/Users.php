<?php

namespace App\Entity;

use App\Entity\Trait\CreatedAtTrait;
use App\Repository\UsersRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use function PHPUnit\Framework\isTrue;

#[ORM\Entity(repositoryClass: UsersRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[UniqueEntity(fields: ['email'], message: 'Cette adresse email est déja utilisée')]
class Users implements UserInterface, PasswordAuthenticatedUserInterface
{
    use CreatedAtTrait; 
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\NotBlank(message:'l\'adresse email ne peut pas etre vide')]
    #[Assert\Email(message:'l\'adresse email n\'est pas valide')]
    #[ORM\Column(length: 180)]
    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[Assert\NotBlank(message:'le mot de passe ne peut pas etre vide')]
    #[Assert\Length(min:8,minMessage:'le mot de passe doit contenir au moins {{ limit }} caracteres')]
    #[ORM\Column]
    private ?string $password = null;

    #[Assert\NotBlank(message:'le nom ne peut pas etre vide')]
    #[Assert\Length(min:4 ,minMessage:'le nom doit contenir au moins {{ limit }} caracteres ' , max: 50 , maxMessage:'le nom doit contenir au moins {{ limit }} caracteres')]
    #[ORM\Column(length: 100)]
    private ?string $lastname = null;

    #[Assert\NotBlank(message:'le prenom ne peut pas etre vide')]
    #[Assert\Length(min:4 ,minMessage:'le prenom doit contenir au moins {{ limit }} caracteres ' , max: 50 , maxMessage:'le prenom doit contenir au moins {{ limit }} caracteres')]
    #[ORM\Column(length: 100)]
    private ?string $firstname = null;

    #[Assert\NotBlank(message:'l\'adresse ne peut pas etre vide')]
    #[Assert\Length(min:15 ,minMessage:'l\'adresse  doit contenir au moins {{ limit }} caracteres ' , max: 50 , maxMessage:'le nom doit contenir au moins {{ limit }} caracteres')]
    #[ORM\Column(length: 255)]
    private ?string $address = null;

    #[Assert\NotBlank(message:'le code postal ne peut pas etre vide')]
    #[ORM\Column(length: 5)]
    private ?string $zipcode = null;

    #[Assert\NotBlank(message:'le nom de ville ne peut pas etre vide')]
    #[ORM\Column(length: 150)]
    private ?string $city = null;

    #[ORM\Column(type:'boolean')]
    private ?bool $is_verified = false;

    #[ORM\Column(type:'string' , length:100)]
    private $resetToken; 
   
    /**
     * @var Collection<int, Orders>
     */
    #[ORM\OneToMany(targetEntity: Orders::class, mappedBy: 'Users')]
    private Collection $orders;

    public function __construct()
    {
        $this->orders = new ArrayCollection();
        $this->created_at= new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     *
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
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

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): static
    {
        $this->address = $address;

        return $this;
    }

    public function getZipcode(): ?string
    {
        return $this->zipcode;
    }

    public function setZipcode(string $zipcode): static
    {
        $this->zipcode = $zipcode;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): static
    {
        $this->city = $city;

        return $this;
    }

  

    /**
     * @return Collection<int, Orders>
     */
    public function getOrders(): Collection
    {
        return $this->orders;
    }

    public function addOrder(Orders $order): static
    {
        if (!$this->orders->contains($order)) {
            $this->orders->add($order);
            $order->setUsers($this);
        }

        return $this;
    }

    public function removeOrder(Orders $order): static
    {
        if ($this->orders->removeElement($order)) {
            // set the owning side to null (unless already changed)
            if ($order->getUsers() === $this) {
                $order->setUsers(null);
            }
        }

        return $this;
    }
    
    public function isIsVerified(): ?bool
    {
        return $this->is_verified;
    }

    public function setIsVerified(?bool $is_verified): self
    {
        $this->is_verified = $is_verified;

        return $this;
    }

    public function getResetToken()
    {
        return $this->resetToken;
    }

    public function setResetToken($resetToken): self
    {
        $this->resetToken = $resetToken;

        return $this;
    }
}
