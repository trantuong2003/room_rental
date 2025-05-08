@extends('layouts.admin')

@section('content')
<div class="head-title">
    <div class="left">
        <h1>Account Management</h1>
        <ul class="breadcrumb">
            <li>
                <a href="#">Dashboard</a>
            </li>
            <li><i class='bx bx-chevron-right'></i></li>
            <li>
                <a class="active" href="#">Account Management</a>
            </li>
        </ul>
    </div>
</div>

<div class="table-data">
    <div class="order">
        <div class="head">
            <h3>List of accounts</h3>
            <i class='bx bx-search'></i>
            <i class='bx bx-filter'></i>
        </div>

        <div class="admin-account-filter">
            <a href="{{ route('account.profile', ['filter' => 'all']) }}" class="filter-btn {{ $filter == 'all' ? 'active' : '' }}">
                All ({{ $countAll }})
            </a>
            <a href="{{ route('account.profile', ['filter' => 'landlord']) }}" class="filter-btn {{ $filter == 'landlord' ? 'active' : '' }}">
                Landlord ({{ $countLandlord }})
            </a>
            <a href="{{ route('account.profile', ['filter' => 'customer']) }}" class="filter-btn {{ $filter == 'customer' ? 'active' : '' }}">
                Customer ({{ $countCustomer }})
            </a>
        </div>

        <div class="admin-account-container">
            <table class="admin-account-table">
                <thead>
                    <tr>
                        <th>STT</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Address</th>
                        <th>CMND/CCCD</th>
                        <th>Certificate of Authenticity</th>
                        <th>Role</th>
                        <th>Email Authentication</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $index => $user)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->phone ?? 'Not updated yet' }}</td>
                        <td>{{ $user->address ?? 'Not updated yet' }}</td>
                        <td>{{ $user->government_id ?? 'Not updated yet' }}</td>
                        <td>
                            @if($user->proof)
                                <span class="view-proof" onclick="showProof('{{ asset('storage/' . $user->proof) }}', '{{ $user->name }}')">Watch the paper</span>
                            @else
                            Not updated yet
                            @endif
                        </td>
                        <td>
                            <span class="role-badge role-{{ strtolower($user->role) }}">
                                {{ ucfirst($user->role) }}
                            </span>
                        </td>
                        <td>
                            @if ($user->email_verified_at)
                                <span class="verified"><i class='bx bx-check-circle'></i> Verified</span>
                            @else
                                <span class="not-verified"><i class='bx bx-x-circle'></i> Not verified</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal for viewing proof documents -->
<div id="proofModal" class="proof-modal">
    <div class="proof-modal-content">
        <div class="proof-modal-header">
            <h2 class="proof-modal-title">Certificate of Authenticity - <span id="proofUserName"></span></h2>
            <span class="proof-modal-close" onclick="closeProofModal()">&times;</span>
        </div>
        <div class="proof-modal-body">
            <img id="proofImage" src="/placeholder.svg" alt="Giấy tờ xác thực">
        </div>
    </div>
</div>

<script>
    // Function to show proof modal
    function showProof(proofUrl, userName) {
        const modal = document.getElementById('proofModal');
        const proofImage = document.getElementById('proofImage');
        const proofUserName = document.getElementById('proofUserName');
        
        proofImage.src = proofUrl;
        proofUserName.textContent = userName;
        modal.style.display = 'block';
    }
    
    // Function to close proof modal
    function closeProofModal() {
        const modal = document.getElementById('proofModal');
        modal.style.display = 'none';
    }
    
    // Close modal when clicking outside of it
    window.onclick = function(event) {
        const modal = document.getElementById('proofModal');
        if (event.target == modal) {
            modal.style.display = 'none';
        }
    }
</script>
@endsection