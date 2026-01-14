@extends('layout.main')
@section('content')
<div class="page-content">
    <div class="card radius-10">
        <div class="card-header border-bottom-0 bg-transparent">
            <h5 class="mb-0">My EMIs</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>EMI Amount</th>
                            <th>Month</th>
                            <th>Created At</th>
                            <th>Paid At</th>
                            <th>Approved At</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($emis as $index => $emi)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>₹{{ number_format($emi->amount, 0) }}</td>
                            <td>{{ $emi->month }}</td>
                            <td>{{ $emi->created_at->format('d M Y') }}</td>
                            <td>{{ $emi->paid_at ? \Carbon\Carbon::parse($emi->paid_at)->format('d M Y') : '-' }}</td>
                            <td>{{ $emi->approved_at ? \Carbon\Carbon::parse($emi->approved_at)->format('d M Y') : '-' }}</td>
                            <td>
                                @if($emi->status == 'approved')
                                    <span class="badge bg-success">Paid & Verified</span>
                                @elseif($emi->status == 'submitted')
                                    <span class="badge bg-warning text-dark">Pending Verification</span>
                                @elseif($emi->status == 'rejected' || $emi->status == 'Rejected')
                                    <span class="badge bg-danger">Rejected</span>
                                @else
                                    <span class="badge bg-danger">Unpaid</span>
                                @endif
                            </td>
                            <td>
                                @if($emi->status == 'unpaid' || $emi->status == 'rejected' || $emi->status == 'Rejected')
                                    <button class="btn btn-main btn-sm" onclick="openPaymentModal({{ $emi->id }}, {{ $emi->amount }})">Pay</button>
                                @else
                                    <button class="btn btn-secondary btn-sm" disabled>Paid</button>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center">No EMIs found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="paymentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Pay EMI</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Please pay <strong>₹<span id="modalAmount"></span></strong> to the details below:</p>
                <div class="alert alert-info" style="color: #333; background-color: #d1ecf1; border-color: #bee5eb;">
                    <strong>Bank Name:</strong> {{ $bankDetails['bank_name'] }}<br>
                    <strong>Account No:</strong> {{ $bankDetails['account_number'] }}<br>
                    <strong>IFSC Code:</strong> {{ $bankDetails['ifsc_code'] }}<br>
                    <strong>Holder Name:</strong> {{ $bankDetails['account_holder'] }}<br>
                    <strong>UPI ID:</strong> {{ $bankDetails['upi_id'] }}
                </div>
                
                <form id="paymentForm">
                    @csrf
                    <input type="hidden" name="emi_id" id="modalEmiId">
                    <div class="mb-3">
                        <label for="screenshot" class="form-label">Upload Payment Screenshot</label>
                        <input class="form-control" type="file" id="screenshot" name="screenshot" accept="image/*" required>
                    </div>
                    <button type="submit" class="btn btn-main w-100">Submit Verification</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function openPaymentModal(id, amount) {
        $('#modalEmiId').val(id);
        $('#modalAmount').text(amount);
        $('#paymentModal').modal('show');
    }

    $('#paymentForm').on('submit', function(e) {
        e.preventDefault();
        var formData = new FormData(this);
        
        // Disable button
        var btn = $(this).find('button[type="submit"]');
        btn.prop('disabled', true).text('Submitting...');

        $.ajax({
            url: "{{ route('member.emi.pay') }}",
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if(response.success) {
                    // Use toastr if available (based on layout)
                    if(typeof toasterMessanger !== 'undefined') {
                        toasterMessanger.success('Success', response.message);
                    } else {
                        alert(response.message);
                    }
                    setTimeout(function(){
                         location.reload();
                    }, 1000);
                } else {
                     if(typeof toasterMessanger !== 'undefined') {
                        toasterMessanger.error('Error', response.message);
                    } else {
                        alert(response.message);
                    }
                    btn.prop('disabled', false).text('Submit Verification');
                }
            },
            error: function(xhr) {
                // Parse validation errors
                 if(xhr.status == 422) {
                    var errors = xhr.responseJSON.errors;
                    var errorMsg = '';
                     $.each(errors, function (key, value) {
                        errorMsg += value[0] + '\n';
                    });
                    alert(errorMsg);
                 } else {
                    alert('An error occurred');
                 }
                btn.prop('disabled', false).text('Submit Verification');
            }
        });
    });
</script>
@endsection
