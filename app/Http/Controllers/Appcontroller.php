<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class Appcontroller extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        //
    }

    public function index(Request $request)
    {
        //
        $input = $request->all();
        $code = isset($input['code']) ? $input['code'] : null;
        return view('welcome', ['code' => $code]);
    }

    public function process(Request $request)
    {

        $code = $request->input('code');
        $data = [
            'client_id' =>env('SAGE_CLIENT_ID'),
            'client_secret' => env('SAGE_CLIENT_SECRET'),
            'code' => $code,
            'grant_type' => 'authorization_code',
            'redirect_uri'=>env('SAGE_REDIRECT_URI')
        ];

        $response = Http::withBody(json_encode($data), 'application/json')
        ->withOptions([
            'Content-Type' => 'application/x-www-form-urlencoded',
        ])->post(env('SAGE_ACOOUNT').'/token');

        $parsed_res =$this->parseJSON($response->body());

        $sales_invoices = Http::withToken($parsed_res['access_token'])
        ->get(env('SAGE_API_V3').'/sales_invoices');

        $sales_invoices = $this->parseJSON($sales_invoices->body());

       foreach ($sales_invoices['$items'] as $key => $value) {

        $pdf_path=$value['$path'];
        $url = env('SAGE_API_V3').$pdf_path;
        var_dump($url);

       }

        die();

    }

    public function parseJSON($res){
        $res= json_decode(json_encode(json_decode($res)),true);
        return $res;
    }
}
