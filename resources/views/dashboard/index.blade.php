@extends('layout.main')
@section('content')
    <div class="page-content bg-watermark">
        <div class="row">
        @if(isset($announcement) && $announcement != null)
            <div class="col-md-12 mb-3">
                <div class="d-flex align-items-center bg-white p-2 rounded shadow-sm">
                    <i class='bx bxs-volume-full text-danger fs-4 me-2'></i>   
                    <marquee scrollamount="10" onmouseover="this.stop();" onmouseout="this.start();">
                        <p class="m-0 text-dark" style="font-size: 16px;"><b>{{ ucwords($announcement->title) ?? '' }} : </b> {{ ucfirst($announcement->description) }}</p>
                    </marquee>
                </div>
            </div>
        @endif
            <div class="col-md-12 text-center">
                <h3>Welcome User, {{ auth()->guard('member')->user()->name }}</h3>
            </div>
            <div class="col-md-12 mt-2 text-center">
                {{-- <img src="{{ asset('images/54.png') }}" width="200" /> --}}
                <!-- <h3>{{ config('app.name') }}</h3> -->
            </div>
        </div>
        @if(auth('member')->user() !== null)
            @php
                $user = auth('member')->user();
            @endphp
            <div class="row mt-2">
                <div class="col-sm">
                    {{-- <a href="{{ $user->direct_bonus_income >= 10 ? route('income.transfer.to.wallet', ["income_type" => "direct", "amount" => $user->direct_bonus_income]) : 'javascript:;' }}" 
                        onclick="{{ $user->direct_bonus_income >= 10 ? "return confirm('Are you sure? You want to transfer your Direct Income to Wallet?')" : "alert('Minimum transfer amount is ₹10'); return false;" }}"> --}}
                        <div class="card radius-10 bg-gradient-ibiza">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <h5 id="invitation_link_left" class="text-white" style="max-width: 170px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                        {{ createShortLink($user->member_id) }}</h5>
                                    <div class="ms-auto">
                                        <a href="javascript:void(0)"
                                            onclick="copyToClipboard('invitation_link_left')"
                                            class="text-white">
                                            <i class='bx bxs-copy fs-3 text-white'></i>
                                        </a>
                                    </div>
                                </div>
                                <div class="d-flex align-items-end justify-content-between text-white">
                                    <p class="mb-0">Referal Link</p>
                                </div>
                            </div>
                        </div>
                    {{-- </a> --}}
                </div>
                <div class="col-sm">
                    <div class="card radius-10 bg-gradient-ohhappiness">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <h5 class="mb-0 text-white"> ₹
                                    {{ $user->total_income }}
                                </h5>
                                <div class="ms-auto">
                                    <i class='bx bx-recycle fs-3 text-white'></i>
                                </div>
                            </div>
                            <div class="d-flex align-items-center text-white">
                                <p class="mb-0">Total Income</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm">
                    <div class="card radius-10 bg-gradient-orange">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <h5 class="mb-0 text-white">
                                    ₹ {{ \App\Models\User::getWalletAmount() }}
                                </h5>
                                <div class="ms-auto">
                                    <i class='bx bx-money fs-3 text-white'></i>
                                </div>
                            </div>
                            <div class="d-flex align-items-center text-white">
                                <p class="mb-0" title="Total Income Without Charges">Wallet Balance</p>
                            </div>
                        </div>
                    </div>
                </div>
                
            </div>
            <div class="row "> {{--- row-cols-1 row-cols-md-2 row-cols-xl-4 --}}
                <div class="col-sm">
                    {{-- <a href="{{ $user->direct_bonus_income >= 10 ? route('income.transfer.to.wallet', ["income_type" => "direct", "amount" => $user->direct_bonus_income]) : 'javascript:;' }}" 
                        onclick="{{ $user->direct_bonus_income >= 10 ? "return confirm('Are you sure? You want to transfer your Direct Income to Wallet?')" : "alert('Minimum transfer amount is ₹10'); return false;" }}"> --}}
                        <div class="card radius-10 bg-gradient-ibiza">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <h5 class="mb-0 text-white">
                                        ₹ {{ $user->direct_bonus_income }}
                                    </h5>
                                    <div class="ms-auto">
                                        <i class='bx bx-coin fs-3 text-white'></i>
                                    </div>
                                </div>
                                <div class="d-flex align-items-end justify-content-between text-white">
                                    <p class="mb-0">DIRECT INCOME</p>

                                </div>
                            </div>
                        </div>
                    {{-- </a> --}}
                </div>
                <div class="col-sm">
                    {{-- <a href="{{ $user->level_income >= 10 ? route('income.transfer.to.wallet', ["income_type" => "level", "amount" => $user->level_income]) : 'javascript:;' }}" 
                        onclick="{{ $user->level_income >= 10 ? "return confirm('Are you sure? You want to transfer your Team Performance Income to Wallet?')" : "alert('Minimum transfer amount is ₹10'); return false;" }}"> --}}
                        <div class="card radius-10 bg-gradient-ohhappiness">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <h5 class="mb-0 text-white">
                                        ₹ {{ $user->level_income }} 
                                    </h5>
                                    <div class="ms-auto">
                                        <i class='bx bx-money fs-3 text-white'></i>
                                    </div>
                                </div>
                                <div class="d-flex align-items-end justify-content-between text-white">
                                    <p class="mb-0">TEAM INCOME</p>

                                </div>
                                    {{-- <div class="d-flex align-items-end">
                                       
                                    </div> --}}
                            </div>
                        </div>
                    {{-- </a> --}}
                </div>

                <div class="col-sm">
                    {{-- <a href="{{ $user->autopool_income >= 10 ? route('income.transfer.to.wallet', ['income_type' => 'autopool', 'amount' => $user->autopool_income]) : 'javascript:;' }}"
                        onclick="{{ $user->autopool_income >= 10 ? "return confirm('Are you sure? You want to transfer your Autopool Income to Wallet?')" : "alert('Minimum transfer amount is ₹10'); return false;" }}"> --}}
                        <div class="card radius-10 bg-gradient-orange">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <h5 class="mb-0 text-white">
                                        ₹ {{ $user->autopool_income }}
                                    </h5>
                                    <div class="ms-auto">
                                        <i class='bx bx-recycle fs-3 text-white'></i>
                                    </div>
                                </div>
                                <div class="d-flex align-items-end justify-content-between text-white">
                                    <p class="mb-0">REWARD INCOME</p>

                                </div>
                            </div>
                        </div>
                    {{-- </a> --}}
                </div>

                <div class="col-sm">
                    <div class="card radius-10 bg-gradient-deepblue">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <h5 class="mb-0 text-white">
                                      {{ $user->allChilds()->where('id','!=',$user->id)->count() }}
                                </h5>
                                <div class="ms-auto">
                                    <i class='bx bx-arrow-from-top fs-3 text-white'></i>
                                </div>
                            </div>
                            <div class="d-flex align-items-center text-white">
                                <p class="mb-0" title="Total Income With Charges">TOTAL DOWNLINE</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection
