<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    const PENDING_LOAN_STATUS=0;
    const APPROVED_LOAN_STATUS=1;
    const REJECTED_LOAN_STATUS=2;
}
