@extends('layouts.app')
 
@section('page-title') {{ __('general.dataexport') }}
@endsection

@section('content')

	<form class="form export-form" role="form" method="POST" action="{{ route('sensors.exportdata') }}">		

	@component('components/box')
		@slot('title')
			{{ __('crud.overview', ['item'=>__('general.sensors')]) }}
		@endslot

		@slot('action')
			<button class="btn btn-primary" type="submit" title="{{ __('crud.export') }}"><i class="fa fa-file-excel-o"></i> Export data from selected sensors</button>
		@endslot

		@slot('body')
			
	            {{ csrf_field() }}
				<table class="table table-striped">
					<tr>
						<th>{{ __('crud.select_multi', ['item'=>__('general.sensors')]) }}</th>
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
						<td>
		                    <label>
		                      <input type="checkbox" name="selected[]" value="{{$sensor->id}}" {{ $sensor->date == '' ? 'disabled readonly' : 'checked=checked' }}>
		                    </label>
						</td>
						<td>{{ $sensor->id }}</td>
						<td>{{ $sensor->name }}</td>
						<td><label class="label label-default">{{ $sensor->type }}</label></td>
						<td>{{ $sensor->key }}</td>
						<td>{{ $sensor->date }}</td>
						<td>{{ $sensor->value }}</td>
						<td>
							
						</td>
					</tr>
					@endforeach
				</table>
				{!! $sensors->render() !!}
		@endslot
	@endcomponent

	</form>

@endsection