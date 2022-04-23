<?php

namespace App\Http\Controllers;

use App\Http\Requests\ApplyLoanRequest;
use App\Http\Requests\ApproveLoanRequest;
use App\Http\Requests\PayEmiRequest;
use App\Models\Loan;
use App\Repository\Interfaces\LoanInterface;
use App\Transformers\LoanEmiTransformer;
use Dingo\Api\Routing\Helpers;
use Dingo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LoanController extends Controller
{
    use Helpers;

    /**
     * @var $loanInterface
     */
    private $loanInterface;
    private $rejectedLoanStatus;

    /**
     * LoanController constructor.
     *
     * @param LoanInterface $loanInterface
     *
     * @throws \Exception
     */
    public function __construct(LoanInterface $loanInterface,Loan $loanModel)
    {
        $this->loanModel = $loanModel;
        $this->loanInterface = $loanInterface;
        $this->rejectedLoanStatus = $this->loanModel::REJECTED_LOAN_STATUS;
    }

    /**
     * @param ApplyLoanRequest $request
     *
     * @return Dingo\Api\Http\Response
     * @throws \Exception
     */

    public function applyLoan(ApplyLoanRequest $request){
        try {
            DB::beginTransaction();
            $response = $this->loanInterface->applyLoan($request);
            DB::commit();
            return $this->response->array(['message' => 'Loan applied successfully','data' => $response]);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    /**
     * @param ApproveLoanRequest $request
     *
     * @return Dingo\Api\Http\Response
     * @throws \Exception
     */

    public function approveLoan(ApproveLoanRequest $request){
        try {
            DB::beginTransaction();
            $response = $this->loanInterface->approveLoan($request);
            DB::commit();
            if($request['status'] == $this->rejectedLoanStatus){
                return $this->response->array(['message' => 'Loan Rejected Successfully']);
            }
            return $this->response->collection($response,new LoanEmiTransformer())->setMeta(['message' => 'Loan Approved Successfully']);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * @param PayEmiRequest $request
     *
     * @return Dingo\Api\Http\Response
     * @throws \Exception
     */

    public function payEmi(PayEmiRequest $request){
        try {
            DB::beginTransaction();
            $this->loanInterface->payEmi($request);
            DB::commit();
            return $this->response->array(['message' => 'Loan emi paid successfully']);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
