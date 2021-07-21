@extends('layouts.layout')

@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.16/css/dataTables.bootstrap4.min.css" integrity="sha512-NDWv4n2v59EOoj+dDvXfD4uGGTCOXkLAnm+DhQtyYxpZL4lMSymTX5HD8i5NEcF+1YLBkgvByardYsJaA1W6MA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
@endsection

@section('content')
    <div class="container-fluid">

                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                        <div class="row d-flex justify-content-between">
                            <div>
                                <h4 class="card-title">Products</h4>
                            </div>
                            <div >
                                <button class="btn btn-primary" onclick="showHideFilter()"> Filters</button>
                                <a href="{{route('product.create')}}" class="btn btn-primary">Create</a>
                            </div>

                        </div>

                        <div id="filtersOption">
                            <div class="row">
                                <div class="col-md-4">
                                    <h2>dcds</h2>
                                </div>
                                <div class="col-md-4">
                                    <h2>dcds</h2>
                                </div>
                                <div class="col-md-4">
                                    <h2>dcds</h2>
                                </div>
                            </div>
                            <div class="row d-flex justify-content-end">
                                <button class="btn btn-primary m-2" onclick="showHideFilter()">Hide</button>
                                <button class="btn btn-primary m-2" onclick="clearFiltes()">Clear Filter</button>
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

                                    <table id="products-table" class="table  table-bordered zero-configuration">
                                        <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Price</th>
                                            <th>MRP</th>

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
    <script>
        var products = [];
        var query = '';
        var pageLimit = 10;
        var totalProductsCount = 0;
        var currentPageNo = 0;
        var totalPageCount = 0;
        function showHideFilter() {
            $('#filtersOption').fadeToggle("medium");

        }
        function pageCountChange(pageCount) {
            pageLimit = pageCount;
            getData();
        }
        function showPages() {
            totalPageCount  = Math.ceil(totalProductsCount/pageLimit);

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
            if(products.length>0){
                $(document).ready( function () {
                    $('#products-tablee').DataTable({
                        "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]]
                    });
                } );
                $('#table-div').show();
                $('#no-data-available').hide();
            }else{
                $(document).ready( function () {
                    $('#products-tablee').DataTable({

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
            dataToSend['url']= "{!! route('getProductList') !!}"+"?pageNo="+currentPageNo+"&limit="+pageLimit;
            dataToSend['requestType']='GET';
            dataToSend['data']={

            };
            console.log("getData")
            dataToSend['successCallbackFunction'] = onGetDataSuccess;
            ajaxCall(dataToSend)
        }

        function onGetDataSuccess(data) {
            if(data['success']){
                products = data['data']['products'];
                totalProductsCount = data['data']['count'];
                generateResult();
                showPages();
                paginationHelping()
            }else{
                showToast('error','Error',data['message']);
            }
        }

        function generateResult() {
            let tableBody=''
            for(let i=0;i<products.length;i++){
                let showurl = "{{ route('product.show',':id' ) }}";
                showurl = showurl.replace(':id', products[i].id);
                let editurl = "{{ route('product.edit',':id' ) }}";
                editurl = editurl.replace(':id', products[i].id);
                let deleteurl = "{{ route('deleteProduct',':id' ) }}";
                deleteurl = deleteurl.replace(':id', products[i].id);

                $("p").css("background-color");

                tableBody+='<tr><td>'+
                    products[i]['product_name']+'</td>'+'<td>'+
                    products[i]['price']+'</td>'+'<td>'+
                    products[i]['mrp']+'</td>'+
                    '<td>'+
                    '<a class="btn btn-sm btn-outline-dark"  href="'+showurl+'">'+
                            '<i class="fa fa-eye" ></i>'+
                        '</a>'+
                        '<a class="btn btn-sm btn-outline-dark" href="'+editurl+'">'+
                            '<i class="fa fa-pencil"></i>'+
                        '</a>'+
                        '<a class="btn btn-sm btn-outline-dark" href="'+deleteurl+'">'+
                            '<i class="fa fa-trash"></i>'+
                        '</a>'
                    +'</td></tr>';
            }
            $('#tbody').empty();
            console.log(tableBody)
            $('#tbody').append(tableBody);

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

        });
    </script>
@endsection


