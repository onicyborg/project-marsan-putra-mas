<div class="sidebar-content">
    <ul class="nav nav-secondary">
        @if (Auth::user()->role == 'admin')
            <li class="nav-item">
                <a href="/">
                    <i class="fas fa-home"></i>
                    <p>Dashboard</p>
                </a>
            </li>

            <li class="nav-section">
                <span class="sidebar-mini-icon">
                    <i class="fa fa-ellipsis-h"></i>
                </span>
                <h4 class="text-section">Main Menu</h4>
            </li>
            <li class="nav-item">
                <a data-bs-toggle="collapse" href="#base">
                    <i class="fas fa-layer-group"></i>
                    <p>Master Data</p>
                    <span class="caret"></span>
                </a>
                <div class="collapse" id="base">
                    <ul class="nav nav-collapse">
                        <li>
                            <a href="/members">
                                <span class="sub-item">Member</span>
                            </a>
                        </li>
                        <li>
                            <a href="/transaction">
                                <span class="sub-item">Transaction</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
        @else
            <li class="nav-item">
                <a href="/">
                    <i class="fas fa-home"></i>
                    <p>Dashboard</p>
                </a>
            </li>

            <li class="nav-section">
                <span class="sidebar-mini-icon">
                    <i class="fa fa-ellipsis-h"></i>
                </span>
                <h4 class="text-section">Main Menu</h4>
            </li>

            <li class="nav-item">
                <a href="/transaction">
                    <i class="fas fa-layer-group"></i>
                    <p>Transaksi</p>
                </a>
            </li>
        @endif
    </ul>
</div>
