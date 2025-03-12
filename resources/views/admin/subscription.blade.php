@extends('layouts.admin')

@section('content')
<div class="main-subscription">
    <div class="subscription-container">
        <div class="subscription-header">
            <h2>Subscription Packages</h2>
            <button class="button button-create" onclick="toggleForm(true)">Create New</button>
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
                        <button class="button button-edit"
                            onclick="editSubscription({{ $subscription->id }})">Edit</button>
                        <form action="{{ route('subscriptions.destroy', $subscription->id) }}" method="POST"
                            style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="button button-delete"
                                onclick="return confirm('Are you sure you want to delete?')">Delete</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Form Overlay -->
<div class="overlay" id="overlay" onclick="toggleForm(false)"></div>
<div class="subscription-form-container" id="subscriptionForm">
    <button class="close-button" onclick="toggleForm(false)">X</button>
    <form id="subscriptionFormElement" method="POST">
        @csrf
        <input type="hidden" id="id" name="id">
        <input type="hidden" id="_method" name="_method" value="POST"> <!-- Dùng để PUT -->

        <div class="form-group">
            <label for="package_name">Name</label>
            <input type="text" id="package_name" name="package_name" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="price">Price</label>
            <input type="number" id="price" name="price" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="duration_days">Duration (days)</label>
            <input type="number" id="duration_days" name="duration_days" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="post_limit">Number of Posts</label>
            <input type="number" id="post_limit" name="post_limit" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="description">Description</label>
            <textarea id="description" name="description" class="form-control" rows="3" required></textarea>
        </div>
        <button type="submit" class="button button-create">Save</button>
    </form>
</div>
@endsection

<script>
    function toggleForm(show, isEdit = false) {
        const form = document.getElementById("subscriptionForm");
        const overlay = document.getElementById("overlay");

        if (show) {
            form.style.display = "block";
            overlay.style.display = "block";

            if (!isEdit) {
                resetForm();
            }
        } else {
            form.style.display = "none";
            overlay.style.display = "none";

            resetForm(); // Reset form khi đóng cửa sổ
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
        form.action = "/admin/subscription"; // Trả về action mặc định (tạo mới)
        form.method = "POST";
        document.getElementById("_method").value = "POST"; // Đảm bảo tạo mới
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

                // Xử lý description: tự động thêm dấu chấm nếu thiếu
                let description = subscription.description.trim();
                if (!description.endsWith(".")) {
                    description += ".";
                }

                // Chuyển dấu chấm thành xuống dòng (mỗi câu một dòng)
                description = description.replace(/\.\s+/g, ".\n");

                // Điền dữ liệu vào form
                document.getElementById("id").value = subscription.id;
                document.getElementById("package_name").value = subscription.package_name;
                document.getElementById("price").value = subscription.price;
                document.getElementById("duration_days").value = subscription.duration_days;
                document.getElementById("post_limit").value = subscription.post_limit;
                document.getElementById("description").value = description;

                // Cập nhật action của form thành cập nhật
                const form = document.getElementById("subscriptionFormElement");
                form.action = `/admin/subscription/${id}`;
                form.method = "POST"; // Laravel yêu cầu form có method là POST
                document.getElementById("_method").value = "PUT"; // Laravel hiểu đây là PUT

                // Mở form ở chế độ chỉnh sửa
                toggleForm(true, true);
            })
            .catch(error => {
                console.error("Error fetching subscription:", error);
                alert("Failed to load subscription data.");
            });
    }
</script>