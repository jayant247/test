@extends('layouts.layout')

@section('css')
@endsection

@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">GiftCard Details</h4>
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
                           <td>Pincode</td>
                           <td>{{$pincode->pincode}}</td>
                        </tr>
                        <tr>
                           <td>Is Active</td>
                           <td>{{$pincode->is_active}}</td>
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


