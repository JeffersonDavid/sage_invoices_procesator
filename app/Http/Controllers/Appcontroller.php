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
