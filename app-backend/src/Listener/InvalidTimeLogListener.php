<?php

declare(strict_types=1);

namespace App\Listener;

use App\Entity\TimeLog;
use App\Event\InvalidTimeLogFound;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Mime\Address;

#[AsMessageHandler]
class InvalidTimeLogListener
{
    public function __construct(
        private readonly MailerInterface $mailer,
        private readonly EntityManagerInterface $entityManager,
        #[Autowire(env: 'DEBUG_EMAIL_FROM')]
        private readonly string $emailFrom,
        #[Autowire(env: 'DEBUG_EMAIL_TO')]
        private readonly string $emailTo,
    ) {
    }

    public function __invoke(InvalidTimeLogFound $event)
    {
        $timeLog = $this->entityManager
            ->getRepository(TimeLog::class)
            ->find($event->getTimeLogId());
        $email = (new TemplatedEmail())
            ->from($this->emailFrom)
            ->to(new Address($this->emailTo))
            ->subject('Invalid Time Log Found')

            // path of the Twig template to render
            ->htmlTemplate('email/invalid-log.html.twig')

            // change locale used in the template, e.g. to match user's locale
            ->locale('de')

            // pass variables (name => value) to the template
            ->context([
                'timeLog' => $timeLog,
            ]);

        $this->mailer->send($email);
    }
}