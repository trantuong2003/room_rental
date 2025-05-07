@extends('layouts.admin')

@section('content')
<div class="head-title">
    <div class="left">
        <h1>Subscription Packages</h1>
        <ul class="breadcrumb">
            <li>
                <a href="#">Dashboard</a>
            </li>
            <li><i class='bx bx-chevron-right'></i></li>
            <li>
                <a class="active" href="#">Subscription Packages</a>
            </li>
        </ul>
    </div>
</div>

<div class="subscription-container">
    <div class="subscription-header">
        <h2>Manage Packages</h2>
        <button class="button button-create" onclick="toggleForm(true)">
            <i class='bx bx-plus'></i> Create New
        </button>
    </div>
    
    <table class="subscription-table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Price</th>
                <th>Duration</th>
                <th>Posts</th>
                <th>Descriptions</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($subscriptions as $subscription)
            <tr>
                <td>{{ $subscription->package_name }}</td>
                <td>${{ number_format($subscription->price, 2) }}</td>
                <td>{{ $subscription->duration_days }} days</td>
                <td>{{ $subscription->post_limit }}</td>
                <td>{{ $subscription->description }}</td>
                <td>
                    <button class="button button-edit" onclick="editSubscription({{ $subscription->id }})">
                        <i class='bx bx-edit'></i> Edit
                    </button>
                    <form action="{{ route('subscriptions.destroy', $subscription->id) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="button button-delete" onclick="return confirm('Are you sure you want to delete?')">
                            <i class='bx bx-trash'></i> Delete
                        </button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Form Overlay -->
<div class="overlay" id="overlay" onclick="toggleForm(false)"></div>
<div class="subscription-form-container" id="subscriptionForm">
    <button class="close-button" onclick="toggleForm(false)">X</button>
    <h3 id="formTitle">Create Subscription Package</h3>
    <form id="subscriptionFormElement" method="POST">
        @csrf
        <input type="hidden" id="id" name="id">
        <input type="hidden" id="_method" name="_method" value="POST">
        
        <div class="form-group">
            <label for="package_name" class="form-label">Name</label>
            <input type="text" id="package_name" name="package_name" class="form-control" required>
        </div>
        
        <div class="form-group">
            <label for="price" class="form-label">Price</label>
            <input type="number" id="price" name="price" class="form-control" required>
        </div>
        
        <div class="form-group">
            <label for="duration_days" class="form-label">Duration (days)</label>
            <input type="number" id="duration_days" name="duration_days" class="form-control" required>
        </div>
        
        <div class="form-group">
            <label for="post_limit" class="form-label">Number of Posts</label>
            <input type="number" id="post_limit" name="post_limit" class="form-control" required>
        </div>
        
        <div class="form-group">
            <label for="description" class="form-label">Description</label>
            <textarea id="description" name="description" class="form-control" rows="3" required></textarea>
        </div>
        
        <button type="submit" class="button button-create">
            <i class='bx bx-save'></i> Save
        </button>
    </form>
</div>

<script>
function toggleForm(show, isEdit = false) {
    const form = document.getElementById("subscriptionForm");
    const overlay = document.getElementById("overlay");
    const formTitle = document.getElementById("formTitle");
    
    if (show) {
        form.style.display = "block";
        overlay.style.display = "block";
        
        if (!isEdit) {
            resetForm();
            formTitle.textContent = "Create Subscription Package";
        } else {
            formTitle.textContent = "Edit Subscription Package";
        }
    } else {
        form.style.display = "none";
        overlay.style.display = "none";
        resetForm();
    }
}

function resetForm() {
    document.getElementById("id").value = "";
    document.getElementById("package_name").value = "";
    document.getElementById("price").value = "";
    document.getElementById("duration_days").value = "";
    document.getElementById("post_limit").value = "";
    document.getElementById("description").value = "";
    
    const form = document.getElementById("subscriptionFormElement");
    form.action = "/admin/subscription";
    form.method = "POST";
    document.getElementById("_method").value = "POST";
}

function editSubscription(id) {
    fetch(`/admin/subscription/${id}/edit`)
        .then(response => response.json())
        .then(subscription => {
            if (!subscription || subscription.error) {
                console.error("Error: Subscription data not found.");
                alert("Subscription not found!");
                return;
            }
            
            // Process description
            let description = subscription.description.trim();
            if (!description.endsWith(".")) {
                description += ".";
            }
            description = description.replace(/\.\s+/g, ".\n");
            
            // Fill form data
            document.getElementById("id").value = subscription.id;
            document.getElementById("package_name").value = subscription.package_name;
            document.getElementById("price").value = subscription.price;
            document.getElementById("duration_days").value = subscription.duration_days;
            document.getElementById("post_limit").value = subscription.post_limit;
            document.getElementById("description").value = description;
            
            // Update form action
            const form = document.getElementById("subscriptionFormElement");
            form.action = `/admin/subscription/${id}`;
            form.method = "POST";
            document.getElementById("_method").value = "PUT";
            
            // Open form in edit mode
            toggleForm(true, true);
        })
        .catch(error => {
            console.error("Error fetching subscription:", error);
            alert("Failed to load subscription data.");
        });
}
</script>
@endsection
