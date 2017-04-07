@extends('layouts.app')

@section('page-title') {{ __('crud.management', ['item'=>__('general.user')]) }}
@endsection

@section('content')


	@component('components/box')
		@slot('title')
			{{ __('crud.overview', ['item'=>__('general.users')]) }}
		@endslot

		@slot('action')
			@permission('user-create')
	            <a class="btn btn-primary" href="{{ route('users.create') }}"><i class="fa fa-plus"></i> {{ __('crud.add', ['item'=>__('general.user')]) }}</a>
	        @endpermission
		@endslot

		@slot('body')
			<table class="table table-striped">
				<thead>
					<tr>
						<th>{{ __('crud.id') }}</th>
						<th>{{ __('crud.avatar') }}</th>
						<th>{{ __('crud.name') }}</th>
						<th>{{ __('crud.email') }}</th>
						<th>{{ __('crud.roles') }}</th>
						<th>{{ __('crud.actions') }}</th>
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
							<a class="btn btn-default" href="{{ route('users.show',$user->id) }}" title="{{ __('crud.show') }}"><i class="fa fa-eye"></i></a>
							@permission('user-edit')
							<a class="btn btn-primary" href="{{ route('users.edit',$user->id) }}" title="{{ __('crud.edit') }}"><i class="fa fa-pencil"></i></a>
							@endpermission
							@permission('user-delete')
							{!! Form::open(['method' => 'DELETE','route' => ['users.destroy', $user->id], 'style'=>'display:inline', 'onsubmit'=>'return confirm("'.__('crud.sure',['item'=>__('general.user'),'name'=>'\''.$user->name.'\'']).'")']) !!}
				            {!! Form::button('<i class="fa fa-trash-o"></i>', ['type'=>'submit', 'class' => 'btn btn-danger pull-right']) !!}
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