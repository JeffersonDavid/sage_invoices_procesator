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

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Carbon;
use App\Mail\Notificator;
use Illuminate\Support\Facades\Mail;

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
        logger()->info('********* token ****************');
        logger()->info(json_encode($this->invoiceService->token));
        //1
        $this->invoices = $this->fetchInvoices();
        //2
        $this->processInoviceData();


        $m = Mail::to('jeffersondvid@hotmail.com')->send(new Notificator($this->payload_to_proccess));

        logger()->info(json_encode($m));

        logger()->info('************ proceso finalizado ********* ');
    }


    public function fetchInvoices( $pageNum = 1 ){
        $from_date = $this->invoiceService->params['from_date'];
        $to_date = $this->invoiceService->params['to_date'];
        $sales_invoices = Http::withToken($this->invoiceService->token)
        ->get(env('SAGE_API_V3').'/sales_invoices?from_date='.$from_date.'&to_date='.$to_date.'&items_per_page=200&page='. $pageNum );
        return $this->invoiceService->parseJSON($sales_invoices->body());
    }

    public function processInoviceData(){
        $this->proccesPayloadLog();
        $this->proccessApiPages();
    }

    public function proccesPayloadLog(){
        $totalItems = $this->invoices['$total'];
        $itemsPerPage = $this->invoices['$itemsPerPage'];
        $totalPages = ceil($totalItems / $itemsPerPage);

        $this->payload_to_proccess = [
            'pages_to_process'=> $totalPages,
            'from_date'=> $this->invoiceService->params['from_date'],
            'to_date'=>$this->invoiceService->params['to_date'],
            'total_to_proccess' => $totalItems, //este campo esta mal calculado solo da el total de items por page?
        ];

        ProcessLog::create(['payload' => json_encode($this->payload_to_proccess)]);

    }

    public function proccessApiPages(){

        for ($i=0; $i < $this->payload_to_proccess['pages_to_process']; $i++) {

            $invoices = $this->fetchInvoices($i);

            foreach ($invoices['$items'] as $key => $value) {

                $url = env('SAGE_API_V3') . $value['$path'];

                $invoice_request = Http::withToken($this->invoiceService->token)
                ->get($url);

                $invoice_data =  $this->invoiceService->parseJSON($invoice_request->body());

                $invoice_name = 'invoice-'.$invoice_data['displayed_as'].'.pdf';

                $invoice_local_foler = 'invoices/range-'. $this->payload_to_proccess['from_date'].'-----'. $this->payload_to_proccess['to_date'];

                $this->downloadPdfInvoice($url, $invoice_name , $invoice_local_foler );

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
        logger()->info('--------');

    }



}
