@extends('admin.layouts.admin')
@section('title', 'EMI Requests')
@section('content')
<div id="main-wrapper">
    <div class="content-header">
        <h1 class="page-title">EMI Requests</h1>
    </div>
    
    <div class="card">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.emis.index') }}">
                <div class="row">
                    <div class="col-md-3">
                        <input type="text" name="search" class="form-control" placeholder="Search User Name/ID" value="{{ request('search') }}">
                    </div>
                    <div class="col-md-3">
                        <select name="status" class="form-control">
                            <option value="">All Status</option>
                            <option value="unpaid" {{ request('status') == 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                            <option value="submitted" {{ request('status') == 'submitted' ? 'selected' : '' }}>Submitted (Pending)</option>
                            <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary">Filter</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    <div class="card mt-3">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>User</th>
                            <th>Amount</th>
                            <th>Month</th>
                            <th>Status</th>
                            <th>Screenshot</th>
                            <th>Paid At</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($emis as $index => $emi)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                {{ $emi->user->name }}<br>
                                <small>ID: {{ $emi->user->member_id }}</small>
                            </td>
                            <td>{{ number_format($emi->amount, 0) }}</td>
                            <td>{{ $emi->month }}</td>
                            <td>
                                @if($emi->status == 'approved')
                                    <span class="badge badge-success">Approved</span>
                                @elseif($emi->status == 'submitted')
                                    <span class="badge badge-warning">Pending</span>
                                @elseif($emi->status == 'rejected' || $emi->status == 'Rejected')
                                    <span class="badge badge-danger">Rejected</span>
                                @else
                                    <span class="badge badge-secondary">Unpaid</span>
                                @endif
                            </td>
                            <td>
                                @if($emi->screenshot)
                                    <a href="{{ asset('storage/'.$emi->screenshot) }}" target="_blank">View Proof</a>
                                @else
                                    -
                                @endif
                            </td>
                            <td>{{ $emi->paid_at ? \Carbon\Carbon::parse($emi->paid_at)->format('d M Y h:i A') : '-' }}</td>
                            <td>
                                @if($emi->status == 'submitted')
                                    <button class="btn btn-success btn-sm verify-btn" data-id="{{ $emi->id }}">Verify</button>
                                    <button class="btn btn-danger btn-sm reject-btn" data-id="{{ $emi->id }}">Reject</button>
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center">No Records Found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4 d-flex justify-content-center">
                {{ $emis->withQueryString()->links('pagination::bootstrap-4') }}
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
@parent
<script>
    $('.verify-btn').click(function(){
        if(!confirm('Are you sure you want to verify this payment?')) return;
        var id = $(this).data('id');
        var btn = $(this);
        $.ajax({
            url: "{{ route('admin.emi.verify') }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                id: id
            },
            success: function(res){
                if(res.success){
                    if(typeof toastr !== 'undefined') toastr.success(res.message); else alert(res.message);
                    location.reload();
                } else {
                    if(typeof toastr !== 'undefined') toastr.error(res.message); else alert(res.message);
                }
            }
        });
    });

    $('.reject-btn').click(function(){
        if(!confirm('Are you sure you want to REJECT this payment?')) return;
        var id = $(this).data('id');
        $.ajax({
            url: "{{ route('admin.emi.reject') }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                id: id
            },
            success: function(res){
                if(res.success){
                    if(typeof toastr !== 'undefined') toastr.success(res.message); else alert(res.message);
                    location.reload();
                } else {
                    if(typeof toastr !== 'undefined') toastr.error(res.message); else alert(res.message);
                }
            }
        });
    });
</script>
@endsection
