<?php
namespace App\Repository\Implementations;
use App\Models\User;
use App\Repository\Interfaces\AuthenticationInterface;
use Illuminate\Support\Facades\Auth;

class AuthenticationImplementation implements AuthenticationInterface
{
    /**
     * Class AuthenticationImplementation
     *
     * @package App\Repository\Implementations
     */

    /**
     * @var $userModel
     */
    private $userModel;

    /**
     * AuthenticationImplementation constructor.
     *
     * @param User $userModel
     * @throws \Exception
     */
    public function __construct(User $userModel)
    {
        $this->userModel = $userModel;

    }

    /**
     * login function for customer
     *
     * @param mixed $request
     *
     * @return mixed
     * @throws \Exception
     */

    public function login($request)
    {
        $credentials = request(['email', 'password']);

        $checkCredentials = Auth::attempt(['email' => $request['email'],
                                    'password' => $request['password']]);

        if(!$checkCredentials){
            throw new \Exception('Invalid username or password',401);
        }

        return  [
            'data' => [
                'access_token' => auth()->attempt($credentials),
                'token_type' => 'bearer',
                'expires_in' => auth()->factory()->getTTL() * 60
            ]
        ];
    }
}