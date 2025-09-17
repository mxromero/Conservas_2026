<?php

namespace App\Services;

use SoapClient;

class SapService
{
    protected $client;
    protected $sapUser;
    protected $sapPassword;

    public function __construct()
    {
        $this->sapUser = env('SAP_USERNAME');
        $this->sapPassword = env('SAP_PASSWORD');
   }

    public function valida_ordenPrev(){

        $wsdl = env('WS_SAP_VALIDA_ORDENES_WSDL');

        $this->client = new SoapClient($wsdl, [
            'login' =>  $this->sapUser,
            'password' => $this->sapPassword,
            'stream_context' => stream_context_create([
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true,
                ]
            ])
        ]);


    }

    public function valida_uma_Vaciado(){

        $wsdl = env('WS_SAP_VAL_UMA_VAIADO_WSDL');

        $this->client = new SoapClient($wsdl, [
            'login' =>  $this->sapUser,
            'password' => $this->sapPassword,
            'stream_context' => stream_context_create([
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true,
                ]
            ])
        ]);
    }

    public function Crea_ClienteSoap($wsdl = ''){

        if (empty($wsdl)) {
            return [
                'success' => false,
                'message' => 'Error al validar WSDL este no existe',
                'data' => null
            ];
        }


        $this->client = new SoapClient($wsdl, [
            'login' =>  $this->sapUser,
            'password' => $this->sapPassword,
            'stream_context' => stream_context_create([
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true,
                ]
            ])
        ]);

    }

    public function obtenerDatos($parametros)
    {
        try {
            $this->valida_ordenPrev();

            $response = $this->client->__soapCall('ZwsPpDatosOrdenprev2', [$parametros]);
            $responseArray = json_decode(json_encode($response), true);

            if (!empty($responseArray['LtMensaje']['Message'])) {
                $message = $responseArray['LtMensaje']['Message'];
                $messageType = $responseArray['LtMensaje']['Type'];

                if ($messageType == 'E') {
                    return [
                        'success' => false,
                        'message' => $message,
                        'data' => null
                    ];
                } elseif ($messageType == 'W') {
                    return [
                        'success' => true,
                        'message' => $message,
                        'data' => $responseArray
                    ];
                }
            }

            return [
                'success' => true,
                'message' => "Operación exitosa.",
                'data' => $responseArray
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Error al conectar con el servicio: revise los datos enviados' . $e->getMessage(),
                'data' => null
            ];
        }

    }


    public function Vaciado_uma($parametros){
        try {

            $this->valida_uma_Vaciado();
            $response = $this->client->__soapCall('ZwsPpEtiquetado0001', [$parametros]);
            $responseArray = json_decode(json_encode($response), true);
            return $responseArray;

        } catch (\Throwable $e) {
            return [
                'success' => false,
                'message' => 'Error al conectar con el servicio: revise los datos enviados' . $e->getMessage(),
                'data' => null
            ];
        }
    }

    /*
    * Valida que material de uma este en la lista de materiales
    * para ser vaciado a linea de proceso
    */
    public function Valida_Material_CS03($parametros){

        try {
            $this->Crea_ClienteSoap(env('WS_SAP_VAL_MATERIAL_WSDL'));
            $response = $this->client->__soapCall('ZppValidaVacnot2', [$parametros]);
            $responseArray = json_decode(json_encode($response), true);
            return $responseArray;

        } catch (\Throwable $e) {
            return [
                'success' => false,
                'message' => 'Error al conectar con el servicio: revise los datos enviados' . $e->getMessage(),
                'data' => null
            ];
        }

    }

    /*
    * Comienza con el proceso de vaciado en SAP
    */

    //Paso 1 Muevo hacia BM05
    public function Vaciado_Mueve_BM05($parametros){

        try {
            $this->Crea_ClienteSoap(env('WS_SAP_GENERA_VACIADO_WSDL'));
            $response = $this->client->__soapCall('ZwsPpEtiquetado0002', [$parametros]);
            $responseArray = json_decode(json_encode($response), true);
            return $responseArray;

        } catch (\Throwable $e) {
            return [
                'success' => false,
                'message' => 'Error al conectar con el servicio: revise los datos enviados' . $e->getMessage(),
                'data' => null
            ];
        }

    }
    //Paso 2 Desasigna UMA
    public function desasignar_uma($parametros){
        try {

            $this->Crea_ClienteSoap(env('WS_SAP_GENERA_VACIADO_WSDL'));
            $response = $this->client->__soapCall('ZwsPpEtiquetado0002', [$parametros]);
            $responseArray = json_decode(json_encode($response), true);
            return $responseArray;

        } catch (\Throwable $e) {
            return [
                'success' => false,
                'message' => 'Error al conectar con el servicio: revise los datos enviados' . $e->getMessage(),
                'data' => null
            ];
        }
    }
    //Paso 3 Cambio Lote
    public function Cambio_Lote($parametros){
        try {
            $this->Crea_ClienteSoap(env('WS_SAP_GENERA_VACIADO_WSDL'));
            $response = $this->client->__soapCall('ZwsPpEtiquetado0002', [$parametros]);
            $responseArray = json_decode(json_encode($response), true);
            return $responseArray;

        } catch (\Throwable $e) {
            return [
                'success' => false,
                'message' => 'Error al conectar con el servicio: revise los datos enviados' . $e->getMessage(),
                'data' => null
            ];
        }
    }
    //Paso 4 Muevo hacia BU05
    public function Vaciado_Mueve_BU05($parametros){
        try {
            $this->Crea_ClienteSoap(env('WS_SAP_GENERA_VACIADO_WSDL'));
            $response = $this->client->__soapCall('ZwsPpEtiquetado0002', [$parametros]);
            $responseArray = json_decode(json_encode($response), true);
            return $responseArray;

        } catch (\Throwable $e) {
            return [
                'success' => false,
                'message' => 'Error al conectar con el servicio: revise los datos enviados' . $e->getMessage(),
                'data' => null
            ];
        }

    }
    //Paso 5 Desebala UMA
    public function Desembalar_uma($parametros){
        try {
            $this->Crea_ClienteSoap(env('WS_SAP_GENERA_VACIADO_WSDL'));
            $response = $this->client->__soapCall('ZwsPpEtiquetado0002', [$parametros]);
            $responseArray = json_decode(json_encode($response), true);
            return $responseArray;

        } catch (\Throwable $e) {
            return [
                'success' => false,
                'message' => 'Error al conectar con el servicio: revise los datos enviados' . $e->getMessage(),
                'data' => null
            ];
        }
    }

    //Funciones Fuera de norma
    public function fueraNorma_ExtraeLote($parametros)
    {
        try {
            $this->Crea_ClienteSoap(env('WS_SAP_FUERA_NORMA_WSDL'));
            $response = $this->client->__soapCall('ZwsPpFueraNorma', [$parametros]);
            $responseArray = json_decode(json_encode($response), true);
            return $responseArray;

        } catch (\Throwable $e) {
            return [
                'success' => false,
                'message' => 'Error al conectar con el servicio: revise los datos enviados' . $e->getMessage(),
                'data' => null
            ];
        }
    }

}
