@extends('layouts.layout')

@section('css')
@endsection

@section('content')
    <div class="container-fluid">
        <div class="card-body">
            <div class="row d-flex justify-content-between">
                <div>
                    <h4 class="card-title">Download Reports</h4><br><br><br>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="basic-form">
                        <div>
                            <button class="btn btn-primary" style='margin-right:160px' id="orderReport" onclick="showOrderReportForm()"> Get Orders Report </button>
                            <button class="btn btn-primary" style='margin-right:160px'  id="productReport" onclick="showProductReportForm()"> Get Product Report </button>
                            <button id="hideButton" class="btn btn-primary" onclick="hideForm()"> Hide Dates</button>
                        </div>
                        <form id="dateForm">
                            @csrf
                            <div class="row">
                                <div class="form-group col-md-4">
                                    <label>Start Date*</label>
                                    <input type="date" id="start_date" name="start_date" class="form-control input-default">
                                    @if($errors->has('start_date'))
                                        <p class="d-block invalid-feedback animated fadeInDown" style="">
                                            {{ $errors->first('start_date') }}
                                        </p>
                                    @endif
                                </div>

                                <div class="form-group col-md-4">
                                    <label>End Date*</label>
                                    <input type="date" id="end_date" name="end_date" class="form-control input-default">
                                    @if($errors->has('end_date'))
                                        <p class="d-block invalid-feedback animated fadeInDown" style="">
                                            {{ $errors->first('end_date') }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-12 ">
                                <button type="submit" id="getOrdersReport" class="btn btn-primary" href="{{route('exportOrders')}}">Download Report</button>
                                <button type="submit" id="getProductsReport" class="btn btn-primary" href="{{route('exportSoldProducts')}}">Download Report</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
@endsection

@section('js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.16/js/jquery.dataTables.min.js" integrity="sha512-yCkOYsxpzPSpcbHspsH6A28Z0cgsfjJhlR78nPAfLLZSSV40tVN4VQ6ES/miqI/1z8a5FWVYwCF145+eyJx9Tw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.16/js/dataTables.bootstrap4.min.js" integrity="sha512-2wDq7VuYclJFDG5YbUbmOEWYtTEs/DwpKa9maNvC8gIhEHyR/rgh1BuyUrPZy00H8/DGlLAwbYwSpzCRV0dQJw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script>
        $('#dateForm').hide();
        $('#hideButton').hide();
        function showOrderReportForm() {
            $('#dateForm').show();
            $('#hideButton').show();
            $('#getProductsReport').hide();
            $('#getOrdersReport').show();
            $('#productReport').hide();
        }
        function showProductReportForm() {
            $('#dateForm').show();
            $('#hideButton').show();
            $('#getOrdersReport').hide();
            $('#getProductsReport').show();
            $('#orderReport').hide();
        }
        function hideForm() {
            $('#dateForm').hide();
            $('#hideButton').hide();
            $('#getProductsReport').hide();
            $('#getOrdersReport').hide();
            $('#orderReport').show();
            $('#productReport').show();
        }
    </script>
@endsection


