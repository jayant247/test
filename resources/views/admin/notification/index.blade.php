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
                                <h4 class="card-title">Notifications</h4>
                            </div>
                            <div>
                                <a href="{{route('notification.create')}}" class="btn btn-primary">Create</a>
                            </div>

                        </div>
                        @if(count($notifications)>0)

                        <div class="table-responsive">

                            <table id="notification-table" class="table table-striped table-bordered zero-configuration">
                                <thead>
                                    <tr>
                                        <th>User Type</th>
                                        <th>Notification Header</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @foreach($notifications as $notification)
                                    <tr>
                                        <td>{{$notification->user_type}}</td>
                                        <td>{{$notification->heading}}</td>
                                        <td>
                                            <form action="{{ route('notification.destroy',$notification->id) }}" method="POST">
                                                <a data-toggle="modal" onclick="openDetailsModal({{$notification->id}})"  class="btn btn-sm btn-outline-dark" >
                                                    <i class="fa fa-eye"></i>
                                                </a>
                                                <a class="btn btn-sm btn-outline-dark" href="{{route('notification.edit',$notification->id)}}" >
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
   @if(count($notifications)>0)
       @foreach($notifications as $notification)
           <div class="modal fade bd-example-modal-lg" id="{{$notification->id}}" tabindex="-1" permission="dialog" aria-hidden="true">
               <div class="modal-dialog modal-lg">
                   <div class="modal-content">
                       <div class="modal-header">
                           <h5 class="modal-title">Notification Details</h5>
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
                                           <td>User Type</td>
                                           <td>{{$notification->user_type}}</td>
                                       </tr>
                                       @if($notification->user_type === 'Specific')
                                           <tr>
                                               <td>Registered From</td>
                                               <td>{{$notification->registered_from}}</td>
                                           </tr>
                                           <tr>
                                               <td>Registered Till</td>
                                               <td>{{$notification->registered_till}}</td>
                                           </tr>
                                        @endif
                                       <tr>
                                           <td>Header</td>
                                           <td>{{$notification->heading}}</td>
                                       </tr>
                                       <tr>
                                           <td>Mobile Notification</td>
                                           <td>{{$notification->is_mobile}}</td>
                                       </tr>
                                       @if($notification->is_mobile === 1)
                                            <tr>
                                               <td>Mobile Body</td>
                                               <td>{{$notification->mobile_body}}</td>
                                            </tr>
                                            <tr>
                                               <td>Mobile Image</td>

                                               <td><a href="{{env('APP_URL').$notification->mobile_image}}" target="_blank">
                                                   <img loading="lazy" style="width: 100px;max-height: 100px;" src="{{env('APP_URL').$notification->mobile_image}}">
                                               </a></td>
                                            </tr>
                                       @endif
                                       <tr>
                                           <td>Mail Notification</td>
                                           <td>{{$notification->is_mail}}</td>
                                       </tr>
                                       @if($notification->is_mail === 1)
                                            <tr>
                                               <td>Mail Body</td>
                                               <td>{{$notification->mail_body}}</td>
                                            </tr>
                                       @endif
                                       <tr>
                                           <td>SMS Notification</td>
                                           <td>{{$notification->is_sms}}</td>
                                       </tr>
                                       @if($notification->is_sms === 1)
                                            <tr>
                                               <td>SMS Body</td>
                                               <td>{{$notification->sms_body}}</td>
                                            </tr>
                                       @endif
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
        let notifications = {!! $notifications !!}
        if(notifications.length>0){
            $(document).ready( function () {
                $('#pincode-table').DataTable({
                    "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]]
                });
            } );
        }
        function openDetailsModal(id) {
            $("#"+id).modal()
        }
    </script>
@endsection


