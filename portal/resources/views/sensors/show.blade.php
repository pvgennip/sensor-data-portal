@extends('layouts.app')
 
@section('page-title') Sensors
@endsection

@section('content')

	<div class="row">
		<div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <label>Name:</label>
                <p>{{ $item->name }}</p>
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <label>Type:</label>
                <p>{{ $item->type }}</p>
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <label>Key:</label>
                <p>{{ $item->key }}</p>
            </div>
        </div>
	</div>
@endsection