@extends('layouts.layout')

@section('css')
{{--    /*<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.16/css/dataTables.bootstrap4.min.css" integrity="sha512-NDWv4n2v59EOoj+dDvXfD4uGGTCOXkLAnm+DhQtyYxpZL4lMSymTX5HD8i5NEcF+1YLBkgvByardYsJaA1W6MA==" crossorigin="anonymous" referrerpolicy="no-referrer" />*/--}}
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row d-flex justify-content-between">
                            <div>
                                <h4 class="card-title">Orders</h4>
                            </div>
                            <div>
                                <button class="btn btn-primary" onclick="showHideFilter()"> Filters</button>
                            </div>
                        </div>
                        <div id="filtersOption">
                            <form class="" method="GET" id="filterForm">
                                 <div class="row">


                                    <div class="form-group mb-2 col-md-8">
                                        <label for="search" class="">Search(by order ref. no/ order id / customer name / delivery piconde,)</label>
                                        <input type="text" class="form-control input-default" id="search" name="search" placeholder="Product name..." value="{{$filter}}">
                                    </div>
                                     <input hidden name="filter" id="filter">
                                </div>
                            </form>
                            <div class="row d-flex justify-content-end">
                                <button class="btn btn-primary m-2" onclick="showHideFilter()">Hide</button>
                                <button class="btn btn-primary m-2" onclick="clearFilters()">Clear Filter</button>
                                <button class="btn btn-primary m-2" onclick="submitFilterForm()">Apply</button>
                            </div>

                        </div>
                        @if(count($orders)>0)
                            <div class="table-responsive">
                                <table id="giftcard-table" class="table table-striped table-bordered zero-configuration">
                                    <thead>
                                    <tr>
                                        <th>@sortablelink('id', 'Order Id')</th>
                                        <th>@sortablelink('orderRefNo', 'Order Ref. No.')</th>
                                        <th>@sortablelink('customer.name', 'Customer Name') </th>
                                        <th>Cart Items Count</th>
                                        <th>@sortablelink('total', 'Total Amount')</th>
                                        <th>@sortablelink('created_at', 'Order Date')</th>
                                        <th>Delivery Pincode</th>
                                        <th>Actions</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($orders as $order)
                                        <tr>
                                            <td>{{$order->id}}</td>
                                            <td>{{$order->orderRefNo}}</td>
                                            <td>{{$order->customer->name}}</td>
                                            <td>{{$order->order_items_count}}</td>
                                            <td>{{$order->total}}</td>
                                            <td>{{$order->created_at}}</td>
                                            <td>{{$order->addressDetails->pincode}}</td>
                                            <td>
                                                <button data-toggle="modal" onclick="openDetailsModal({{$order->id}})"  class="btn btn-sm btn-outline-dark" href="{{route('order.show',$order->id)}}">
                                                    <i class="fa fa-eye"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="d-flex justify-content-end">
                            {{ $orders->appends(request()->except('page'))->links() }}
                            </div>
                            <p>
                                Displaying {{$orders->count()}} of {{ $orders->total() }} product(s).
                            </p>
                        @else
                            <div class="text-center">
                                <h5>No Data Available</h5>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>


    {{--    Modal For Show Option--}}




@endsection

@section('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.16/js/jquery.dataTables.min.js" integrity="sha512-yCkOYsxpzPSpcbHspsH6A28Z0cgsfjJhlR78nPAfLLZSSV40tVN4VQ6ES/miqI/1z8a5FWVYwCF145+eyJx9Tw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.16/js/dataTables.bootstrap4.min.js" integrity="sha512-2wDq7VuYclJFDG5YbUbmOEWYtTEs/DwpKa9maNvC8gIhEHyR/rgh1BuyUrPZy00H8/DGlLAwbYwSpzCRV0dQJw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script>
        $(document).ready(function (){
            let filterString =  {!! json_encode($filter) !!};
        });

        function submitFilterForm(){
            document.getElementById("filterForm").submit()
        }
        function showHideFilter() {
            $('#filtersOption').fadeToggle("medium");
            let params = new URLSearchParams(url.search);
        }
        function clearFilters(){
            let params = window.location.href.split("?");
            window.location.href = params[0];
        }
    </script>
@endsection


