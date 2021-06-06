@extends('layouts.layout')

@section('css')
@endsection

@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Product Details</h4>
                <div class="row">                                        
                    <div class="col form-group col-md-2">
                        <div class="card-body">
                            <label>Product Name :</label>
                        </div>
                    </div>
                    <div class="col form-group col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <p>{{$product->product_name}}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col form-group col-md-2">
                        <div class="card-body">
                            <label>Product Description :</label>
                        </div>
                    </div>
                    <div class="col form-group col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <p>{{$product->description}}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">                                        
                    <div class="col form-group col-md-2">
                        <div class="card-body">
                            <label>Product Price :</label>
                        </div>
                    </div>
                    <div class="col form-group col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <p>{{$product->price}}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col form-group col-md-2">
                        <div class="card-body">
                            <label>Product MRP :</label>
                        </div>
                    </div>
                    <div class="col form-group col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <p>{{$product->mrp}}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">                                        
                    <div class="col form-group col-md-2">
                        <div class="card-body">
                            <label>Available Sizes :</label>
                        </div>
                    </div>
                    <div class="col form-group col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <p>{{$product->available_sizes}}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col form-group col-md-2">
                        <div class="card-body">
                            <label>Available Colors :</label>
                        </div>
                    </div>
                    <div class="col form-group col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <p>{{$product->available_colors}}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row ">                                        
                    <div class="col form-group col-md-2 col-lg-2 B ">
                        <div class="card-body">
                            <label >Available Sizes :</label>
                        </div>
                    </div>
                    <div class="col form-group col-md-4 col-lg-2 B">
                        <div class="card">
                            <div class="card-body ">
                                <p>{{$product->available_sizes}}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col form-group col-md-2">
                        <div class="card-body">
                            <label>Available Colors :</label>
                        </div>
                    </div>
                    <div class="col form-group col-md-4">
                        <div class="card">
                            <div class="card-body  style=height:100px;width:100px">
                                <p>{{$product->available_colors}}</p>
                            </div>
                        </div>
                    </div>
                </div>


            </div>
        </div>


    </div>
@endsection

@section('js')
@endsection


