<?php

namespace EtoA\Entity;

use Doctrine\ORM\Mapping as ORM;
use EtoA\Alliance\AlliancePollVotesRepository;

#[ORM\Entity(repositoryClass: AlliancePollVotesRepository::class)]
class AlliancePollVotes
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'vote_id')]
    private ?int $id = null;

    #[ORM\JoinColumn(name: 'vote_alliance_id', referencedColumnName: 'alliance_id')]
    #[ORM\ManyToOne(targetEntity: Alliance::class)]
    private ?Alliance $alliance = null;

    #[ORM\JoinColumn(name: 'vote_user_id', referencedColumnName: 'user_id')]
    #[ORM\ManyToOne(targetEntity: User::class)]
    private ?User $user = null;

    #[ORM\JoinColumn(name: 'vote_poll_id', referencedColumnName: 'poll_id')]
    #[ORM\ManyToOne(targetEntity: AlliancePoll::class)]
    private ?AlliancePoll $poll = null;

    #[ORM\Column(name: 'vote_number')]
    private ?int $number = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumber(): ?int
    {
        return $this->number;
    }

    public function setNumber(int $number): static
    {
        $this->number = $number;

        return $this;
    }

    public function getAlliance(): ?Alliance
    {
        return $this->alliance;
    }

    public function setAlliance(?Alliance $alliance): static
    {
        $this->alliance = $alliance;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getPoll(): ?AlliancePoll
    {
        return $this->poll;
    }

    public function setPoll(?AlliancePoll $poll): static
    {
        $this->poll = $poll;

        return $this;
    }
}
