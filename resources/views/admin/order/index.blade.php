@extends('layouts.layout')

@section('css')
{{--    /*<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.16/css/dataTables.bootstrap4.min.css" integrity="sha512-NDWv4n2v59EOoj+dDvXfD4uGGTCOXkLAnm+DhQtyYxpZL4lMSymTX5HD8i5NEcF+1YLBkgvByardYsJaA1W6MA==" crossorigin="anonymous" referrerpolicy="no-referrer" />*/--}}
@endsection

@section('content')


    <div class="container-fluid">

        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <div class="row d-flex justify-content-between">
                            <div>
                                <h4 class="card-title">orders</h4>
                            </div>
                            <div >
                                <button class="btn btn-primary" onclick="showHideFilter()"> Filters</button>
                            </div>

                        </div>

                        <div id="filtersOption">
                            <div class="row">
                                <div class="col-md-4">
                                    <label>Order Ref No.</label>
                                    <input id="order_ref" name="order_ref" class="form-control input-default" />
                                </div>
                                <div class="col-md-4">
                                    <label>Pincode</label>
                                    <input id="pincode" name="pincode" class="form-control input-default" />
                                </div>
                                <div class="col-md-4">
                                    <label>Mobile No.</label>
                                    <input id="mobile_no" name="mobile_no" class="form-control input-default" />
                                </div>
                                <div class="col-md-4">
                                    <label>Order Creation Date</label>
                                    <input id="created_at" name="created_at" class="form-control input-default" value=""/>
                                </div>
                            </div>
                            <div class="row d-flex justify-content-end">
                                <button class="btn btn-primary m-2" onclick="showHideFilter()">Hide</button>
                                <button class="btn btn-primary m-2" onclick="applyFilter()">Apply</button>
                                <button class="btn btn-primary m-2" onclick="clearFilter()">Clear Filter</button>
                            </div>

                        </div>

                        <div class="row">
                            <div class="col-sm-12 col-md-6" >
                                <div style="display: inline-flex;" >
                                    <label>Show entries</label>
                                    <select id="page-selector" onchange="pageCountChange(event.target.value)" class="form-control form-control-sm">
                                        <option value="10">10</option>
                                        <option value="25">25</option>
                                        <option value="50">50</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div id="table-div">
                            <div class="row">
                                <div  class="table-responsive">

                                    <table id="orders-table" class="table  table-bordered zero-configuration">
                                        <thead>
                                        <tr>
                                            <th>Order Id</th>
                                            <th>Order Ref No.</th>
                                            <th>Customer Name</th>
                                            <th>Cart Items Count</th>
                                            <th>Total Amount</th>
                                            <th>Order Date</th>
                                            <th>Delivery Pincode</th>
                                            <th>Actions</th>
                                        </tr>
                                        </thead>
                                        <tbody id="tbody">
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="row d-flex justify-content-end" id="pagination-row">
                            </div>
                        </div>


                        <div id="no-data-available" class="text-center">
                            <h5>No Data Available</h5>
                        </div>

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
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <script>
        $(document).ready(function (){
            let filterString =  {!! json_encode($filter) !!};
        });

        function showHideFilter() {
            $('#filtersOption').fadeToggle("medium");
            let params = new URLSearchParams(url.search);
        }

    </script>

    <script>
        let order_status =  {!! json_encode($id) !!};
        var orders = [];
        var query = '';
        var pageLimit = 10;
        var totalordersCount = 0;
        var currentPageNo = 0;
        var totalPageCount = 0;
        var payment_status = 2;

        switch (order_status){
            case 1:
                payment_status = 2;
                break;
            case 2:
                payment_status = 2;
                break;
            case 3:
                payment_status = 2;
                break;
            case 4:
                payment_status = 4;
                break;
            case 5:
                payment_status = 2;
                break;
            case 6:
                payment_status = 2;
                break;
            case 7:
                payment_status = 2;
                break;
            case 8:
                payment_status = 2;
                break;
            case 9:
                payment_status = 2;
                break;
            case 10:
                payment_status = 2;
                break;
        }


        function showHideFilter() {
            $('#filtersOption').fadeToggle("medium");

        }
        function pageCountChange(pageCount) {
            pageLimit = pageCount;
            getData();
        }
        function showPages() {
            totalPageCount  = Math.ceil(totalordersCount/pageLimit);

            let pagination=  '<nav>' +
                '<ul class="pagination">' +
                '<li id="previous" class="page-item disabled"><a class="page-link" href="#">Previous</a>' +
                '</li>';
            for(let i=0;i<totalPageCount;i++){
                if(currentPageNo==i){
                    let currentPage = i+1;
                    pagination += '<li id="'+i+'" class="page-item page-no active" ><a class="page-link" href="#">'+currentPage+'</a>' +
                        '</li>'
                }else{
                    if(i>3){
                        let currentPage = i+1;
                        pagination += '<li id="'+i+'" class="page-item d-none page-no" ><a class="page-link" href="#">'+currentPage+'</a>' +
                            '</li>'
                    }else{
                        let currentPage = i+1;
                        pagination += '<li id="'+i+'" class="page-item page-no" ><a class="page-link" href="#">'+currentPage+'</a>' +
                            '</li>'
                    }
                }


            }
            pagination +=
                '<li id="next" class="page-item"><a class="page-link" href="#">Next</a>' +
                '</li>'
            '</ul></nav>';
            $('#pagination-row').empty();
            $('#pagination-row').append(pagination);

        }


        function paginationHelping() {
            if(orders.length>0){
                $(document).ready( function () {
                    $('#orders-tablee').DataTable({
                        "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]]
                    });
                } );
                $('#table-div').show();
                $('#no-data-available').hide();
            }else{
                $(document).ready( function () {
                    $('#orders-tablee').DataTable({

                    }).destroy();
                } );
                $('#table-div').hide();
                $('#no-data-available').show();
            }
            $('.page-item').removeClass('active')
            $('#'+currentPageNo).addClass('active')
            if(currentPageNo==0){
                $('#previous').addClass('disabled')
            }else{
                $('#previous').removeClass('disabled')
            }

            if(totalPageCount-1 == currentPageNo ){
                $('#next').addClass('disabled')
            }else{
                $('#next').removeClass('disabled')
            }
            for(let i=4;i<totalPageCount;i++){
                $('#'+i).addClass('d-none');
            }
            if(currentPageNo>3){
                $('#'+currentPageNo).removeClass('d-none')
            }
        }
        function getData() {
            let dataToSend = {}
            dataToSend['url']= "{!! route('getOrders') !!}"+"?pageNo="+currentPageNo+"&limit="+pageLimit+"&payment_status="+payment_status+"&order_status="+order_status+query;
            dataToSend['requestType']='GET';
            dataToSend['data']={

            };
            console.log("getData")
            dataToSend['successCallbackFunction'] = onGetDataSuccess;
            ajaxCall(dataToSend)
        }

        function onGetDataSuccess(data) {
            if(data['success']){
                orders = data['data']['orders'];
                console.log(orders)
                totalordersCount = data['data']['count'];
                generateResult();
                showPages();
                paginationHelping();
                $('#table-div').show();
            }else{
                $('#no-data-available').show();
                $('#table-div').hide();
                showToast('error','Error',data['message']);
            }
        }

        function generateResult() {
            let tableBody=''
            for(let i=0;i<orders.length;i++){
                let showurl = "{{ route('order.show',':id' ) }}";
                showurl = showurl.replace(':id', orders[i].id);
                let editurl = "{{ route('order.edit',':id' ) }}";
                editurl = editurl.replace(':id', orders[i].id);
                let deleteurl = "{{ route('deleteProduct',':id' ) }}";
                deleteurl = deleteurl.replace(':id', orders[i].id);

                $("p").css("background-color");

                tableBody+='<tr><td>'+
                    orders[i]['id']+'</td>'+'<td>'+
                    orders[i]['orderRefNo']+'</td>'+'<td>'+
                    orders[i]['customer']['name']+'</td>'+'<td>'+
                    orders[i]['order_items_count']+'</td>'+'<td>'+
                    orders[i]['total']+'</td>'+'<td>'+
                    orders[i]['created_at']+'</td>'+'<td>'+
                    orders[i]['address_details']['pincode']+'</td>'+
                    '<td>'+
                    '<a class="btn btn-sm btn-outline-dark"  href="'+showurl+'">'+
                    '<i class="fa fa-eye" ></i>'+
                    '</a>'
                    +'</td></tr>';
            }
            $('#tbody').empty();
            console.log(tableBody)
            $('#tbody').append(tableBody);

        }
        function clearFilter(){
            $('#pincode').val('');
            $('#mobile_no').val('');
            $('#created_at').val('');
            $('#order_ref').val('');
            getData();
        }
        function applyFilter(){
            query='';
            if($('#pincode').val()){
                query =query+'&'+'pincode='+$('#pincode').val();
            }
            if($('#mobile_no').val()){
                query =query+'&'+'mobile_no='+$('#mobile_no').val();
            }
            if($('#order_ref').val()){
                query =query+'&'+'order_ref='+$('#order_ref').val();
            }
            if($('#created_at').val()){
                query =query+'&'+'startDate='+$('#created_at').val().split('-')[0].trim();
                query =query+'&'+'endDate='+$('#created_at').val().split('-')[1].trim();
            }
            getData();
        }
        $(document).ready(function () {
            getData();
            $('#filtersOption').hide()

            $(document).on('click','.page-no',function () {
                currentPageNo = +(this.id)
                console.log(currentPageNo)
                getData();
            })
            $(document).on('click','#next',function () {
                if(currentPageNo<totalPageCount-1){
                    currentPageNo++;
                    getData();
                }
            })
            $(document).on('click','#previous',function () {

                if(currentPageNo>0){
                    currentPageNo--;
                    getData();
                }
            })
            $('input[name="created_at"]').focus(function (){
                $('input[name="created_at"]').daterangepicker({
                    maxDate: moment(),
                    autoApply: false,
                    autoUpdateInput:false,
                    ranges: {
                        'Today': [moment(), moment()],
                        'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                        'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                        'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                        'This Month': [moment().startOf('month'), moment().endOf('month')],
                        'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                    },
                });
            });


            $('input[name="created_at"]').on('apply.daterangepicker', function(ev, picker) {
                $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
            });

            $('input[name="created_at"]').on('cancel.daterangepicker', function(ev, picker) {
                $(this).val('');
            });

        });
    </script>
@endsection


