<?php

namespace App\Enums;

enum PaymentStatus: string
{
    case Unpaid        = 'unpaid';
    case Paid          = 'paid';
    case PartiallyPaid = 'partially_paid';
    case Refunded      = 'refunded';
}
