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
			@if (isset($sensors))
			<table class="table table-striped">
				<thead>
					<tr>
						<th>{{ __('crud.id') }}</th>
						<th>{{ __('crud.name') }}</th>
						<th>{{ __('crud.type') }}</th>
						<th>{{ __('crud.key') }}</th>
						<th>{{ __('general.last_data') }}</th>
						<th>{{ __('general.last_value') }}</th>
						<th>{{ __('crud.actions') }}</th>
					</tr>
				</thead>
				<tbody>
					@foreach ($sensors as $key => $sensor)
					<tr>
						<td>{{ $sensor->id }}</td>
						<td>
							@if ($sensor->date != '')
							<a href="{{ route('sensors.showdata',$sensor->id) }}" title="{{ __('crud.show') }}">{{ $sensor->name }}</a>
							@else
							{{ $sensor->name }}
							@endif
							</td>
						<td><label class="label label-default">{{ $sensor->type }}</label></td>
						<td>{{ $sensor->key }}</td>
						<td>{{ $sensor->date }}</td>
						<td>{{ $sensor->value }}</td>
						<td>
							<a class="btn btn-default" href="{{ route('sensors.showdata',$sensor->id) }}" title="{{ __('crud.show') }}" {{ $sensor->date == '' ? 'disabled' : '' }}><i class="fa fa-bar-chart"></i></a>
						</td>
					</tr>
					@endforeach
				</tbody>
			</table>
			@endif
		@endslot
	@endcomponent

	

@endsection