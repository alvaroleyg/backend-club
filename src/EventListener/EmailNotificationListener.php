<?php

namespace App\EventListener;

use App\Event\ClubMembershipEvent;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class EmailNotificationListener
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function onMembershipChange(ClubMembershipEvent $event): void
    {
        $member = $event->getMember();
        $club = $event->getClub();
        $action = $event->getAction();

        $memberType = $member instanceof \App\Entity\Player ? 'Jugador' : 'Entrenador';
        $actionType = str_contains($action, 'ADDED') ? 'agregado' : 'eliminado';

        $email = (new Email())
            ->from('system@clubapp.com')
            ->to('admin@clubapp.com')
            ->subject("Cambio en membresía del club {$club->getName()}")
            ->text("$memberType {$member->getName()} ha sido $actionType del club {$club->getName()}");

        $this->mailer->send($email);
    }
}