<?php declare(strict_types=1);

namespace App\Domain\Payment;

enum PaymentStatus: string
{
    case Success = 'SUCCESS';
    case Refund = 'REFUND';
    case Cancel = 'CANCEL';
    case Pending = 'PENDING';
}
