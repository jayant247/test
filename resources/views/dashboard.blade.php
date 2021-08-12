@extends('layouts.layout')

@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.16/css/dataTables.bootstrap4.min.css" integrity="sha512-NDWv4n2v59EOoj+dDvXfD4uGGTCOXkLAnm+DhQtyYxpZL4lMSymTX5HD8i5NEcF+1YLBkgvByardYsJaA1W6MA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
@endsection

@section('content')
    <div class="container-fluid">
        <div class="card-body">
            <div class="row">
                <div class="col-12">
                    <div class="basic-form">
                        <div>
                            <button class="btn btn-primary" onclick="showForm()"> Select Dashboard Date</button>
                            <button id="hideButton" class="btn btn-primary" onclick="hideForm()"> Hide Dates</button>
                        </div>
                        <form enctype="multipart/form-data" action="{{ route('dashboardForDate') }}" method="POST" id="dateForm">
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
                                <button type="submit" class="btn btn-primary">Submit Dates</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-lg-3 col-sm-6">
                <div class="card gradient-1">
                    <div class="card-body">
                        <h3 class="card-title text-white">Total Orders </h3>
                        <div class="d-inline-block">
                            <h2 class="text-white">{{$totalOrders}}</h2>
                            @if($is_specific_date)
                                <p class="text-white mb-0">{{$start_date}} - {{$end_date}}
                            @else
                                <p class="text-white mb-0">{{ date('Y-m-d') }}</p>
                            @endif
                        </div>
                        <span class="float-right display-5 opacity-5"></span>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-sm-6">
                <div class="card gradient-2">
                    <div class="card-body">
                        <h3 class="card-title text-white">Total Products Sold</h3>
                        <div class="d-inline-block">
                            <h2 class="text-white">{{$productCount}}</h2>
                            @if($is_specific_date)
                                <p class="text-white mb-0">{{$start_date}} - {{$end_date}}
                            @else
                                <p class="text-white mb-0">{{ date('Y-m-d') }}</p>
                            @endif
                        </div>
                        <span class="float-right display-5 opacity-5"></span>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6">
                <div class="card gradient-3">
                    <div class="card-body">
                        <h3 class="card-title text-white">Total Order Value</h3>
                        <div class="d-inline-block">
                            <h2 class="text-white"> ₹ {{$orderValue}}</h2>
                            @if($is_specific_date)
                                <p class="text-white mb-0">{{$start_date}} - {{$end_date}}
                            @else
                                <p class="text-white mb-0">{{ date('Y-m-d') }}</p>
                            @endif
                        </div>
                        <span class="float-right display-5 opacity-5"></span>
                    </div>
                </div>
            </div>

            
            <div class="col-lg-3 col-sm-6">
                <div class="card gradient-4">
                    <div class="card-body">
                        <h3 class="card-title text-white">New Customers</h3>
                        <div class="d-inline-block">
                            <h2 class="text-white">{{$newCust}}</h2>
                            @if($is_specific_date)
                                <p class="text-white mb-0">{{$start_date}} - {{$end_date}}
                            @else
                                <p class="text-white mb-0">{{ date('Y-m-d') }}</p>
                            @endif
                        </div>
                        <span class="float-right display-5 opacity-5"></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-7">
                <div id="linechart" style="width: 600px; height: 400px"></div>
            </div>
            <div class="col-4">
                <div id="piechart" style="width: 400px; height: 400px;"></div>
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
        function showForm() {
            $('#dateForm').show();
            $('#hideButton').show();
        }
        function hideForm() {
            $('#dateForm').hide();
            $('#hideButton').hide();
        }
    </script>
    <script type="text/javascript">
      var orderResult = <?php echo $orderResult ?>;
      var ordercategories = <?php echo $orderCategories ?>;
      console.log(orderResult);
      let googleData=[];
      let googlePieChartData=[];
      Object.keys(orderResult).forEach(key => {
          googleData.push(orderResult[key])
       });
      Object.keys(ordercategories).forEach(key => {
          googlePieChartData.push(ordercategories[key])
       });
      // forEach(let item of orderResult){
      //   // googleData.push(item);
      // }
      console.log(googleData);
      google.charts.load('current', {'packages':['corechart']});
      google.charts.setOnLoadCallback(drawChart);
      function drawChart() {
        var data = google.visualization.arrayToDataTable(googleData);
        var data1 = google.visualization.arrayToDataTable(googlePieChartData);
        var options = {
          title: 'Weekly Order Count',
          curveType: 'function',
          legend: { position: 'bottom' }
        };
        var chart = new google.visualization.LineChart(document.getElementById('linechart'));
        chart.draw(data, options);

        var options1 = {
            title: 'Order Status Details',
            is3D: true,
          };

          var chart1 = new google.visualization.PieChart(document.getElementById('piechart'));

          chart1.draw(data1, options1);
      }
    </script>


@endsection


