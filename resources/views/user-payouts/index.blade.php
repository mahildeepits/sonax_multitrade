@extends('layout.main')
@section('content')
    <div class="page-content">
        <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
            <div class="breadcrumb-title pe-3">My Payouts</div>
            <div class="ps-3">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 p-0">
                        <li class="breadcrumb-item"><a href="{{ route('member.dashboard') }}"><i class="bx bx-home-alt"></i></a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">My Payouts</li>
                    </ol>
                </nav>
            </div>
            <div class="ms-auto">
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <div class="row mb-4 mx-2">
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="">From date</label>
                            <input type="date" name="from_date" id="from_date" class="form-control filter" id="from_date">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="">To date</label>
                            <input type="date" name="to_date" id="to_date" class="form-control filter" id="to_date">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="">Income Type</label>
                            {!! Form::select('income_type',['direct_income' => 'Direct Income'],null,['class' => 'form-control filter','placeholder' => 'All','id' => 'income_type']) !!}
                        </div>
                    </div>
                    @if ($type == null)
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="">List type</label>
                                {!! Form::select('status',['requested' => 'Requested','not_requested' => 'Not Requested'],null,['class' => 'form-control filter','placeholder' => 'All','id' => 'list_type']) !!}
                            </div>
                        </div>
                    @endif
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="">Payment Status</label>
                            {!! Form::select('status',['paid' => 'Paid','not_paid' => 'Not paid'],null,['class' => 'form-control filter','placeholder' => 'All','id' => 'payment_status']) !!}
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 text-nowrap">
                        {!! $dataTable->table() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    @parent
    {!! $dataTable->scripts() !!}
    <script>
        const userpayoutsDataTable = $('#userpayouts-table');
        $(document).ready(function(){
            userpayoutsDataTable.parent().addClass('over-flow-scroll');
        });
        userpayoutsDataTable.on('preXhr.dt',function(e, settings,data){
            data.from = $('#from_date').val();
            data.to = $('#to_date').val();
            data.type = $('#list_type').val();
            data.status = $('#payment_status').val();
            data.income_type = $('#income_type').val();
        });
        $(function(){
            $('.filter').on('change',function(){
                userpayoutsDataTable.DataTable().ajax.reload();
            });
        });
    </script>
@endsection
