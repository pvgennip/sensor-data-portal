@extends('layouts.app')

@section('page-title') Role management
@endsection

@section('content')


	@component('components/box')
		@slot('title')
			Role overview
		@endslot
		@slot('action')
			@permission('role-create')
	            <a class="btn btn-success btn-xs" href="{{ route('roles.create') }}"><i class="fa fa-plus"></i> Add new role</a>
	        @endpermission
		@endslot

		@slot('body')
		<table class="table table-striped">
			<tr>
				<th>No</th>
				<th>Name</th>
				<th>Description</th>
				<th width="280px">Action</th>
			</tr>
		@foreach ($roles as $key => $role)
		<tr>
			<td>{{ ++$i }}</td>
			<td>{{ $role->display_name }}</td>
			<td>{{ $role->description }}</td>
			<td>
				<a class="btn btn-info btn-xs" href="{{ route('roles.show',$role->id) }}" title="Show"><i class="fa fa-eye"></i></a>
					@permission('role-edit')
					<a class="btn btn-primary btn-xs" href="{{ route('roles.edit',$role->id) }}" title="Edit"><i class="fa fa-pencil"></i></a>
					@endpermission
					@permission('role-delete')
					{!! Form::open(['method' => 'DELETE','route' => ['roles.destroy', $role->id],'style'=>'display:inline', 'onsubmit'=>'return confirm("Are you sure you want to delete role '.$role->display_name.'?")']) !!}
		            {!! Form::button('<i class="fa fa-trash-o"></i>', ['type'=>'submit', 'class' => 'btn btn-danger btn-xs pull-right']) !!}
		        	{!! Form::close() !!}
		        	@endpermission
			</td>
		</tr>
		@endforeach
		</table>
		{!! $roles->render() !!}
		@endslot

	@endcomponent
@endsection