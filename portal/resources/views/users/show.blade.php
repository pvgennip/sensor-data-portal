@extends('layouts.app')

@section('page-title') User
@endsection

@section('content')

	<div class="row">
		<div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <label>Name:</label>
                <p>{{ $user->name }}</p>
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <label>Email:</label>
                <p>{{ $user->email }}</p>
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <label>Sensors:</label>
                @if(!empty($sensors))
                    <p>
                    @foreach($sensors as $key => $name)
                        <label class="label label-primary">{{ $name }}</label>
                    @endforeach
                    </p>
                @endif
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <label>Roles:</label>
                @if(!empty($user->roles))
                    <p>
					@foreach($user->roles as $v)
						<label class="label label-warning">{{ $v->display_name }}</label>
					@endforeach
                    </p>
                @endif
            </div>
        </div>
	</div>
@endsection