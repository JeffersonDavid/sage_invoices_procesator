<?php

namespace App\Services;

use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class InvoiceConnetor
{
    /* métodos y/o atributos */


    public $token;
    public $refresh_token;
    public $code;
    public $grant_type;
    public $params;
    public $valid;
    public $type;




    public function __construct($code, $from_date, $to_date, $type)
    {

        $this->valid = true;
        $this->code = $code;
        $this->grant_type='authorization_code';
        $this->params = [
            'from_date'=> $from_date,
            'to_date'=> $to_date
        ];


        logger()->info(json_encode($this->params));


        $this->getToken();

        $this->type = $type;
    }

    private function getToken(){

        logger()->info('Intentando obtener el token');
        logger()->info(json_encode($this->getPayload()));

        $response = Http::withBody(json_encode($this->getPayload()), 'application/json')->withOptions(
        ['Content-Type' => 'application/x-www-form-urlencoded',])->post(env('SAGE_ACOOUNT').'/token');
        $parsed_res =$this->parseJSON($response->body());

        if(!isset($parsed_res['access_token'])){
            session(['failed_request' => $this->params]);
            logger()->info(' redirecciona porque el code ha caducado');
            $this->valid = false;
            return $this;

        }

        logger()->info('token obtenido correctamente');
        Session::flush();

        $this->token = $parsed_res['access_token'];
        $this->refresh_token = $parsed_res['refresh_token'];
    }


    private function getPayload(){
        return [
            'client_id' =>env('SAGE_CLIENT_ID'),
            'client_secret' => env('SAGE_CLIENT_SECRET'),
            'code' => $this->code,
            'grant_type' => $this->grant_type,
            'redirect_uri'=>env('SAGE_REDIRECT_URI')
        ];
    }

    public function parseJSON($res){
        $res= json_decode(json_encode(json_decode($res)),true);
        return $res;
    }

    public function refresh_api_token(){
        $payload = [
            'client_id' =>env('SAGE_CLIENT_ID'),
            'client_secret' => env('SAGE_CLIENT_SECRET'),
            'refresh_token' => $this->refresh_token,
            'grant_type' => 'refresh_token',
            'redirect_uri'=>env('SAGE_REDIRECT_URI')
        ];
        $response = Http::withBody(json_encode($payload), 'application/json')->withOptions(
        ['Content-Type' => 'application/x-www-form-urlencoded',])->post(env('SAGE_ACOOUNT').'/token');
        $parsed_res =$this->parseJSON($response->body());
        $this->token = $parsed_res['access_token'];
        $this->refresh_token = $parsed_res['refresh_token'];
    }
}



?>
