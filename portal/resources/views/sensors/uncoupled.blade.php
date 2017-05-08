@extends('layouts.app')
 
@section('page-title') {{ __('crud.management', ['item'=>__('general.sensor')]) }}
@endsection

@section('content')

	{{-- uncoupled sensors --}}
	@role('superadmin')
	@if($uncoupled_sensors->count() > 0)
	@component('components/box')
		@slot('title')
			{{ __('crud.overview', ['item'=>__('general.sensors')]) }}
		@endslot

		@slot('action')
			
		@endslot

		@slot('body')
			<table class="table table-striped">
				<tr>
					<th>{{ __('crud.key') }}</th>
					<th>{{ __('crud.type') }}</th>
					<th>{{ __('general.last_date') }}</th>
				</tr>
				@foreach ($uncoupled_sensors as $sensor)
				<tr>
					<td>{{ '' }}</td>
					<td><label class="label label-default">{{ '' }}</label></td>
					<td>{{ '' }}</td>
				</tr>
				@endforeach
			</table>

		@endslot
	@endcomponent
	@endif
	@endrole

@endsection