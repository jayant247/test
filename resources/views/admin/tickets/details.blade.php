@extends('layouts.layout')
@section('content')
        <div class="card">
            <div class="card-header">
                Subject : {{$ticket->subject}}<br>
                @if($ticket->assigned_to!=null && $ticket->admin->name)
                <span class="badge badge-info">Assigned To :  {{$ticket->admin->name}}</span>
                @endif
                <span style="float: right" > <a class="btn  btn-primary" target="_blank"
{{--                                                href="{{ route('orders.show', $ticket->order_id) }}"--}}
                    >
                    View Order Details
                </a></span>
                @if($ticket->assigned_to==null)

                <span class="pr-2" style="float: right" > <a class="btn  btn-primary"  href="{{ route('tickets.assign', $ticket->id) }}">
                        Assign Ticket To Self
                    </a></span>

                @else
                    @can('assign_ticket_to_self_force')
                        <span class="pr-2" style="float: right" >
                            <a class="btn  btn-danger"  href="{{ route('tickets.assign', $ticket->id) }}">
                        Assign Ticket To Self
                        </a>
                        </span>
                    @endcan
                @endif
                @if($ticket->ticket_status_id==2)
                <span class="pr-2" style="float: right" > <a class="btn  btn-danger"  href="{{ route('tickets.close', $ticket->id) }}">
                        Close Ticket
                    </a></span>
                @elseif($ticket->ticket_status_id==3)
                <span class="pr-2" style="float: right" > <a class="btn  btn-danger"  href="{{ route('tickets.open', $ticket->id) }}">
                        Re-Open Ticket
                    </a></span>
                @endif
            </div>

            <div class="card-body">
                <div class="col-md-12">
                    Category : {{$ticket->category}}<br>
                    Customer Name : {{$ticket->customer->name}}<br>
                    Customer Mobile No : {{$ticket->customer->mobile_no}}<br>
                </div>
            </div>
        </div>

    <div class="row">
        <div class="col-md-12">
            <!-- The time line -->
            <div class="timeline">
                <!-- timeline time label -->
                <div class="mx-5">
                    @foreach($ticketMessages as $key=>$message)
                        <div class="card">
                            <div class="card-body">
                                @if($message->message_by=='admin')
                                    <div class="media media-reply">
                                        <img class="mr-3 circle-rounded" src="{{asset("img/support.png")}}" width="50" height="50" alt="Generic placeholder image">
                                        <div class="media-body">
                                            <div class="d-sm-flex justify-content-between mb-2">
                                                <h5 class="mb-sm-0">{{$message->supportAgent->name}} <small class="text-muted ml-3">{{$message->created_at}}</small></h5>

                                            </div>
                                            @if($message->message!=null)
                                                <p>{{$message->message}}</p>
                                            @endif

                                            @if($message->photo_url!=null)
                                                <img src="{{asset($message->photo_url)}}"  style="max-height: 400px">
                                            @endif
                                        </div>
                                    </div>
                                @else
                                    <div class="media media-reply">

                                        <div class="media-body">
                                            <div class="d-sm-flex justify-content-between mb-2">
                                                <h5 class="mb-sm-0">{{$ticket->customer->name}} <small class="text-muted ml-3">{{$message->created_at}}</small></h5>

                                            </div>

                                            @if($message->message!=null)
                                                <p>{{$message->message}}</p>
                                            @endif

                                            @if($message->photo_url!=null)
                                                <img src="{{asset($message->photo_url)}}" style="max-height: 400px">
                                            @endif
                                        </div><img class="mr-3 circle-rounded" src="{{asset("img/person.png")}}" width="50" height="50" alt="Generic placeholder image">
                                    </div>
                                @endif
                            </div>
                        </div>

                    @endforeach
                </div>
                @if($ticket->assigned_to==Auth::User()->id && $ticket->ticket_status_id==2)
                <div class="mx-5">
                    <div class="card">
                        <div class="card-body">
                            <form action="{{ route("tickets.addMessage") }}" method="POST" enctype="multipart/form-data" class="form-profile">
                                @csrf
                                <input hidden name="ticket_id" id="tickt_id" value="{{$ticket->id}}">
                                <div class="form-group">
                                    <textarea class="form-control" id="message" name="message" cols="30" rows="2" placeholder="Post a new message"></textarea>
                                    @if($errors->has('message'))
                                        <p class="help-block">
                                            {{ $errors->first('message') }}
                                        </p>
                                    @endif
                                </div>
                                <div class="d-flex align-items-center">
                                    <button class="btn btn-primary px-3 ml-4" type="submit">Send</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                @endif


            </div>
        </div>
        <!-- /.col -->
    </div>
@endsection
