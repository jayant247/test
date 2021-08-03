<div class="nk-sidebar">
    <div class="nk-nav-scroll">
        <ul class="metismenu" id="menu">
            <li class="nav-label">Dashboard</li>
            <li>
                <a href="{{route('dashboard.index')}}" aria-expanded="false">
                    <i class="icon-speedometer menu-icon"></i><span class="nav-text">Dashboard</span>
                </a>
            </li>
            <li class="nav-label">User Info</li>

            <li class="mega-menu mega-menu-sm">
                <a class="has-arrow" href="javascript:void()" aria-expanded="false">
                    <i class="icon-globe-alt menu-icon"></i><span class="nav-text">User Management</span>
                </a>
                <ul aria-expanded="false">
                    <li>
                        <a href="{{route('user.index')}}" aria-expanded="false">
                            <i class="icon-badge menu-icon"></i><span class="nav-text">Users</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{route('getCustomers')}}" aria-expanded="false">
                            <i class="icon-badge menu-icon"></i><span class="nav-text">Customers</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{route('role.index')}}" aria-expanded="false">
                            <i class="icon-badge menu-icon"></i><span class="nav-text">Roles</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{route('permission.index')}}" aria-expanded="false">
                            <i class="icon-badge menu-icon"></i><span class="nav-text">Permissions</span>
                        </a>
                    </li>
                </ul>
            </li>
            <!-- <li class="nav-label">Users</li> -->

            <li class="nav-label">Order Info</li>
            <li class="mega-menu mega-menu-sm">
                <a class="has-arrow" href="javascript:void()" aria-expanded="false">
                    <i class="icon-globe-alt menu-icon"></i><span class="nav-text">Orders</span>
                </a>
                <ul aria-expanded="false">
                    <li><a href="{{route('orderindex',1)}}">New Orders</a></li>
                    <li><a href="{{route('orderindex',6)}}">Confirmed Orders</a></li>
                    <li><a href="{{route('orderindex',2)}}">Shipped Orders</a></li>
                    <li><a href="{{route('orderindex',3)}}">Delivered Orders</a></li>
                    <li><a href="{{route('orderindex',4)}}">Cancelled</a></li>
                    <li><a href="{{route('orderindex',7)}}">Return Requested</a></li>
                    <li><a href="{{route('orderindex',9)}}">Returned</a></li>
                    <li><a href="{{route('orderindex',8)}}">Replacement Requested Orders</a></li>
                    <li><a href="{{route('orderindex',10)}}">Replacement In Progress</a></li>

                </ul>
            </li>
            <li class="nav-label">Product Info</li>
            <li class="mega-menu mega-menu-sm">
                <a class="has-arrow" href="javascript:void()" aria-expanded="false">
                    <i class="icon-globe-alt menu-icon"></i><span class="nav-text">Product Management</span>
                </a>
                <ul aria-expanded="false">
                    <li>
                        <a href="{{route('product.index')}}" aria-expanded="false">
                            <i class="icon-badge menu-icon"></i><span class="nav-text">Products</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{route('category.index')}}" aria-expanded="false">
                            <i class="icon-badge menu-icon"></i><span class="nav-text">Category</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{route('subcategory.index')}}" aria-expanded="false">
                            <i class="icon-badge menu-icon"></i><span class="nav-text">Sub-Category</span>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="nav-label">Offer Section</li>
            <li class="mega-menu mega-menu-sm">
                <a class="has-arrow" href="javascript:void()" aria-expanded="false">
                    <i class="icon-globe-alt menu-icon"></i><span class="nav-text">Offer Zone</span>
                </a>
                <ul aria-expanded="false">
                    <li>
                        <a href="{{route('promocode.index')}}" aria-expanded="false">
                            <i class="icon-badge menu-icon"></i><span class="nav-text">Promocodes</span>
                        </a>
                    </li>

                    <li>
                        <a href="{{route('giftcard.index')}}" aria-expanded="false">
                            <i class="icon-badge menu-icon"></i><span class="nav-text">Gift Cards</span>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="nav-label">Notifications</li>
            <li>
                <a href="{{route('notification.index')}}" aria-expanded="false">
                    <i class="icon-badge menu-icon"></i><span class="nav-text">Notifications</span>
                </a>
            </li>
            <li class="nav-label">Other Metadata</li>
            <li>
                <a href="{{route('pincode.index')}}" aria-expanded="false">
                    <i class="icon-badge menu-icon"></i><span class="nav-text">Delivery Pincodes</span>
                </a>
            </li>
            <li>
                <a href="{{route('tickets.index')}}" aria-expanded="false">
                    <i class="icon-badge menu-icon"></i><span class="nav-text">Support</span>
                </a>
            </li>
            <li>
                <a href="{{route('report.index')}}" aria-expanded="false">
                    <i class="icon-badge menu-icon"></i><span class="nav-text">Reports</span>
                </a>
            </li>
        </ul>
    </div>
</div>
