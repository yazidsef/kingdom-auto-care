<?php

namespace App\Service ;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;

class sendMailService
{
    private $mailer;
    public function __construct(MailerInterface $mailer){
        $this->mailer = $mailer;
    }

    public function send(string $from , string $to , string $subject , string $template , array $context):void
    {
        //on peut utiliser la syntaxe de chainage piur la config de l'email
        $email =( new TemplatedEmail())
        ->from($from)
        ->to($to)
        ->subject($subject)
        ->htmlTemplate("email/$template.html.twig")
        ->context($context);

        //on envoie l'email
        $this->mailer->send($email);
        

    }

}