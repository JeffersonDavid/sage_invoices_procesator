<?php

namespace App\Jobs;

use App\Models\Invoice;
use App\Services\InvoiceConnetor;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;


class ProcessInvoices implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public InvoiceConnetor $invoiceService)
    {
        $this->invoiceService = $invoiceService;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        //1
        $invoices = $this->fetchInvoices();
        //2
        $this->processInoviceData($invoices);

    }



    public function savePDFSInfo($url){

       // $sales_invoices = Http::withToken($this->invoiceService->token)
       // ->get(env('SAGE_API_V3').)
    }

    public function fetchInvoices(){
        $from_date = $this->invoiceService->params['from_date'];
        $to_date = $this->invoiceService->params['to_date'];
        $sales_invoices = Http::withToken($this->invoiceService->token)
        ->get(env('SAGE_API_V3').'/sales_invoices?from_date='.$from_date.'&to_date='.$to_date.'&items_per_page=200&page=1');
        return $this->invoiceService->parseJSON($sales_invoices->body());
    }

    public function processInoviceData($invoices){
        $pages_container=[];
        $default_page = 0;
        while ($invoices['$next']){
            $default_page ++;
            array_push($pages_container,$default_page);
        }
    }

    /*
    public function fetchInvoices()
    {
        try {

                $from_date = $this->invoiceService->params['from_date'];
                $to_date = $this->invoiceService->params['to_date'];
                $pages_container=[];
                $page = 0;

                $sales_invoices = Http::withToken($this->invoiceService->token)
                ->get(env('SAGE_API_V3').'/sales_invoices?from_date='.$from_date.'&to_date='.$to_date.'&items_per_page=200&page=1');
                $sales_invoices= $this->invoiceService->parseJSON($sales_invoices->body());

                while ($sales_invoices['$next']){
                    $page ++;
                    array_push($pages_container,$page);
                }

                if(empty($pages_container)){


                    foreach ($sales_invoices['$items'] as $key => $value) {
                        $url=env('SAGE_API_V3').$value['$path'];
                         $this->downloadPdfInvoice($url);
                    }

                 }else{


                    foreach ($sales_invoices['$items'] as $key => $value) {
                        $url=env('SAGE_API_V3').$value['$path'];
                         $this->downloadPdfInvoice($url);
                    }

                 }






        } catch (\Exception $e) {
            //throw $th;
            logger()->info("error durante el batch");
            logger()->info(json_encode($e));
        }
    }
    */

    public function downloadPdfInvoice($url){
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Accept: application/pdf',
            'Authorization: Bearer ' . $this->invoiceService->token
        ));
        $pdf = curl_exec($ch);
        $month = date('m');
        $year = date("Y");
        $id = md5(rand());
        Storage::disk('local')->put('invoices/'.$month.'-'.$year.'/invoice-'.$id.'.pdf', $pdf);
        $info = curl_getinfo($ch);
        logger()->info($info);
    }
}
