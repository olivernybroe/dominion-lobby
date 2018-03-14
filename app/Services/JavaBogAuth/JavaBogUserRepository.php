<?php


namespace App\Services\JavaBogAuth;


use App\Models\User;
use Laravel\Passport\Bridge\UserRepository;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\UserEntityInterface;
use League\OAuth2\Server\Repositories\UserRepositoryInterface;

class JavaBogUserRepository extends UserRepository implements UserRepositoryInterface
{
    public function getUserEntityByUserCredentials($username, $password, $grantType, ClientEntityInterface $clientEntity)
    {
        $user = parent::getUserEntityByUserCredentials($username, $password, $grantType, $clientEntity);

        if(!$user) {
            try{
                $user = \Auth::getUserFromCredentials($username, $password);
            }
            catch (\SoapFault $exception) {
                $user = null;
            }

        }

        return $user;
    }

}