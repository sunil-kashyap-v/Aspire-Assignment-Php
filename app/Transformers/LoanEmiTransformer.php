<?php
namespace App\Transformers;
use App\Models\LoanRepayment;
use League\Fractal\TransformerAbstract;

class LoanEmiTransformer extends TransformerAbstract
{
    public function transform(LoanRepayment $model)
    {
        return[
            'emi_date' => $model->date,
            'emi_amount' => $model->emi_amount
        ];
    }
}