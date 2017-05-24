@extends('layouts.app')
 
@section('page-title') {{ __('general.dataexport') }}
@endsection

@section('content')

	@if (isset($sensors))
	<form class="form export-form" role="form" method="POST" action="{{ route('sensors.exportdata') }}">		
		{{ csrf_field() }}
		@component('components/box')
			@slot('title')
				{{ __('crud.overview', ['item'=>__('general.sensors')]) }}
			@endslot

			@slot('action')
				<button class="btn btn-primary" type="submit" title="{{ __('crud.export') }}"><i class="fa fa-file-excel-o"></i> Export data from selected sensors</button>
			@endslot

			@slot('body')
					<table class="table table-striped">
						<tr>
							<th>{{ __('crud.id') }}</th>
							<th>{{ __('general.export') }}</th>
							<th>{{ __('crud.name') }}</th>
							<th>{{ __('crud.type') }}</th>
							<th>{{ __('crud.key') }}</th>
							<th>{{ __('general.last_data') }}</th>
							<th>{{ __('general.last_value') }}</th>
						</tr>
						@foreach ($sensors as $key => $sensor)
						<tr>
							<td>{{ $sensor->id }}</td>
							<td>
			                    <label>
			                      <input type="checkbox" name="selected[]" value="{{$sensor->id}}" {{ $sensor->date == '' ? 'disabled readonly' : '' }}>
			                    </label>
							</td>
							<td>{{ $sensor->name }}</td>
							<td><label class="label label-default">{{ $sensor->type }}</label></td>
							<td>{{ $sensor->key }}</td>
							<td>{{ $sensor->date }}</td>
							<td>{{ $sensor->value }}</td>
						</tr>
						@endforeach
					</table>
			@endslot
		@endcomponent
		@endif

		@if (isset($data_sensors))
		@component('components/box')
			@slot('title')
				{{ __('general.dataexport').' '.__('general.sensors') }}
			@endslot

			@slot('action')
				
			@endslot

			@slot('body')
				
					<table class="table table-striped">
						<tr>
							<th>{{ __('crud.id') }}</th>
							<th>{{ __('crud.name') }}</th>
							<th>Link</th>
						</tr>
						@foreach ($data_sensors as $key => $sensor)
						<tr>
							<td>{{ $sensor->id }}</td>
							<td>{{ $sensor->name }}</td>
							<td>
			                    <a href="{{ $sensor->link }}">{{ $sensor->link }}</a>
							</td>
						</tr>
						@endforeach
					</table>
			@endslot
		@endcomponent
		@endif

	</form>

@endsection