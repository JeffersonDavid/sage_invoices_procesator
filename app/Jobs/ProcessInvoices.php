<?php

namespace App\Jobs;

use App\Models\Invoice;
use App\Models\ProcessLog;
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

use Illuminate\Support\Carbon;


class ProcessInvoices implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public $invoices;
    public $payload_to_proccess;

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
        $this->invoices = $this->fetchInvoices();
        //2
        $this->processInoviceData();
    }


    public function fetchInvoices(){
        $from_date = $this->invoiceService->params['from_date'];
        $to_date = $this->invoiceService->params['to_date'];
        $sales_invoices = Http::withToken($this->invoiceService->token)
        ->get(env('SAGE_API_V3').'/sales_invoices?from_date='.$from_date.'&to_date='.$to_date.'&items_per_page=200&page=1');

        return $this->invoiceService->parseJSON($sales_invoices->body());
    }

    public function processInoviceData(){
        $this->proccesPayloadLog();
        $this->proccessData_for_each_invoice();
    }

    public function proccesPayloadLog(){
        $pages_container=[];
        $default_page = 0;
        while ($this->invoices['$next']){
            $default_page ++;
            array_push($pages_container,$default_page);
        }
        $this->payload_to_proccess = [
            'pages_to_process'=>count($pages_container),
            'from_date'=> $this->invoiceService->params['from_date'],
            'to_date'=>$this->invoiceService->params['to_date'],
            'total_to_proccess' => $this->invoices['$total'], //este campo esta mal calculado solo da el total de items por page?
        ];
        ProcessLog::create(['payload' => json_encode($this->payload_to_proccess)]);
    }

    public function proccessData_for_each_invoice(){

        if($this->payload_to_proccess['pages_to_process']<=0){
            $invoices = $this->fetchInvoices();
            foreach ($invoices['$items'] as $key => $value) {
                $url=env('SAGE_API_V3').$value['$path'];
                $invoice_request = Http::withToken($this->invoiceService->token)
                ->get($url);
                $invoice_data =  $this->invoiceService->parseJSON($invoice_request->body());
                $invoice_name = 'invoice-'.$invoice_data['displayed_as'].'.pdf';
                $month = date('m');
                $year = date("Y");
                $invoice_local_foler = 'invoices/'.$month.'-'.$year;
                $this->downloadPdfInvoice($url,$invoice_name,$invoice_local_foler);
                Invoice::create([
                        'invoice_number'=>$invoice_data['displayed_as'],
                        'invoice_name'=>$invoice_name,
                        'invoice_api_name'=>$invoice_data['id'],
                        'invoice_download_url'=>$url,
                        'invoice_date'=>$invoice_data['date'],
                        'invoice_local_path'=> $invoice_local_foler,
                        'send_status'=>0,
                        'token_call'=>$this->invoiceService->token,
                        'refresh_token_call'=>$this->invoiceService->refresh_token,
                        'shipment_date'=>Carbon::now()->toDateString()
                    ]);
            }
        }
    }

    public function downloadPdfInvoice($url,$invoice_name,$invoice_local_foler){
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Accept: application/pdf',
            'Authorization: Bearer ' . $this->invoiceService->token
        ));
        $pdf = curl_exec($ch);
        Storage::disk('local')->put($invoice_local_foler.'/'.$invoice_name, $pdf);
        $info = curl_getinfo($ch);
        logger()->info($info);
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


}
