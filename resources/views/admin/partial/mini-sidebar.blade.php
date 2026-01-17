<div class="secondary-sidebar">
    <div class="secondary-sidebar-bar">
        <a href="#" class="logo-box">Admin Panel</a>
    </div>
    <div class="secondary-sidebar-menu">
        <ul class="accordion-menu">
            <li class="active-page">
                <a href="{{ route('admin.dashboard') }}">
                    <i class="menu-icon icon-home4"></i><span>Dashboard</span>
                </a>
            </li>
            <!-- <li>
                <a href="javascript:void(0)">
                    <i class="menu-icon icon-apps"></i><span>Joining Kits</span><i class="accordion-icon fa fa-angle-left"></i>
                </a>
                <ul class="sub-menu">
                    <li><a href="{{ route('joining.kits') }}">Joining Kit</a></li>
                    <li><a href="{{ route('admin.joining.pins') }}">Generate Pins</a></li>
                    <li><a href="{{ route('admin.transfer.pins') }}">Transfer Pins</a></li>
                    <li><a href="{{ route('joining.pin.status') }}">Pin Status</a></li>
                    <li><a href="{{ route('admin.pin.history') }}">Pin History</a></li>
                </ul>
            </li> -->
            <li>
                <a href="{{ route('admin.all.payouts') }}">
                    <i class="menu-icon fa fa-money"></i><span>Income Report</span>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.requested.payouts') }}">
                    <i class="menu-icon fa fa-bank"></i><span>Withdrawal Report</span>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.emis.index') }}">
                    <i class="menu-icon fa fa-rupee"></i><span>Installment Requests</span>
                </a>
            </li>
            <li>
                <a href="{{ route('level-income.index') }}">
                    <i class="menu-icon fa fa-line-chart"></i><span>Level Income</span>
                </a>
            </li>
            {{-- <li>
                <a href="{{ route('admin.requested.payouts') }}">
                    <i class="menu-icon fa fa-money"></i><span>Payout Requests</span>
                </a>
            </li> --}}
            <li>
                <a href="{{ route('admin.users') }}">
                    <i class="menu-icon fa fa-users"></i><span>Users</span>
                </a>
            </li>
            {{-- <li>
                <a href="{{ route('pancard.report') }}">
                    <i class="menu-icon fa fa-id-card-o"></i><span>Pancard Report</span>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.pending.pins') }}">
                    <i class="menu-icon fa fa-id-card-o"></i><span>Pending Pins</span>
                </a>
            </li> --}}
            <li>
                <a href="{{ route('admin.edit.user') }}">
                    <i class="menu-icon fa fa-id-card-o"></i><span>User Profiles</span>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.edit.kyc') }}">
                    <i class="menu-icon fa fa-id-card-o"></i><span>Edit KYC Details</span>
                </a>
            </li>

{{--            <li>--}}
{{--                <a href="javascript:void(0)">--}}
{{--                   <i class="menu-icon fa fa-product-hunt"></i><span>Product</span><i class="accordion-icon fa fa-angle-left"></i>--}}
{{--                </a>--}}
{{--                <ul class="sub-menu">--}}
{{--                    <li><a href="{{ route('admin.product')}}">Add Product</a></li>--}}
{{--                    <li><a href="{{ route('admin.deleted.product') }}">Deleted Product</a></li>--}}
{{--                </ul>--}}
{{--            </li>--}}
            <li>
                <a href="{{ route('admin.charges') }}">
                    <i class="menu-icon fa fa-gears"></i><span>Admin Settings</span>
                </a>
            </li>
            <li>
                <a href="{{ route('website.settings') }}">
                    <i class="menu-icon fa fa-globe"></i><span>Website Settings</span>
                </a>
            </li>
            <li>
                <a href="{{ route('change.password') }}">
                    <i class="menu-icon fa fa-key"></i><span>Password</span>
                </a>
            </li>
            <li>
                <a href="{{ route('rewards.index') }}">
                    <i class="menu-icon fa fa-gift"></i><span>Rewards</span>
                </a>
            </li>
            {{-- <li>
                <a href="{{ route('admin.courses') }}">
                    <i class="menu-icon fa fa-book"></i><span>Courses</span>
                </a>
            </li>
            <li>
                <a href="{{ route('roles.index') }}">
                    <i class="menu-icon fa fa-gift"></i><span>Roles</span>
                </a>
            </li> --}}

        </ul>
    </div>
</div>
