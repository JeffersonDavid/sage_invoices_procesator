<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessInvoices;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use App\Services\InvoiceConnetor;
use Illuminate\Support\Facades\Artisan;

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

    public function process(InvoiceConnetor $invoiceService)
    {

        //logger()->info(json_encode($invoiceService));
        ProcessInvoices::dispatch($invoiceService);
        //Artisan::command('php artisan queue:work --queue=high,default');
        return view('welcome');

        /*
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

        $this->dowloadPDF_file($url,$parsed_res['access_token']);

        die();
       }

        */

    }


    public function dowloadPDF_file($url,$token){
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Accept: application/pdf',
                'Authorization: Bearer ' . $token
            ));


            $pdf = curl_exec($ch);

            $month = date('m');
            $year = date("Y");

            Storage::disk('local')->put('invoices/'.$month.'-'.$year.'/example.pdf', $pdf);

            //for debug only!
            $info = curl_getinfo($ch);
            var_dump($info);

    }
}
