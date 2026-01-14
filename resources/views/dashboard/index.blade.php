@extends('layout.main')
@section('content')
    <div class="page-content bg-watermark">
        <div class="row">
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
                    <div class="card radius-10 bg-gradient-orange">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <h5 class="mb-0 text-white"> ₹
                                    {{ $user->payouts()->sum('amount') }}
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
                    <div class="card radius-10 bg-gradient-ohhappiness">
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
                                        ₹ {{ $user->payouts()->where('income_type', 'direct_income')->sum('amount') }}
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
                                        ₹ {{ $user->payouts()->where('income_type', 'like', 'level%')->sum('amount') }} 
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
                                        ₹ {{ $user->payouts()->where('income_type', 'reward')->sum('amount') }}
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
    <script type="text/javascript">
        function copyToClipboard(elementId) {
            var element = document.getElementById(elementId);
            var text = element.textContent;
            var textArea = document.createElement("textarea");
            textArea.value = text;
            document.body.appendChild(textArea);
            textArea.select();
            document.execCommand("copy");
            document.body.removeChild(textArea);
        }
    </script>
@endsection

