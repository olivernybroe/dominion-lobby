<?php


namespace App\Services\JavaBogAuth;


use App\Models\User;
use function Couchbase\defaultDecoder;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\UnauthorizedException;
use SoapClient;

class JavaBogSoap
{
    /** @var SoapClient */
    protected $soapClient;

    /**
     * JavaBogSoap constructor.
     */
    public function __construct()
    {
        $this->soapClient = new SoapClient("http://javabog.dk:9901/brugeradmin?wsdl");
    }

    public function __call($name, $arguments)
    {
        $args = [];
        foreach ($arguments as $key => $argument) {
            $args["arg$key"] = $argument;
        }

       return $this->soapClient->{$name}($args);
    }

    /**
     * @return SoapClient
     */
    public function getSoapClient(): SoapClient
    {
        return $this->soapClient;
    }

    public function getUser($username, $password)
    {
        $data = $this->__call("hentBruger", [$username, $password])->return;

        return User::firstOrCreate([
            'campusnet_id' => $data->brugernavn
        ], [
            'name' => $data->fornavn . " " . $data->efternavn,
            'email' => $data->email,
            'password' => \Hash::make($data->adgangskode)
        ]);
    }

}