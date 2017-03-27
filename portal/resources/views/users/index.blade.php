@extends('layouts.app')

@section('page-title') User management
@endsection

@section('content')


	@component('components/box')
		@slot('title')
			User overview
		@endslot

		@slot('action')
			@permission('user-create')
	            <a class="btn btn-success btn-xs" href="{{ route('users.create') }}"><i class="fa fa-plus"></i> Add new user</a>
	        @endpermission
		@endslot

		@slot('body')
			<table class="table table-striped">
				<tr>
					<th>No</th>
					<th>Name</th>
					<th>Email</th>
					<th>Roles</th>
					<th width="280px">Action</th>
				</tr>
			@foreach ($data as $key => $user)
			<tr>
				<td>{{ ++$i }}</td>
				<td>{{ $user->name }}</td>
				<td>{{ $user->email }}</td>
				<td>
					@if(!empty($user->roles))
						@foreach($user->roles as $v)
							<label class="label label-success">{{ $v->display_name }}</label>
						@endforeach
					@endif
				</td>
				<td>
					<a class="btn btn-info btn-xs" href="{{ route('users.show',$user->id) }}" title="Show"><i class="fa fa-eye"></i></a>
					@permission('user-edit')
					<a class="btn btn-primary btn-xs" href="{{ route('users.edit',$user->id) }}" title="Edit"><i class="fa fa-pencil"></i></a>
					@endpermission
					@permission('user-delete')
					{!! Form::open(['method' => 'DELETE','route' => ['users.destroy', $user->id], 'style'=>'display:inline', 'onsubmit'=>'return confirm("Are you sure you want to delete user '.$user->name.'?")']) !!}
		            {!! Form::button('<i class="fa fa-trash-o"></i>', ['type'=>'submit', 'class' => 'btn btn-danger btn-xs pull-right']) !!}
		        	{!! Form::close() !!}
		        	@endpermission
				</td>
			</tr>
			@endforeach
			</table>
			{!! $data->render() !!}
		@endslot
	@endcomponent
@endsection