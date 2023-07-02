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

    public function process(Request $request)
    {
        $inputs = $request->all();

        if($inputs['type']='year_month'){

            $year = $inputs['year'];
            $month = $inputs['month'];
            // Obtener el primer día del mes
            $inputs['from_date'] = date('Y-m-d', strtotime("{$year}-{$month}-01"));
            // Obtener el último día del mes
            $inputs['to_date'] = date('Y-m-d', strtotime("{$year}-{$month}-" . date('t', strtotime("{$year}-{$month}-01"))));
        }


        $invoiceService = new InvoiceConnetor($inputs['code'], $inputs['from_date'], $inputs['to_date'], $inputs['type']);

        if($invoiceService->valid){
            ProcessInvoices::dispatch($invoiceService);
            return view('welcome',['success'=> 'Facturas enviadas correctamente, recibira un correo electronico cuando el proceso haya terminado']);
        }

        return redirect('http://127.0.0.1:8000?sessionerror=true&from_date='.$inputs['from_date'].'&to_date='.$inputs['to_date'].'&type='.$inputs['type']);

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

    }
}
