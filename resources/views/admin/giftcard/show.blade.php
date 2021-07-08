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
                           <td>Title</td>
                           <td>{{$giftcard->title}}</td>
                       </tr>
                       <tr>
                           <td>Description</td>
                           <td>{{$giftcard->description}}</td>
                       </tr>
                       <tr>
                           <td>Purchase Amount</td>
                           <td>{{$giftcard->purchase_amount}}</td>
                       </tr>
                       <tr>
                           <td>Gift Amount</td>
                           <td>{{$giftcard->gift_amount}}</td>
                       </tr>
                       <tr>
                           <td>Validity</td>
                           <td>{{$giftcard->validity_days_from_purchase_date}}</td>
                       </tr>
                       <tr>
                           <td>Start Date</td>
                           <td>{{$giftcard->start_from}}</td>
                       </tr>
                       <tr>
                           <td>End Date</td>
                           <td>{{$giftcard->end_on}}</td>
                       </tr>
                       <tr>
                           <td>Is Active</td>
                           <td>{{$giftcard->is_active}}</td>
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


