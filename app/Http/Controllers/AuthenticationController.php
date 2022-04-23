<?php

namespace App\Http\Controllers;


use App\Http\Requests\LoginRequest;
use App\Repository\Interfaces\AuthenticationInterface;
use Dingo\Api\Routing\Helpers;
use Dingo;
use Illuminate\Http\Request;

class AuthenticationController extends Controller
{
    use Helpers;

    /**
     * @var $authenticationInterface
     */
    private $authenticationInterface;

    /**
     * AuthenticationController constructor.
     *
     * @param AuthenticationInterface $authenticationInterface
     *
     * @throws \Exception
     */
    public function __construct(AuthenticationInterface $authenticationInterface)
    {
        $this->authenticationInterface = $authenticationInterface;
    }

    /**
     * @param LoginRequest $request
     *
     * @return Dingo\Api\Http\Response
     * @throws \Exception
     */
    public function login(LoginRequest $request)
    {
        try {
            $response = $this->authenticationInterface->login($request);
            return $this->response->array($response);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }
}