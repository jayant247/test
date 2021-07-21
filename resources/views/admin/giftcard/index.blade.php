@extends('layouts.layout')

@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.16/css/dataTables.bootstrap4.min.css" integrity="sha512-NDWv4n2v59EOoj+dDvXfD4uGGTCOXkLAnm+DhQtyYxpZL4lMSymTX5HD8i5NEcF+1YLBkgvByardYsJaA1W6MA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row d-flex justify-content-between">
                            <div>
                                <h4 class="card-title">Gift cards</h4>
                            </div>
                            <div>
                                <a href="{{route('giftcard.create')}}" class="btn btn-primary">Create</a>
                            </div>

                        </div>
                        @if(count($giftcards)>0)

                        <div class="table-responsive">

                            <table id="giftcard-table" class="table table-striped table-bordered zero-configuration">
                                <thead>
                                    <tr>
                                        <th>Title</th>
                                        <th>Gift Card Amount </th>
                                        <th>Validity</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @foreach($giftcards as $giftcard)
                                    <tr>
                                        <td>{{$giftcard->title}}</td>
                                        <td>{{$giftcard->gift_amount}}</td>
                                        <td>{{$giftcard->validity_days_from_purchase_date}}</td>
                                        <td>
                                            <form action="{{ route('giftcard.destroy',$giftcard->id) }}" method="POST">
                                                <a data-toggle="modal" onclick="openDetailsModal({{$giftcard->id}})"  class="btn btn-sm btn-outline-dark" >
                                                    <i class="fa fa-eye"></i>
                                                </a>
                                                <a class="btn btn-sm btn-outline-dark" href="{{route('giftcard.edit',$giftcard->id)}}" >
                                                    <i class="fa fa-pencil"></i>
                                                </a>
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" onclick="return confirm(' you want to delete?');" class="btn btn-sm btn-outline-dark">
                                                
                                                    <i class="fa fa-trash"></i>
                                                </a>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
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
   @if(count($giftcards)>0)
       @foreach($giftcards as $giftcard)
           <div class="modal fade bd-example-modal-lg" id="{{$giftcard->id}}" tabindex="-1" permission="dialog" aria-hidden="true">
               <div class="modal-dialog modal-lg">
                   <div class="modal-content">
                       <div class="modal-header">
                           <h5 class="modal-title">Gift Card Details</h5>
                           <button type="button" class="close" data-dismiss="modal"><span>&times;</span>
                           </button>
                       </div>
                       <div class="modal-body">

                           <div class="container-fluid">
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
                       <div class="modal-footer">
                           <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
{{--                           <button type="button" class="btn btn-primary">Save changes</button>--}}
                       </div>
                   </div>
               </div>
           </div>
       @endforeach
   @endif



@endsection

@section('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.16/js/jquery.dataTables.min.js" integrity="sha512-yCkOYsxpzPSpcbHspsH6A28Z0cgsfjJhlR78nPAfLLZSSV40tVN4VQ6ES/miqI/1z8a5FWVYwCF145+eyJx9Tw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.16/js/dataTables.bootstrap4.min.js" integrity="sha512-2wDq7VuYclJFDG5YbUbmOEWYtTEs/DwpKa9maNvC8gIhEHyR/rgh1BuyUrPZy00H8/DGlLAwbYwSpzCRV0dQJw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script>
        let giftcards = {!! $giftcards !!}
        if(giftcards.length>0){
            $(document).ready( function () {
                $('#giftcard-table').DataTable({
                    "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]]
                });
            } );
        }
        function openDetailsModal(id) {
            $("#"+id).modal()
        }
    </script>
@endsection


