@extends('layouts.app')
@section('sidebar')
@include('layouts.sidebar')
@stop
@section('content-header')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark">Clickpost</h1>
            </div>
            <!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                    <li class="breadcrumb-item active">Clickpost</li>
                </ol>
            </div>
            <!-- /.col -->
        </div>
        <!-- /.row -->
    </div>
    <!-- /.container-fluid -->
</div>
@stop
@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Generate the AWB using excel</h3>
            </div>
            <form name="clickpost" action="{{ url('/clickpost/post') }}" id="clickpost" method="post" enctype="multipart/form-data">
                <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                <div class="card-body">
                    <div class="form-group">
                        <label for="exampleInputFile">File input</label>
                        <div class="input-group">
                            <div class="custom-file">
                                <input type="file" required="required" class="custom-file-input" id="exampleInputFile" name="exampleInputFile">
                                <label class="custom-file-label" for="exampleInputFile">Choose file</label>
                            </div>
                            <div class="input-group-append">
                                <span class="input-group-text">
                                    <a href="data:text/csv;charset=utf-8,drop_name,drop_pincode,drop_city,drop_state,drop_country,drop_address,drop_phone,cod_value,invoice_date,quantity,price,invoice_number,length,breadth,height,weight,reference_number,order_type,invoice_value,drop_email,order_id,otp_required,shipping_id" download="<?php echo time(); ?>.csv">
                                        DOWNLOAD SAMPLE
                                    </a>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </form>
        </div>
        <img src="{{ asset('img/shipping-methods.png') }}" class="img-responsive">
    </div>
</div>
@stop