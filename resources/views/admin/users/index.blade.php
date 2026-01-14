@extends('admin.layouts.admin')
@section('title','MLM Software - Admin Panel')
@section('content')
    @if(request()->has('kyc_details'))
        <div class="modal fade" id="kyc-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">KYC Details</h5>
                        <button type="button" onclick="window.location.href='{{ route('admin.users') }}'" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        @if($kycDetails != null)
                            <div class="row">
                                <div class="col-md-6 form-group">
                                    <label>KYC Type</label>
                                    @switch($kycDetails->kyc_type)
                                        @case('1')
                                            <h6>Adhaar Card</h6>
                                        @break
                                        @case('2')
                                            <h6>Voter ID</h6>
                                        @break
                                        @case('3')
                                            <h6>Driving License</h6>
                                        @break
                                    @endswitch
                                </div>
                                <div class="col-md-6 form-group">
                                    <label>Detail</label>
                                    <h6>{{ $kycDetails->card_no }}</h6>
                                </div>
                                @if($kycDetails->card_front != '')
                                    <div class="col-md-6 form-group">
                                        <label>Card Front</label>
                                        <img src="{{ asset('images/kyc_docs/'.$kycDetails->card_front) }}" width="150" />
                                    </div>
                                @endif
                                @if($kycDetails->card_back != '')
                                    <div class="col-md-6 form-group">
                                        <label>Card Front</label>
                                        <img src="{{ asset('images/kyc_docs/'.$kycDetails->card_back) }}" width="150s" />
                                    </div>
                                @endif
                            </div>
                        @else
                            <div class="row">
                                <div class="col-md-12 text-center">
                                    <i>No details found</i>
                                </div>
                            </div>
                        @endif
                    </div>
                    @if($kycDetails != null)
                        <div class="modal-footer">
                            <a href="{{ route('admin.edit.kyc',['user_id'=>$kycDetails->user_id]) }}" class="btn btn-info">Edit Details</a>
                            <a href="{{ route('admin.users') }}" class="btn btn-secondary">Close</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif
    <div id="main-wrapper">
        <div class="content-header">
            <h1 class="page-title">Users</h1>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body table-responsive">
                        <table class="table table-bordered table-striped static-datatable-users">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Username</th>
                                    <th>Password</th>
                                    <th>Sponsor</th>
                                    <th>Used Pin</th>
                                    <th>Mobile</th>
                                    <th width="200">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($users as $key => $user)
                                    <tr>
                                        <td>{{ $loop->index+1 }}</td>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->member_id }}</td>
                                        <td title="{{ \Illuminate\Support\Facades\Crypt::decrypt($user->enc_password) }}">{{ \Illuminate\Support\Facades\Crypt::decrypt($user->enc_password) }}</td>
                                        <td>{{ $user->sponsor_id }}</td>
                                        <td>{{ $user->epin }}</td>
                                        <td>{{ $user->mobile }}</td>
                                        <td>
                                            <a target="_blank" href="{{ route('admin.edit.user',['member_id'=>$user->member_id]) }}" class="btn btn-info btn-xs">Edit</a>
                                            <a href="{{ route('admin.users',['kyc_details'=>$user->id]) }}" class="btn btn-danger btn-xs">KYC</a>
                                            @if($user->is_blocked)
                                                <a href="{{ route('admin.users',['unblock_user'=>$user->id]) }}" class="btn btn-danger btn-xs">Un-Block</a>
                                            @else
                                                <a href="{{ route('admin.users',['block_user'=>$user->id]) }}" class="btn btn-warning btn-xs">Block</a>
                                            @endif
                                            @if($user->is_paid == 0 && $user->user_icon == 'golden.png')
                                                <a href="{{ route('set-user-to-paid',$user->id) }}" onclick="return confirm('Are you sure to set as paid user ?')" class="btn btn-dark btn-xs">Paid</a>
                                            @endif

{{--                                            @if($user->is_franchise)--}}
{{--                                                <a href="{{ route('admin.franchise.user',['user_id' => $user->id, 'type' => 'remove']) }}" class="btn btn-danger btn-xs mt-1s" title="Remove as a Franchise user"><i class="fa fa-user-times" aria-hidden="true"></i></a>--}}
{{--                                            @else--}}
{{--                                                <a href="{{ route('admin.franchise.user',['user_id' => $user->id, 'type' => 'make']) }}" class="btn btn-info btn-xs mt-1s" title="Make as a Franchise user"><i class="fa fa-user-plus" aria-hidden="true"></i></a>--}}
{{--                                            @endif--}}

                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
{{--                        <div class="float-right">--}}
{{--                            {!! $users->render('vendor.pagination.default') !!}--}}
{{--                        </div>--}}
                    </div>
                </div>
            </div>
        </div>
    </div><!-- Main Wrapper -->
@endsection

@section('scripts')
    @parent
    <script>
        $(document).ready(function(){
            @if(request()->q)
                setTimeout(function(){
                    $('input[type=search]').val('{{ request()->q }}').trigger('keyup');
                },500);
            @endif
            @if(request()->has('kyc_details'))
                $('#kyc-modal').modal('show');
            @endif
        });
    </script>
@endsection
