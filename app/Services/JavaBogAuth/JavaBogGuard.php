<?php


namespace App\Services\JavaBogAuth;


use App\Models\User;
use Illuminate\Auth\GuardHelpers;
use Illuminate\Auth\SessionGuard;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class JavaBogGuard  extends SessionGuard implements Guard, StatefulGuard
{
    /** @var UserProvider */
    protected $userProvider;

    /** @var Request */
    protected $request;

    /** @var JavaBogSoap */
    protected $soap;

    /**
     * JavaBogGuard constructor.
     * @param UserProvider $userProvider
     * @param Request $request
     */
    public function __construct(UserProvider $userProvider, Request $request)
    {
        parent::__construct("session", $userProvider, app('session.store'), $request);
        $this->userProvider = $userProvider;
        $this->request = $request;
        $this->soap = new JavaBogSoap();
    }

    /**
     * Get the currently authenticated user.
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function user()
    {
        parent::user();

        if (!is_null($this->user)) {
            return $this->user;
        }

        if($this->request->has("username") && $this->request->has("password")) {
            $this->user = $this->getUserFromCredentials(
                $this->request->get("username"),
                $this->request->get("password")
            );
        }

        return $this->user;
    }

    /**
     * Validate a user's credentials.
     *
     * @param  array $credentials
     * @return bool
     */
    public function validate(array $credentials = [])
    {
        return parent::validate($credentials) || $this->attempt($credentials, false);
    }

    /**
     * Attempt to authenticate a user using the given credentials.
     *
     * @param  array $credentials
     * @param  bool $remember
     * @return bool
     */
    public function attempt(array $credentials = [], $remember = false)
    {
        $this->user = $this->getUserFromCredentials(
            $credentials['username'],
            $credentials['password']
        );

        $credentials['campusnet_id'] = $credentials['username'];
        unset($credentials['username']);

        return !is_null($this->user) || parent::attempt($credentials, $remember);
    }

    /**
     * @param $username
     * @param $password
     * @return User|null
     */
    public function getUserFromCredentials($username, $password)
    {
        try{
            return $this->soap->getUser($username, $password);
        }
        catch (\SoapFault $exception) {
            return null;
        }
    }
}