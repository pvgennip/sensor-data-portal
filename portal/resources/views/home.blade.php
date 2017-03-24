@extends('layouts.app')

@section('page-title') Dashboard
@endsection

@section('breadcrum')
    <ol class="breadcrumb">
        <li><a href="/admin"><i class="fa fa-home"></i> Home</a></li>
        <li class="active">Dashboard</li>
    </ol>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Info</div>

                <div class="panel-body">
                    You are logged in!
                </div>
            </div>
        </div>
    </div>
@endsection
