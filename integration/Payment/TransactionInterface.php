<?php

namespace App\Extensions\Payment;

interface TransactionInterface
{
    const CURRENCY_RUR = 'RUR';

    const STATUS_PENDING = 'pending';
    const STATUS_ERROR = 'error';
    const STATUS_SUCCESS = 'success';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_ABORTED = 'aborted';
    const STATUS_UNKNOWN = 'unknown';

    const TYPE_INCOME = 'income';
    const TYPE_OUTCOME = 'outcome';
    const TYPE_ANOTHER = 'another';
}