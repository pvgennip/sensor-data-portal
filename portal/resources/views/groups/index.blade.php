@extends('layouts.app')
 
@section('page-title') Group management
@endsection

@section('content')
	<div class="row">
	    <div class="col-lg-12 margin-tb">
	        <div class="pull-right">
	        	@permission('group-create')
	            <a class="btn btn-success" href="{{ route('groups.create') }}"> Create new group</a>
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
			<th width="280px">Action</th>
		</tr>
	@foreach ($groups as $key => $group)
	<tr>
		<td>{{ ++$i }}</td>
		<td>{{ $group->name }}</td>
		<td>{{ $group->type }}</td>
		<td>
			<a class="btn btn-info" href="{{ route('groups.show',$group->id) }}">Show</a>
			@permission('group-edit')
			<a class="btn btn-primary" href="{{ route('groups.edit',$group->id) }}">Edit</a>
			@endpermission
			@permission('group-delete')
			{!! Form::open(['method' => 'DELETE','route' => ['groups.destroy', $group->id],'style'=>'display:inline']) !!}
            {!! Form::submit('Delete', ['class' => 'btn btn-danger']) !!}
        	{!! Form::close() !!}
        	@endpermission
		</td>
	</tr>
	@endforeach
	</table>
	{!! $groups->render() !!}
@endsection