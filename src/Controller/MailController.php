<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

class MailController extends AbstractController
{
    #[Route('/send-mail', name: 'send_mail')]
    public function sendMail(MailerInterface $mailer): Response
    {
        $email = (new Email())
            ->from('noreply@example.com')
            ->to('test@example.com')
            ->subject('¡Hola desde Symfony!')
            ->text('Este es un correo de prueba usando Mailpit.')
            ->html('<p><strong>Correo de prueba usando Mailpit.</strong></p>');

        $mailer->send($email);

        return new Response('Correo enviado correctamente.');
    }
}