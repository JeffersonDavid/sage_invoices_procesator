<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Options extends Component
{
    /**
     * Create a new component instance.
     */

    public $code;
    public $type;
    public $dataset;

    public function __construct(string $code )
    {
        $this->code = $code;

        $this->dataset = [

            0 => [
                'type' => 'range',
                'data' => [
                    'long_title' => 'Envio de facturas (rango)',
                    'desc' => 'Ejecuta un rango de fechas que se descargarán y se enviarán en un zip comprimido'
                ]
                ],

            1 => [
                'type' => 'month',
                'data' => [
                    'long_title' => 'Envio de facturas (mes actual)',
                    'desc' => 'Descarga y envia facturas del mes en curso '. date('M')
                ]
                ],

                2 => [
                    'type' => 'year_month',
                    'data' => [
                        'long_title' => 'Envio de facturas (selección mes completo )',
                        'desc' => 'Descarga y envia facturas de un completo ',

                    ],
                    'select_moths' => [
                                        1 => 'Enero',
                                        2 => 'Febrero',
                                        3 => 'Marzo',
                                        4 => 'Abril',
                                        5 => 'Mayo',
                                        6 => 'Junio',
                                        7 => 'Julio',
                                        8 => 'Agosto',
                                        9 => 'Septiembre',
                                        10 => 'Octubre',
                                        11 => 'Noviembre',
                                        12 => 'Diciembre'
                    ],
                    'select_years'=> range(2000, 2030)
                ]
        ];
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.options');
    }
}
