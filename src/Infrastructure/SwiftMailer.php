<?php declare(strict_types=1);

namespace App\Infrastructure;

use App\SharedKernel\MailerInterface;
use Psr\Log\LoggerInterface;
use SplFileObject;
use Swift_Attachment;
use Swift_Mailer;
use Swift_Message;

final class SwiftMailer implements MailerInterface
{
    /**
     * @var int
     */
    private const SWIFT_MAILER_ERROR_CODE = 0;

    /**
     * @var array<string>
     */
    private static array $from = [
        'noreply@post.technopark.ru' => 'ТЕХНОПАРК Интернет магазин',
    ];

    private function __construct(
        private readonly LoggerInterface $logger,
        private readonly Swift_Mailer $mailer,
    ) {
    }

    /**
     * @inheritDoc
     */
    public function send(string $subject, string $description, ?SplFileObject $attachment, array $recipients): void
    {
        $message = new Swift_Message();
        $message->setFrom(self::$from);
        $message->setSubject($subject);
        $message->setDescription($description);
        $message->setTo($recipients);

        if ($attachment !== null) {
            $entity = Swift_Attachment::fromPath(
                $attachment->getPath(),
                $attachment->getType()
            );
            $entity->setFilename($attachment->getFilename());

            $message->attach($entity);
        }

        $failedRecipients = [];

        if ($this->mailer->send($message, $failedRecipients) !== self::SWIFT_MAILER_ERROR_CODE) {
            return;
        }

        $this->logger->error('Произошла ошибка при отправке письма.', [
            self::class,
            __METHOD__,
            $failedRecipients,
        ]);
    }
}
