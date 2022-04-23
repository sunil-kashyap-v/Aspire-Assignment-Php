<?php

namespace App\Repository\Implementations;


use App\Models\LoanRepayment;
use App\Models\Loan;
use App\Repository\Interfaces\LoanInterface;
use Carbon\Carbon;

class LoanImplementation implements LoanInterface
{
    /**
     * Class LoanImplementation
     *
     * @package App\Repository\Implementations
     */

    /**
     * @var $loanModel
     * @var $loanRepaymentModel
     */
    protected $loanModel;
    protected $loanRepaymentModel;
    protected $pendingLoanStatus;
    protected $approvedLoanStatus;
    protected $rejectedLoanStatus;

    /**
     * LoanImplementation constructor.
     *
     * @param Loan $loanModel
     * @param LoanRepayment $loanRepaymentModel
     * @throws \Exception
     */
    public function __construct(Loan $loanModel, LoanRepayment $loanRepaymentModel)
    {
        $this->loanModel = $loanModel;
        $this->loanRepaymentModel = $loanRepaymentModel;
        $this->pendingLoanStatus = $this->loanModel::PENDING_LOAN_STATUS;
        $this->approvedLoanStatus = $this->loanModel::APPROVED_LOAN_STATUS;
        $this->rejectedLoanStatus = $this->loanModel::REJECTED_LOAN_STATUS;

    }

    /**
     * applyLoan function for customer
     *
     * @param mixed $request
     *
     * @return mixed
     * @throws \Exception
     */

    public function applyLoan($request)
    {
        $user = auth()->user();
        $userId = $user->id;
        $tenure = $request['tenure'];
        $amount = $request['amount'];

        if (!$user) {
            throw new \Exception('Invalid User', 403);
        }

        $checkLoan = $this->loanModel->where('user_id', $userId)
            ->where('status', $this->pendingLoanStatus)
            ->first();

        if ($checkLoan) {
            throw new \Exception('Loan is already applied and it is in pending state,Please try after sometime', 400);
        }

        $addLoan = new $this->loanModel();
        $addLoan->user_id = $user->id;
        $addLoan->amount = $amount;
        $addLoan->tenure = $tenure;
        $addLoan->pending_emi_tenure = $request['tenure'];
        $addLoan->save();

        return ['loan_id' => $addLoan->id];
    }

    /**
     * approveLoan function for customer
     *
     * @param mixed $request
     *
     * @return mixed
     * @throws \Exception
     */

    public function approveLoan($request)
    {
        $user = auth()->user();
        $userId = $user->id;
        $loanId = $request['loan_id'];
        $requestStatus = $request['status'];

        $checkLoan = $this->loanModel->where('user_id', $userId)
            ->where('id', $loanId)
            ->first();

        if (!$checkLoan) {
            throw new \Exception('Invalid loan id', 400);
        }
        if ($checkLoan['status'] == $this->approvedLoanStatus) {
            throw new \Exception('Requested loan id is already approved', 400);
        }
        if ($checkLoan['status'] == $this->rejectedLoanStatus) {
            throw new \Exception('Requested loan id is rejected', 400);
        }

        $totalAmount = $checkLoan['amount'];
        $totalTenure = $checkLoan['tenure'];

        $emiAmount = $totalAmount / $totalTenure;

        $this->loanModel
            ->where('user_id', $userId)
            ->where('id', $loanId)
            ->update(['status' => $requestStatus]);

        $currentDate = Carbon::now();

        if($requestStatus == $this->approvedLoanStatus){
            for($i=0;$i<$totalTenure;$i++){
                $emiDate = $currentDate->addDays(7);

                $addRepayment = new $this->loanRepaymentModel();
                $addRepayment->loan_id = $loanId;
                $addRepayment->emi_amount = $emiAmount;
                $addRepayment->date = $emiDate->format('Y-m-d');
                $addRepayment->save();
            }

            return $this->loanRepaymentModel->where('loan_id',$loanId)->get();
        }

    }

    /**
     * payEmi function for customer
     *
     * @param mixed $request
     *
     * @return mixed
     * @throws \Exception
     */

    public function payEmi($request)
    {
        $user = auth()->user();
        $userId = $user->id;
        $loanId = $request['loan_id'];
        $requestAmount = $request['amount'];
        $requestDate = $request['date'];

        $checkLoan = $this->loanModel->where('user_id', $userId)
            ->where('id', $loanId)
            ->first();

        if (!$checkLoan) {
            throw new \Exception('Invalid loan id', 400);
        }
        if ($checkLoan['status'] == $this->pendingLoanStatus) {
            throw new \Exception('Requested loan id is in pending state', 400);
        }
        if ($checkLoan['status'] == $this->rejectedLoanStatus) {
            throw new \Exception('Requested loan id is rejected', 400);
        }

        $getLoanRepaymentDetails = $this->loanRepaymentModel->where('loan_id', $loanId)
            ->where('status',0)
            ->first();

        if(!$getLoanRepaymentDetails){
            throw new \Exception('No pending emis present for the requested loan', 400);
        }

        $pendingEmiTenure = $checkLoan['pending_emi_tenure'];

        $emiAmount = $getLoanRepaymentDetails['emi_amount'];
        $emiDate = $getLoanRepaymentDetails['date'];

        if ($emiAmount != $requestAmount) {
            throw new \Exception("You are paying an invalid amount,actual emi amount is $emiAmount and the emi date is on $emiDate", 400);
        }
        if($emiDate != $requestDate){
            throw new \Exception("Wrong emi date,actual emi amount is $emiAmount and the emi date is on $emiDate", 400);
        }
        $this->loanRepaymentModel->where('loan_id', $loanId)
            ->where('date',$requestDate)
            ->where('emi_amount',$requestAmount)
            ->update(['status' => 1
            ]);

        $this->loanModel->where('id', $loanId)
            ->update(['pending_emi_tenure' => $pendingEmiTenure - 1
            ]);
    }

}