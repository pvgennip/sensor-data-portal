@extends('layouts.app')
 
@section('page-title') {{ __('general.sensordata').': '.$item->name }}
@endsection

@section('content')

    @if (isset($chartjs))
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.bundle.min.js"></script>
                
        @component('components/box')
            @slot('title')
                {{ __('general.sensordata').' '.__('general.chart') }}
            @endslot

            @slot('action')
                
            @endslot

            @slot('body')
                <div class="chart" style="padding:10px;" >
                    {!! $chartjs->render() !!}
                </div>
            @endslot
        @endcomponent
    @endif


@endsection