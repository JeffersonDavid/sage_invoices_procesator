

@foreach ($dataset as $item)



<div class="card" style="width: 50rem;margin:5px">
    <div class="card-body">
        <h5 class="card-title">{{$item['data']['long_title']}}</h5>

        <p class="card-text">{{$item['data']['desc']}}</p>


        @switch($item['type'])
            @case('range')

            <form method="POST" action="/process?code={{$code}}&country=ES&state=random_string" class="d-flex flex-wrap">
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
            <form method="POST" action="/process?code={{$code}}&country=ES&state=random_string" class="d-flex flex-wrap">
                @csrf
                <div class="d-flex justify-content-center mt-4">
                    <button type="submit" class="btn btn-primary btn-lg" style="height:50px">Procesar y enviar</button>
                    <input type="date" id="from_date" name="from_date" class="d-none" value="{{date('Y-m-01')}}">
                    <input type="date" id="to_date" name="to_date" class="d-none" value="{{date('Y-m-t')}}">
                </div>
            </form>
            @break
            @default

        @endswitch




    </div>
</div>

@endforeach
