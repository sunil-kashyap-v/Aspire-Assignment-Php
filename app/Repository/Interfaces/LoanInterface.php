<?php

namespace App\Repository\Interfaces;


interface LoanInterface
{
    public function applyLoan($request);
    public function approveLoan($request);
    public function payEmi($request);
}