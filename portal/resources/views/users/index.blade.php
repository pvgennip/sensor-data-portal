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
	            <a class="btn btn-primary" href="{{ route('users.create') }}"><i class="fa fa-plus"></i> Add new user</a>
	        @endpermission
		@endslot

		@slot('body')
			<table class="table table-striped">
				<thead>
					<tr>
						<th>ID</th>
						<th>Avatar</th>
						<th>Name</th>
						<th>Email</th>
						<th>Roles</th>
						<th width="280px">Action</th>
					</tr>
				</thead>
				<tbody>
					@foreach ($data as $key => $user)
					<tr>
						<td>{{ $user->id }}</td>
						<td><img src="/uploads/avatars/{{ $user->avatar }}" style="width:35px; height:35px;" class="img-circle"></td>
						<td>{{ $user->name }}</td>
						<td>{{ $user->email }}</td>
						<td>
							@if(!empty($user->roles))
								@foreach($user->roles as $v)
									<label class="label label-warning">{{ $v->display_name }}</label>
								@endforeach
							@endif
						</td>
						<td>
							<a class="btn btn-default" href="{{ route('users.show',$user->id) }}" title="Show"><i class="fa fa-eye"></i></a>
							@permission('user-edit')
							<a class="btn btn-primary" href="{{ route('users.edit',$user->id) }}" title="Edit"><i class="fa fa-pencil"></i></a>
							@endpermission
							@permission('user-delete')
							{!! Form::open(['method' => 'DELETE','route' => ['users.destroy', $user->id], 'style'=>'display:inline', 'onsubmit'=>'return confirm("Are you sure you want to delete user '.$user->name.'?")']) !!}
				            {!! Form::button('<i class="fa fa-trash-o"></i>', ['type'=>'submit', 'class' => 'btn btn-danger']) !!}
				        	{!! Form::close() !!}
				        	@endpermission
						</td>
					</tr>
					@endforeach
				<tbody>
			</table>
			{!! $data->render() !!}
		@endslot
	@endcomponent
@endsection