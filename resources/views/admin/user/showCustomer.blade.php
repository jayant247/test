@extends('layouts.layout')

@section('css')
@endsection

@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Customer Details</h4>
                <div class="row">
                    <table  class="table table-striped table-bordered zero-configuration">
                        <thead>
                        <tr>
                            <th>Field</th>
                            <th>Value</th>
                        </tr>
                        </thead>
                        <tbody>

                        <tr>
                            <td>Name</td>
                            <td>{{$customer->name}}</td>
                        </tr>
                        <tr>
                            <td>Email</td>
                            <td>{{$customer->email}}</td>
                        </tr>
                        <tr>
                            <td>Mobile</td>
                            <td>{{$customer->mobile_no}}</td>
                        </tr>
                        <tr>
                            <td>Role</td>
                            <td>{{$customer->role->name}}</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
@endsection
