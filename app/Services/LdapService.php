<?php

namespace App\Services;

use Illuminate\Auth\Authenticatable;

class LdapService
{
    public $usuario;
    public $grupos = [];
    public $estadoSesion = false;
    public $nombreCompleto;

    public $cargo;

    private $correo;
    private $contrasenia;

    public $ldapData = [];

    public function setCorreo(string $correo): void
    {
        $this->correo = $correo;
        [$usuario] = explode('@', $this->correo);
        $this->usuario = $usuario;
    }

    public function setContrasenia(string $contrasenia): void
    {
        $this->contrasenia = $contrasenia;
    }

    public function setNombreCompleto(string $nombreCompleto): void
    {
        $this->nombreCompleto = $nombreCompleto;
    }

    public function getNombreCompleto(): ?string
    {
        return $this->nombreCompleto;
    }

    public function getGroups(): array
    {
        return $this->grupos;
    }

    public function iniciarSesion(): bool
    {
        $ldapHost = env('LDAP_HOST');
        $ldapPort = env('LDAP_PORT', 389);
        $ldapDomain = env('LDAP_DOMAIN');

        $ldapUsuario  = $ldapDomain . '\\' . $this->usuario;
        $ldapContrasenia = $this->contrasenia;

        $conexion = ldap_connect($ldapHost, $ldapPort);
        ldap_set_option($conexion, LDAP_OPT_PROTOCOL_VERSION, 3);

        if (!$conexion) {
            return false;
        }

        $ldapBind = @ldap_bind($conexion, $ldapUsuario, $ldapContrasenia);

        if ($ldapBind) {

            $this->estadoSesion = true;

            $baseDn = env('LDAP_BASE_DN');
            $filtro = sprintf(env('LDAP_FILTER'), $this->usuario);

            $resultado = ldap_search($conexion, $baseDn, $filtro);
            $entry = ldap_get_entries($conexion, $resultado);

            $this->setNombreCompleto($entry[0]['displayname'][0]); //Nombre Completo
            $this->cargo = $entry[0]['title'][0] ?? null; //Cargo



            $miembroDe = $entry[0]['memberof'] ?? [];
            unset($miembroDe['count']);

            $extractCn = function ($item) {
                preg_match('/CN=([^,]+)/', $item, $matches);
                return $matches[1] ?? null;
            };


            // Solo grupos que empiezan con 'MA_'
            $this->grupos = array_filter(array_map($extractCn, $miembroDe), function ($valor) {
                return strpos($valor, 'UP_') === 0;
            });


            if (empty($this->grupos)) {
                // No pertenece → no permitir login
                $this->estadoSesion = false;
                return false;
            }

            $this->ldapData = $this->getGroups();

            session(['fullname' => $this->nombreCompleto]);
            session(['grupos' => $this->grupos]);

            return true;
        }


        $this->estadoSesion = false;
        return false;
    }
}
