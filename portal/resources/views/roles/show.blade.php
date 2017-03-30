@extends('layouts.app')

@section('page-title') Role
@endsection

@section('content')

	<div class="row">
		<div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <label>Name:</label>
                <p>{{ $role->display_name }}</p>
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <label>Description:</label>
                <p>{{ $role->description }}</p>
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <label>Permissions:</label>
                @if(!empty($rolePermissions))
                    <p>
					@foreach($rolePermissions as $v)
						<label class="label label-success">{{ $v->display_name }}</label>
					@endforeach
                    </p>
				@endif
            </div>
        </div>
	</div>
@endsection