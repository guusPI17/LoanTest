<?php

declare(strict_types=1);

namespace app\enums;

/**
 * Статус заявки на займ.
 */
enum LoanRequestStatusCode: string
{
    /** Заявка одобрена. */
    case Approved = 'approved';

    /** Заявка отклонена. */
    case Declined = 'declined';
}
