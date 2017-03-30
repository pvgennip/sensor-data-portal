@extends('layouts.app')
 
@section('page-title') Sensor management
@endsection

@section('content')


	@component('components/box')
		@slot('title')
			Sensor overview
		@endslot

		@slot('action')
			@permission('sensor-create')
	            <a class="btn btn-primary" href="{{ route('sensors.create') }}"><i class="fa fa-plus"></i> Add new sensor</a>
	            @endpermission
		@endslot

		@slot('body')
			<table class="table table-striped">
				<tr>
					<th>ID</th>
					<th>Name</th>
					<th>Type</th>
					<th>Key</th>
					<th width="280px">Action</th>
				</tr>
			@foreach ($sensors as $key => $sensor)
			<tr>
				<td>{{ $sensor->id }}</td>
				<td>{{ $sensor->name }}</td>
				<td>{{ $sensor->type }}</td>
				<td>{{ $sensor->key }}</td>
				<td>
					<a class="btn btn-default" href="{{ route('sensors.show',$sensor->id) }}" title="Show"><i class="fa fa-eye"></i></a>
					@permission('sensor-edit')
					<a class="btn btn-primary" href="{{ route('sensors.edit',$sensor->id) }}" title="Edit"><i class="fa fa-pencil"></i></a>
					@endpermission
					@permission('sensor-delete')
					{!! Form::open(['method' => 'DELETE','route' => ['sensors.destroy', $sensor->id],'style'=>'display:inline', 'onsubmit'=>'return confirm("Are you sure you want to delete sensor '.$sensor->name.'?")']) !!}
		            {!! Form::button('<i class="fa fa-trash-o"></i>', ['type'=>'submit', 'class' => 'btn btn-danger pull-right']) !!}
		        	{!! Form::close() !!}
		        	@endpermission
				</td>
			</tr>
			@endforeach
			</table>
			{!! $sensors->render() !!}
		@endslot
	@endcomponent
@endsection