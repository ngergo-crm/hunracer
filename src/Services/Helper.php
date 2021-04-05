<?php

namespace App\Services;


use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

class Helper
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function sendEmail(String $from, String $to, String $subject, String $email_body, array $context = [])
    {
        $from_name = 'Magyar Kerékpáros Szövetség';
        $email = (new TemplatedEmail());
        $email->from(new Address($from, $from_name))
            ->to($to)
            ->subject($subject)
            //->text($email_body)
            ->htmlTemplate($email_body)
            ->context($context)
        ;
        $this->mailer->send($email);
    }
}
