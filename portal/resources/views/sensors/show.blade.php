@extends('layouts.app')
 
@section('page-title') Sensors
@endsection

@section('content')
	<div class="row">
	    <div class="col-lg-12 margin-tb">
	        <div class="pull-right">
	            <a class="btn btn-primary" href="{{ route('sensors.index') }}"> Back</a>
	        </div>
	    </div>
	</div>
	<div class="row">
		<div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <strong>Name:</strong>
                {{ $item->name }}
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <strong>Type:</strong>
                {{ $item->type }}
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <strong>Key:</strong>
                {{ $item->key }}
            </div>
        </div>
	</div>
@endsection