@extends('layouts.app')
 
@section('page-title') {{ __('crud.management', ['item'=>__('general.sensordata')]) }}
@endsection

@section('content')

			
	@component('components/box')
		@slot('title')
			{{ __('crud.overview', ['item'=>__('general.sensors')]) }}
		@endslot

		@slot('action')
			
		@endslot

		@slot('body')
			<table class="table table-striped">
				<tr>
					<th>{{ __('crud.id') }}</th>
					<th>{{ __('crud.name') }}</th>
					<th>{{ __('crud.type') }}</th>
					<th>{{ __('crud.key') }}</th>
					<th>{{ __('general.last_date') }}</th>
					<th>{{ __('general.last_value') }}</th>
					<th>{{ __('crud.actions') }}</th>
				</tr>
				@foreach ($sensors as $key => $sensor)
				<tr>
					<td>{{ $sensor->id }}</td>
					<td>{{ $sensor->name }}</td>
					<td><label class="label label-default">{{ $sensor->type }}</label></td>
					<td>{{ $sensor->key }}</td>
					<td>{{ $sensor->date }}</td>
					<td>{{ $sensor->value }}</td>
					<td>
						<a class="btn btn-default" href="{{ route('sensors.showdata',$sensor->id) }}" title="{{ __('crud.show') }}"><i class="fa fa-bar-chart"></i></a>
					</td>
				</tr>
				@endforeach
			</table>
			{!! $sensors->render() !!}
		@endslot
	@endcomponent

	

@endsection