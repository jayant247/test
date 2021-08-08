@extends('layouts.layout')

@section('css')
@endsection

@section('content')
    <div class="container-fluid">

        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between">
                    <h3>Order Ref No : #{{$order->orderRefNo}}</h3>
                    <h3>Order Status : {{$order->orderStatus->name}}</h3>
                </div>

            </div>
            <div class="card-body">
                <div class="row">
                    @if($order->order_status==1)
                        <div class="col-md-12">
                            <button type="button"  data-toggle="modal" data-target="#confirmModal" class="btn btn-primary">Confirm Order</button>
                        </div>
                    @endif
                    @if($order->order_status==4)
                        <div class="col-md-12">
                            <h5>Cancellation Reason</h5>
                            <p>{{$order->cancellation_reason}}</p>
                        </div>
                    @endif
                    @if($order->order_status==7 || $order->order_status==8 || $order->order_status==9 || $order->order_status==10)
                        <div class="col-md-12">
                            <h5>Return/Replacement Type : {{$order->return_replacemnet_type}}</h5>
                        </div>
                    @endif
                </div>
                <hr>
                <div class="row">
                    <div class="col-12">
                        <h5 class="mb-sm-0">Address Details</h5>
                        <br>
                    </div>
                    <div class="col-md-4">
                        <p>Address Name
                            <br>{{$order->addressDetails->address_name}}
                        </p>
                    </div>
                    <div class="col-md-4">
                        <p>Address Line 1
                            <br>{{$order->addressDetails->address_line_1}}
                        </p>
                    </div>
                    <div class="col-md-4">
                        <p>Address Line 2
                            <br>{{$order->addressDetails->address_line_2}}
                        </p>
                    </div>
                    <div class="col-md-4">
                        <p>City
                            <br>{{$order->addressDetails->city}}
                        </p>
                    </div>
                    <div class="col-md-4">
                        <p>Pincode
                            <br>{{$order->addressDetails->pincode}}
                        </p>
                    </div>
                    <div class="col-md-4">
                        <p>Contact Number
                            <br>{{$order->addressDetails->contact_number}}
                        </p>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="table-responsive">
                        <table class="table table-bordered zero-configuration">
                            <thead>
                            <tr>
                                <th>Item Id</th>
                                <th>Item Name</th>
                                <th>Item Color</th>
                                <th>Item Size</th>
                                <th>Item Image</th>
                                <th>Item Qty</th>
                                <th>Item Price</th>
                                <th>Item Total Price</th>
                                @if($order->order_status==7 || $order->order_status==8 || $order->order_status==9 || $order->order_status==10)
                                    <th>Order Replacement/ Return Reason</th>
                                    <th>Order Replacement/ Return Time</th>
                                    <th>Order Replacement/ Return Status</th>
                                    <th>Action</th>
                                @endif
                            </tr>
                            </thead>
                            <tbody id="tbody">
                                @foreach($order->orderItems as $orderItem)
                                    <tr>
                                       <td>{{$orderItem->product_variable_id}}</td>
                                        <td>{{$orderItem->productVariable->productDetails->product_name}}</td>
                                        <td>{{$orderItem->productVariable->color}}</td>
                                        <td>{{$orderItem->productVariable->size}}</td>
                                        <td>
                                            <a href="{{env('APP_URL').$orderItem->productVariable->primary_image}}" target="_blank">
                                                <img loading="lazy" style="width: 100px;max-height: 100px;" src="{{env('APP_URL').$orderItem->productVariable->primary_image}}">
                                            </a>
                                        </td>
                                        <td>{{$orderItem->quantity}}</td>
                                        <td>{{$orderItem->selling_price}}</td>
                                        <td>{{$orderItem->selling_price*$orderItem->quantity}}</td>
                                        @if($order->order_status==7 || $order->order_status==8 || $order->order_status==9 || $order->order_status==10)
                                            <th>{{$orderItem->return_replacement_reason??''}}</th>
                                            <th>{{$orderItem->return_replacement_requested_at??''}}</th>
                                            <th>{{$orderItem->replacement_return_status??''}}</th>

                                            <th>
                                                @if($orderItem->replacement_return_status=='requested')
                                                    <a class="btn btn-link">Approve</a>
                                                    <a class="btn btn-link">Decline</a>
                                                @endif
                                            </th>
                                        @endif
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="table-responsive">
                            <table class="table table-bordered zero-configuration">
                                <thead>
                                <tr>
                                    <td>Payment Mode Used</td>
                                    <td>{{$order->paymentMode}}</td>
                                </tr>
                                <tr>
                                    <td>Order Status</td>
                                    <td>{{$order->orderStatus->name}}</td>
                                </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                    <div class="col-md-6 ">
                        <div class="table-responsive">
                            <table class="table table-bordered zero-configuration">
                                <thead>
                                    <tr>
                                        <td>Subtotal</td>
                                        <td>{{$order->subTotal}}</td>
                                    </tr>
                                    <tr>
                                        <td>Wallet Balance Used</td>
                                        <td>{{$order->wallet_balance_used}}</td>
                                    </tr>
                                    <tr>
                                        <td>Promo Code Discount</td>
                                        <td>{{$order->promo_discount}}</td>
                                    </tr>
                                    <tr>
                                        <td>Gift Card Used</td>
                                        <td>{{$order->gift_card_amount_used}}</td>
                                    </tr>
                                    <tr>
                                        <td>Shipping Charges</td>
                                        <td>{{$order->shipping_charge}}</td>
                                    </tr>
                                    <tr>
                                        <td>Total</td>
                                        <td>{{$order->total}}</td>
                                    </tr>

                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="row">
                    @if($order->order_status==1)
                        <div class="col-md-12">
                            <button type="button"  data-toggle="modal" data-target="#confirmModal" class="btn btn-primary">Confirm Order</button>
                        </div>
                    @endif
                    @if($order->order_status==4)
                        <div class="col-md-12">
                            <p>Cancellation Reason</p>
                            <p>{{$order->cancellation_reason}}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="confirmModal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Order Confirmation Modal</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{route('confirmOrder',$order->id)}}" method="post">
                        @csrf
                        <div class="row form-group">
                            <div class="col-md-4">
                                <label>Height</label>
                                <input required class="form-control input-default" name="height" id="height" type="number">
                            </div>
                            <div class="col-md-4">
                                <label>Length</label>
                                <input required class="form-control input-default" name="length" id="length" type="number">
                            </div>
                            <div class="col-md-4">
                                <label>Breadth</label>
                                <input required class="form-control input-default" name="breadth" id="breadth" type="number">
                            </div>
                            <div class="col-md-4">
                                <label>Weight</label>
                                <input required class="form-control input-default" name="weight" id="weight" type="number">
                            </div>

                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <button class="btn btn-primary" type="submit">Confirm Order</button>
                            </div>

                        </div>

                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>

                </div>
            </div>
        </div>
    </div>

@endsection

@section('js')
    <script>

    </script>
@endsection
