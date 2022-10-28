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
                <h3 class="card-title">Files</h3>
            </div>
            <form name="clickpost" action="{{ url('/clickpost/post') }}" id="clickpost" method="post" enctype="multipart/form-data">
                <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover text-nowrap">
                        <thead>
                            <tr>
                                <th>Sr No</th>
                                <th>Folder Name</th>
                                <th>Total Records</th>
                                <th>Success Records</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($files as $index => $file)
                            <?php $id = $file['file_id']; ?>
                            <tr>
                                <td>{{ $file['id'] }}</td>
                                <td>{{ date('Y_m_d_H_i_s', strtotime($file['uploaded_at'])) }}</td>
                                <td>{{ $file['total_count']}}</td>
                                <td>{{ $file['success_count']}}</td>
                                <td>
                                    <a class="btn btn-info" href="{!! url('/call-the-command?id='.$file['file_id']) !!}" target="_blank">Process Pending</a>
                                    <a class="btn btn-success" href="{{ url('/clickpost/download-excel?id='.$file['id']) }}" target="_blank">Download File</a>

                                    <a class="btn btn-success" href="{{ url('/clickpost/download-pdf?id='.$file['id']) }}" target="_blank">Download Labels</a>

                                    <a class="btn btn-danger" href="{{ url('/clickpost/delete?id='.$file['id']) }}" >Delete</a>

                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </form>
        </div>
        
    </div>
</div>
@stop