<?php

namespace App\Auth;

use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Contracts\Auth\Authenticatable;
use App\Services\LdapService;

class LdapUserProvider implements UserProvider
{

    public $grupos = [];
    public $nombreCompleto;

    public function retrieveByToken($identifier, $token)
    {
        return null; // no usamos remember_token
    }

    public function updateRememberToken(Authenticatable $user, $token)
    {
        // vacío porque no usamos remember_token
    }

    public function retrieveByCredentials(array $credentials)
    {
        session_start();

        $ldap = new LdapService();
        $ldap->setCorreo($credentials['correo']);
        $ldap->setContrasenia($credentials['password']);
        $ldap->IniciarSesion();
        $this->grupos = session('grupos');
        $this->nombreCompleto = session('fullname');

        if ($ldap->estadoSesion) {
            return new LdapUser(
                $ldap->usuario,
                $this->nombreCompleto,
                $credentials['correo'],
                $this->grupos
            );
        }

        return null;
    }

    public function validateCredentials(Authenticatable $user, array $credentials)
    {
        // Si retrieveByCredentials devolvió un user válido, entonces pasó LDAP
        return $user instanceof LdapUser;
    }

    public function retrieveById($identifier)
    {

        $allSession = session()->all();

       // dd($allSession);
        $dominio = "@" . env('DOMINIO_VIGENTE');
        // No tenemos base de datos, retornamos un user fakemu
        return new LdapUser($identifier, $identifier, $identifier . $dominio, $allSession['grupos'] ?? []);
    }
}
