<?php 

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

class TestMailController extends AbstractController
{
    #[Route('/test-mail', name: 'test_mail')]
    public function testMail(MailerInterface $mailer): JsonResponse
    {
        $email = (new Email())
            ->from('test@mailtrap.io')
            ->to('test@mailtrap.io')
            ->subject('Test Email')
            ->text('This is a test email from Symfony');

        try {
            $mailer->send($email);
            return $this->json(['status' => 'Email sent successfully']);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], 500);
        }
    }
}