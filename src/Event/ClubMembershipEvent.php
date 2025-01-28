<?php

namespace App\Event;    

use App\Entity\Club;
use Symfony\Contracts\EventDispatcher\Event;

class ClubMembershipEvent extends Event
{
    public const PLAYER_ADDED = 'club.player_added';
    public const PLAYER_REMOVED = 'club.player_removed';
    public const COACH_ADDED = 'club.coach_added';
    public const COACH_REMOVED = 'club.coach_removed';

    private $club;
    private $member;
    private $action;

    public function __construct(Club $club, $member, string $action)
    {
        $this->club = $club;
        $this->member = $member;
        $this->action = $action;
    }

    public function getClub(): Club { return $this->club; }
    public function getMember() { return $this->member; }
    public function getAction(): string { return $this->action; }
}