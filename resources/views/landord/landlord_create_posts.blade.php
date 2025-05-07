@extends('layouts.landord')

@section('content')
<div class="main">
    <div class="create_post">
        <div class="container">
            <h1>Create a New Post</h1>
            <form action="{{ route('landlord.posts.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <h2>Location</h2>
                <div class="mb-4">
                    <label>Address</label>
                    <input type="text" name="address" placeholder="Enter address" required>
                </div>
                <div class="grid grid-2">
                    <div>
                        <label>Latitude</label>
                        <input type="text" name="latitude" placeholder="Enter latitude" required>
                    </div>
                    <div>
                        <label>Longitude</label>
                        <input type="text" name="longitude" placeholder="Enter longitude" required>
                    </div>
                </div>
                <h2>Rental Details</h2>
                <div class="grid grid-2">
                    <div>
                        <label>Rental Price</label>
                        <input type="text" name="price" placeholder="Enter rental price" required>
                    </div>
                    <div>
                        <label>Area</label>
                        <input type="text" name="acreage" placeholder="Enter area" required>
                    </div>
                </div>
                <div class="grid grid-2">
                    <div>
                        <label>Number of Bedrooms</label>
                        <input type="number" name="bedrooms" placeholder="Enter number of bedrooms" required>
                    </div>
                    <div>
                        <label>Number of Bathrooms</label>
                        <input type="number" name="bathrooms" placeholder="Enter number of bathrooms" required>
                    </div>
                </div>
                <div class="grid grid-2">
                    <div>
                        <label>Electricity Price</label>
                        <input type="text" name="electricity_price" placeholder="Enter electricity price">
                    </div>
                    <div>
                        <label>Water Price</label>
                        <input type="text" name="water_price" placeholder="Enter water price">
                    </div>
                </div>
                <div class="grid grid-2">
                    <div>
                        <label>Internet Price</label>
                        <input type="text" name="internet_price" placeholder="Enter internet price">
                    </div>
                    <div>
                        <label>Service Price</label>
                        <input type="text" name="service_price" placeholder="Enter service price">
                    </div>
                </div>
                <div class="grid grid-2">
                    <div>
                        <label>Furniture</label>
                        <input type="text" name="furniture" placeholder="Enter furniture details">
                    </div>
                    <div>
                        <label>Amenities</label>
                        <input type="text" name="utilities" placeholder="Enter amenities">
                    </div>
                </div>
                <h2>Description</h2>
                <div class="mb-4">
                    <label>Title <span class="text-red-500">*</span></label>
                    <input type="text" name="title" placeholder="Title" required>
                    <div class="text-right text-gray-500 text-sm">0/150 characters</div>
                </div>
                <div class="mb-4">
                    <label>Description <span class="text-red-500">*</span></label>
                    <textarea name="description" rows="6" placeholder="Description" required></textarea>
                    <div class="text-right text-gray-500 text-sm">0/5000 characters</div>
                </div>
                <h2>Images/Video</h2>
                <div class="mb-4">
                    <label>Images</label>
                    <div class="border border-dashed border-gray-300 p-4 rounded-lg text-center">
                        <input type="file" name="images[]" id="upload-image" multiple accept="image/*">
                        <p class="text-gray-500 text-sm mt-2">Maximum 25 images, size not exceeding 8MB</p>
                    </div>
                </div>
                <div class="text-right mt-6">
                    <button type="submit" class="btn-create">Create Post</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection