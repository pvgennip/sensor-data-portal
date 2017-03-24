@extends('layouts.app')
 
@section('page-title') Sensor management
@endsection

@section('content')
	<div class="row">
	    <div class="col-lg-12 margin-tb">
	        <div class="pull-right">
	        	@permission('sensor-create')
	            <a class="btn btn-success" href="{{ route('sensors.create') }}"> Create new sensor</a>
	            @endpermission
	        </div>
	    </div>
	</div>
	@if ($message = Session::get('success'))
		<div class="alert alert-success">
			<p>{{ $message }}</p>
		</div>
	@endif
	<table class="table table-bordered">
		<tr>
			<th>No</th>
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
			<a class="btn btn-info" href="{{ route('sensors.show',$sensor->id) }}">Show</a>
			@permission('sensor-edit')
			<a class="btn btn-primary" href="{{ route('sensors.edit',$sensor->id) }}">Edit</a>
			@endpermission
			@permission('sensor-delete')
			{!! Form::open(['method' => 'DELETE','route' => ['sensors.destroy', $sensor->id],'style'=>'display:inline']) !!}
            {!! Form::submit('Delete', ['class' => 'btn btn-danger']) !!}
        	{!! Form::close() !!}
        	@endpermission
		</td>
	</tr>
	@endforeach
	</table>
	{!! $sensors->render() !!}
@endsection