@section('css')
    @parent
    <style>
        .bg-watermark:before{
            background: url(../images/54.png) no-repeat;
            background-size: contain;
            background-position: center;
            opacity: 0.1;
            content: ' ';
            height: 600px;
            width: 80%;
            display: inline-block;
            position: absolute;
        }
    </style>
@endsection
@section('scripts')
@parent
    <script>
        
        function copyToClipboard(id) {
            const textElement = document.getElementById(id);
            if (!textElement) return alert("Element not found");

            const text = textElement.innerText || textElement.value;

            // Modern secure clipboard API
            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(text).then(() => {
                    toastrMessage('Success', 'Copied to Clipboard', 'success');
                }).catch(err => {
                    toastrMessage('Failed to copy', err, 'error');
                });
            } else {
                // Fallback for insecure contexts or unsupported browsers
                const textarea = document.createElement("textarea");
                textarea.value = text;
                textarea.style.position = "fixed"; // Prevent scrolling to bottom
                document.body.appendChild(textarea);
                textarea.focus();
                textarea.select();

                try {
                    const success = document.execCommand('copy');
                    if (success) {
                        toastrMessage('Success', 'Copied to Clipboard', 'success')
                    } else {
                        toastrMessage('Failed to copy', '', 'error');
                    }
                } catch (err) {
                    toastrMessage('Failed to copy', err, 'error');
                }

                document.body.removeChild(textarea);
            }
        }
    </script>
@endsection

