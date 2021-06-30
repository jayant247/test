@extends('layouts.layout')
@section('content')
    <div class="card">
        <div class="card-header">
            Support Tickets
        </div>

        <div class="card-body">
            <div class="col-12">
                <div class="card card-primary card-tabs">
                    <div class="card-header p-0 pt-1">
                        <ul class="nav nav-tabs" id="custom-tabs-one-tab" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="custom-tabs-one-home-tab" data-toggle="pill" href="#custom-tabs-one-home" role="tab" aria-controls="custom-tabs-one-home" aria-selected="false">New </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="custom-tabs-one-profile-tab" data-toggle="pill" href="#custom-tabs-one-profile" role="tab" aria-controls="custom-tabs-one-profile" aria-selected="false">In Process</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link " id="custom-tabs-one-messages-tab" data-toggle="pill" href="#custom-tabs-one-messages" role="tab" aria-controls="custom-tabs-one-messages" aria-selected="true">Resolved</a>
                            </li>
{{--                            <li class="nav-item">--}}
{{--                                <a class="nav-link" id="custom-tabs-one-settings-tab" data-toggle="pill" href="#custom-tabs-one-settings" role="tab" aria-controls="custom-tabs-one-settings" aria-selected="false">Unpaid Orders</a>--}}
{{--                            </li>--}}
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content" id="custom-tabs-one-tabContent">
                            <div class="tab-pane fade active show" id="custom-tabs-one-home" role="tabpanel" aria-labelledby="custom-tabs-one-home-tab">
                                <div class="table-responsive">
                                    <table class=" table table-bordered table-striped table-hover datatable">
                                        <thead>
                                        <tr>
                                            <th width="10">

                                            </th>
                                            <th>
                                                Customer Name
                                            </th>
                                            <th>
                                                Subject
                                            </th>
                                            <th>
                                                Category
                                            </th>
                                            <th>
                                                Created At
                                            </th>
                                            <th>
                                                Action
                                            </th>

                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($tickets as $key => $ticket)
                                            @if($ticket->ticket_status_id==1 )
                                                <tr>
                                                    <td></td>
                                                    <td>{{$ticket->customer->name}}</td>
                                                    <td>{{$ticket->subject}}</td>
                                                    <th>{{$ticket->category}}</th>
{{--                                                    <th ><span class="badge badge-success">{{$ticket->couponDetails!==null?$ticket->couponDetails->code:''}}</span></th>--}}
                                                    <td>{{$ticket->created_at}}</td>
                                                    <td>
                                                        <a class="btn btn-xs btn-primary" href="{{ route('tickets.getMessage', $ticket->id) }}">
                                                            View
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endif

                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="custom-tabs-one-profile" role="tabpanel" aria-labelledby="custom-tabs-one-profile-tab">
                                <div class="table-responsive">
                                    <table class=" table table-bordered table-striped table-hover datatable">
                                        <thead>
                                        <tr>
                                            <th width="10">

                                            </th>
                                            <th>
                                                Customer Name
                                            </th>
                                            <th>
                                                Subject
                                            </th>
                                            <th>
                                                Category
                                            </th>
                                            <th>
                                                Assigned To
                                            </th>
                                            <th>
                                                Created At
                                            </th>
                                            <th>
                                                Action
                                            </th>

                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($tickets as $key => $ticket)
                                            @if($ticket->ticket_status_id==2 )
                                                <tr>
                                                    <td></td>
                                                    <td>{{$ticket->customer->name}}</td>
                                                    <td>{{$ticket->subject}}</td>
                                                    <th>{{$ticket->category}}</th>
                                                    <th>{{$ticket->admin->name}}</th>

{{--                                                                                                        <th ><span class="badge badge-success">{{$ticket->couponDetails!==null?$ticket->couponDetails->code:''}}</span></th>--}}
                                                    <td>{{$ticket->created_at}}</td>
                                                    <td>
                                                        <a class="btn btn-xs btn-primary" href="{{ route('tickets.getMessage', $ticket->id) }}">
                                                            View
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endif

                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="tab-pane fade " id="custom-tabs-one-messages" role="tabpanel" aria-labelledby="custom-tabs-one-messages-tab">
                                <table class=" table table-bordered table-striped table-hover datatable">
                                    <thead>
                                    <tr>
                                        <th width="10">

                                        </th>
                                        <th>
                                            Customer Name
                                        </th>
                                        <th>
                                            Subject
                                        </th>
                                        <th>
                                            Category
                                        </th>
                                        <th>
                                            Assigned To
                                        </th>
                                        <th>
                                            Created At
                                        </th>
                                        <th>
                                            Action
                                        </th>

                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($tickets as $key => $ticket)
                                        @if($ticket->ticket_status_id==3 )
                                            <tr>
                                                <td></td>
                                                <td>{{$ticket->customer->name}}</td>
                                                <td>{{$ticket->subject}}</td>
                                                <th>{{$ticket->category}}</th>
                                                <th>{{$ticket->admin->name??''}}</th>

                                                {{--                                                                                                        <th ><span class="badge badge-success">{{$ticket->couponDetails!==null?$ticket->couponDetails->code:''}}</span></th>--}}
                                                <td>{{$ticket->created_at}}</td>
                                                <td>
                                                    <a class="btn btn-xs btn-primary" href="{{ route('tickets.getMessage', $ticket->id) }}">
                                                        View
                                                    </a>
                                                </td>
                                            </tr>
                                        @endif

                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="tab-pane fade" id="custom-tabs-one-settings" role="tabpanel" aria-labelledby="custom-tabs-one-settings-tab">
                                <div class="table-responsive">

                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /.card -->
                </div>
            </div>
        </div>
        @endsection

        @section('scripts')
            @parent
            <script>

            </script>
@endsection
