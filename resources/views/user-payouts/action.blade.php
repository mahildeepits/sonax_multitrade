@if($model->is_paid == null && $model->is_requested == null)
    @if($model->net_amount >= 10)
        <a href="{{route('income.transfer.to.wallet', ['id' => encrypt($model->id)])}}" onclick="return confirm('Are you sure you want to transfer this income to your wallet?')" class="btn btn-info btn-sm">Transfer to Wallet</a>
    @else
        <span class="badge bg-secondary">Min ₹10 to Transfer</span>
    @endif
@elseif($model->is_paid != null)
    <span class="badge bg-success">Paid</span>
@elseif($model->is_requested != null)
    @if($model->income_type == 'withdrawal')
        <span class="badge bg-warning text-dark">Withdrawal Pending</span>
    @else
        <span class="badge bg-primary">Transferred to Wallet</span>
    @endif
@endif