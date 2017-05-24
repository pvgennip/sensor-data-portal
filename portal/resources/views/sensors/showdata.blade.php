@extends('layouts.app')
 
@section('page-title') {{ __('general.sensordata').': '.$sensor->name }}
@endsection

@section('content')
                
        @component('components/box')
            @slot('title')
                <form id="dateselect" class="form date-form" role="form" method="POST" action="{{ route('sensors.showdata',$sensor->id) }}">        
                    {{ csrf_field() }}
                    
                    <div class="row">
                        <div class="col-sm-3">
                            <p>{{ $sensor->name.' '.__('general.chart')}}</p>
                            <h5>{{' ('.$dataPoints.' '.__('general.datepoints').($dataPoints >= $maxDataPoints ? ' = MAX' : '').')' }}</h5>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group" style="font-size: initial;">
                                <label>{{ __('general.select', ['item'=>__('general.averaging').' '.__('general.resolution')]) }}</label>
                                {!! Form::select('resolution[]', $resolutions, $selectedResolution, array('class' => 'form-control', 'onchange' => 'submit();')) !!}
                            </div>
                        </div>
                        <div class="col-sm-5">
                                <div class="form-group" style="font-size: initial;">
                                    <label>{{ __('general.select', ['item'=>__('general.daterange')] )}}</label>

                                    <div id="reportrange" class="input-group">
                                        <div class="input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                        </div>
                                        <input type="text" class="form-control pull-right" name="daterange" value="{{ $selectedDateRange }}" />
                                    </div>
                                </div>

                                <script type="text/javascript" src="//cdn.jsdelivr.net/jquery/1/jquery.min.js"></script>
                                <script type="text/javascript" src="//cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
                                {{-- <link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/bootstrap/3/css/bootstrap.css" /> --}}
                                <script type="text/javascript" src="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.js"></script>
                                <link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.css" />
                                
                                <script type="text/javascript">
                                    date_format = 'DD-MM-YYYY';

                                    start = moment().subtract(7, 'days');;
                                    end   = moment();

                                    function setStartAndEndFromValue()
                                    {
                                        rangeVal = $('input[name="daterange"]').val();
                                        dateArray = rangeVal.split(' - ');
                                        start = moment(dateArray[0], date_format);
                                        end   = moment(dateArray[1], date_format);
                                        console.log(start, end);
                                        $('#reportrange').daterangepicker(
                                            {
                                                "autoApply": true,
                                                locale: {
                                                  format: date_format
                                                },
                                                startDate: start,
                                                endDate: end,
                                                ranges: {
                                                   'Today': [moment(), moment()],
                                                   'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                                                   'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                                                   'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                                                   'This Month': [moment().startOf('month'), moment().endOf('month')],
                                                   'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                                                }
                                            }, 
                                            cb
                                        );
                                    }

                                    function cb(start, end, init) 
                                    {
                                        $('input[name="daterange"]').val(start.format(date_format) + ' - ' + end.format(date_format));
                                        $('#dateselect').submit();
                                    }

                                    setStartAndEndFromValue();
                                </script>
                        </div>
                    </div>
                </form>
            @endslot

            @slot('action')
                
            @endslot

            @slot('body')
                @if (isset($chartjs))
                <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.bundle.min.js"></script>
                <div style="text-align: center;"><small>{{ __('general.click_legend') }}</small></div>
                <div class="chart" style="padding:10px;" >
                    {!! $chartjs->render() !!}
                </div>
                @endif
            @endslot
        @endcomponent

    @if(isset($error))
        <div class="alert alert-danger">
            <p>{{ $error }}</p>
        </div>
    @endif

@endsection