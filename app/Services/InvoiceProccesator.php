<?php

namespace App\Services;

use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\Http;

class InvoiceProccesator
{
    /* métodos y/o atributos */


    private $token;
    private $refresh_token;
    private $code;
    private $grant_type;




    public function __construct()
    {
        $this->code = request()->input('code');
        $this->grant_type='authorization_code';
        $this->getToken();

    }

    private function getToken(){

        $response = Http::withBody(json_encode($this->getPayload()), 'application/json')->withOptions(
        ['Content-Type' => 'application/x-www-form-urlencoded',])->post(env('SAGE_ACOOUNT').'/token');

        $parsed_res =$this->parseJSON($response->body());

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

    private function parseJSON($res){
        $res= json_decode(json_encode(json_decode($res)),true);
        return $res;
    }
}



?>
