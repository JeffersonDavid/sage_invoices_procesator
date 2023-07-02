

@foreach ($dataset as $item)



<div class="card" style="width: 50rem;margin:5px">
    <div class="card-body">
        <h5 class="card-title">{{$item['data']['long_title']}}</h5>

        <p class="card-text">{{$item['data']['desc']}}</p>


        @switch($item['type'])
            @case('range')
            <form id='range' method="POST" action="/process?code={{$code}}&country=ES&state=random_string&type=range" class="d-flex flex-wrap">
                @csrf
                <div class="d-flex">
                    <div class="form-group mr-2">
                        <label for="from_date">Desde</label>
                        <input type="date" id="from_date" name="from_date" class="form-control">
                    </div>
                    <div class="form-group mr-2">
                        <label for="to_date">Hasta</label>
                        <input type="date" id="to_date" name="to_date" class="form-control">
                        &nbsp;
                    </div>
                </div>
                <div class="d-flex justify-content-center mt-4">
                    <button type="submit" class="btn btn-primary btn-lg" style="height:50px">Procesar y enviar</button>
                </div>
            </form>

            @break

            @case('month')
            <form id='month' method="POST" action="/process?code={{$code}}&country=ES&state=random_string&type=month" class="d-flex flex-wrap">
                @csrf
                <div class="d-flex justify-content-center mt-4">
                    <button type="submit" class="btn btn-primary btn-lg" style="height:50px">Procesar y enviar</button>
                    <input type="date" id="from_date" name="from_date" class="d-none" value="{{date('Y-m-01')}}">
                    <input type="date" id="to_date" name="to_date" class="d-none" value="{{date('Y-m-t')}}">
                </div>
            </form>
            @break

            @case('year_month')
            <form id='year_month' method="POST" action="/process?code={{$code}}&country=ES&state=random_string&type=year_month" class="d-flex flex-wrap">
                @csrf
                <div>
                    <select name="month" id="month">
                        @foreach($item['select_moths'] as $numero => $nombre)
                            <option value="{{ $numero }}">{{ $nombre }}</option>
                        @endforeach
                    </select>
                    <select name="year" id="year">
                        @foreach($item['select_years'] as $numero => $nombre)
                        <option value="{{ $numero }}" @if($nombre == date('Y')) selected @endif>{{ $nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <input type="date" id="from_date_x" name="from_date" class="d-none">
                <input type="date" id="to_date_x" name="to_date" class="d-none">
                <button type="submit" class="btn btn-primary btn-lg" style="margin-left:9px">Procesar y enviar</button>
            </form>
            @break
            @default
        @endswitch
    </div>
</div>




@endforeach

<script>
    const form = document.getElementById('year_month');
      // Agregar un evento al formulario cuando se envíe
      form.addEventListener('submit', function(event) {

          event.preventDefault(); // Prevenir el envío del formulario por defecto

          // Obtener los valores seleccionados del mes y el año
          const month = parseInt(document.getElementById('month').value) < 10 ?  '0' + (parseInt(document.getElementById('month').value)) : document.getElementById('month').value;
          const year = '20' + (document.getElementById('year').value);

          // Asignar los valores de fecha al input oculto 'from_date' y 'to_date'
          const fromDate = `${year}-${month}-01`;
          const toDate = `${year}-${month}-31`;

          document.getElementById('from_date_x').value = fromDate;
          document.getElementById('to_date_x').value = toDate;

          // Enviar el formulario programáticamente
          form.submit();
      });

  </script>
