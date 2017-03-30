@extends('layouts.app')
 
@section('page-title') Group management
@endsection

@section('content')

			
	@component('components/box')
		@slot('title')
			Group overview
		@endslot

		@slot('action')
			@permission('group-create')
	            <a class="btn btn-primary" href="{{ route('groups.create') }}"><i class="fa fa-plus"></i> Add new group</a>
	            @endpermission
		@endslot

		@slot('body')
			<table class="table table-striped">
				<tr>
					<th>ID</th>
					<th>Name</th>
					<th>Type</th>
					<th width="280px">Action</th>
				</tr>
			@foreach ($groups as $key => $group)
			<tr>
				<td>{{ $group->id }}</td>
				<td>{{ $group->name }}</td>
				<td>{{ $group->type }}</td>
				<td>
					<a class="btn btn-default" href="{{ route('groups.show',$group->id) }}" title="Show"><i class="fa fa-eye"></i></a>
					@permission('group-edit')
					<a class="btn btn-primary" href="{{ route('groups.edit',$group->id) }}" title="Edit"><i class="fa fa-pencil"></i></a>
					@endpermission
					@permission('group-delete')
					{!! Form::open(['method' => 'DELETE','route' => ['groups.destroy', $group->id],'style'=>'display:inline', 'onsubmit'=>'return confirm("Are you sure you want to delete group '.$group->name.'?")']) !!}
		            {!! Form::button('<i class="fa fa-trash-o"></i>', ['type'=>'submit', 'class' => 'btn btn-danger pull-right']) !!}
		        	{!! Form::close() !!}
		        	@endpermission
				</td>
			</tr>
			@endforeach
			</table>
			{!! $groups->render() !!}
		@endslot
	@endcomponent
@endsection