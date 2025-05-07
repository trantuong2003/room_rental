@extends('layouts.landord')

@section('content')
<div class="main">
    <div class="edit_post">
        <div class="container">
            <h1>Edit Post</h1>
            <form action="{{ route('landlord.posts.update', $post->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <h2>Location</h2>
                <div class="mb-4">
                    <label>Address</label>
                    <input type="text" name="address" value="{{ old('address', $post->address) }}"
                        placeholder="Enter address" required>
                    @error('address')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
                <div class="grid grid-2">
                    <div>
                        <label>Latitude</label>
                        <input type="text" name="latitude" value="{{ old('latitude', $post->latitude) }}"
                            placeholder="Enter latitude" required>
                        @error('latitude')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <label>Longitude</label>
                        <input type="text" name="longitude" value="{{ old('longitude', $post->longitude) }}"
                            placeholder="Enter longitude" required>
                        @error('longitude')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <h2>Rental Details</h2>
                <div class="grid grid-2">
                    <div>
                        <label>Rental Price</label>
                        <input type="text" name="price" value="{{ old('price', $post->price) }}"
                            placeholder="Enter rental price" required>
                        @error('price')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <label>Area</label>
                        <input type="text" name="acreage" value="{{ old('acreage', $post->acreage) }}"
                            placeholder="Enter area" required>
                        @error('acreage')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="grid grid-2">
                    <div>
                        <label>Number of Bedrooms</label>
                        <input type="number" name="bedrooms" value="{{ old('bedrooms', $post->bedrooms) }}"
                            placeholder="Enter number of bedrooms" required>
                        @error('bedrooms')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <label>Number of Bathrooms</label>
                        <input type="number" name="bathrooms" value="{{ old('bathrooms', $post->bathrooms) }}"
                            placeholder="Enter number of bathrooms" required>
                        @error('bathrooms')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="grid grid-2">
                    <div>
                        <label>Electricity Price</label>
                        <input type="text" name="electricity_price"
                            value="{{ old('electricity_price', $post->electricity_price) }}"
                            placeholder="Enter electricity price">
                        @error('electricity_price')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <label>Water Price</label>
                        <input type="text" name="water_price" value="{{ old('water_price', $post->water_price) }}"
                            placeholder="Enter water price">
                        @error('water_price')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="grid grid-2">
                    <div>
                        <label>Internet Price</label>
                        <input type="text" name="internet_price"
                            value="{{ old('internet_price', $post->internet_price) }}"
                            placeholder="Enter internet price">
                        @error('internet_price')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <label>Service Price</label>
                        <input type="text" name="service_price" value="{{ old('service_price', $post->service_price) }}"
                            placeholder="Enter service price">
                        @error('service_price')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="grid grid-2">
                    <div>
                        <label>Furniture</label>
                        <input type="text" name="furniture" value="{{ old('furniture', $post->furniture) }}"
                            placeholder="Enter furniture details">
                        @error('furniture')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <label>Amenities</label>
                        <input type="text" name="utilities" value="{{ old('utilities', $post->utilities) }}"
                            placeholder="Enter amenities">
                        @error('utilities')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <h2>Description</h2>
                <div class="mb-4">
                    <label>Title <span class="text-red-500">*</span></label>
                    <input type="text" name="title" value="{{ old('title', $post->title) }}" placeholder="Title"
                        required>
                    <div class="text-right text-gray-500 text-sm">{{ strlen(old('title', $post->title)) }}/150
                        characters</div>
                    @error('title')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
                <div class="mb-4">
                    <label>Description <span class="text-red-500">*</span></label>
                    <textarea name="description" rows="6" placeholder="Description"
                        required>{{ old('description', $post->description) }}</textarea>
                    <div class="text-right text-gray-500 text-sm">{{ strlen(old('description', $post->description))
                        }}/5000 characters</div>
                    @error('description')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>


                <div></div>
                <div class="mb-4">
                    <label>Add New Images</label>
                    <div class="border border-dashed border-gray-300 p-4 rounded-lg text-center">
                        <input type="file" name="images[]" id="upload-image" multiple accept="image/*">
                        <p class="text-gray-500 text-sm mt-2">Maximum 25 images, size not exceeding 8MB</p>
                    </div>
                    @error('images.*')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
                <div></div>
                <div class="text-right mt-6">
                    <button type="submit" class="btn-update">Update Post</button>
                </div>
            </form>
            <div></div>
            <h2>Images/Video</h2>
            <div></div>
            <div class="mb-4">
                <label>Manage Images</label>
                <div class="flex items-center justify-between border border-dashed border-gray-300 p-4 rounded-lg">
                    <form action="{{ route('landlord.posts.images.delete', $post->id) }}" method="POST"
                        onsubmit="return confirm('Are you sure you want to delete all images of this post?');"
                        class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded">Delete All Images</button>
                    </form>
                    <p class="text-gray-500 text-sm">Currently has {{ $post->images->count() }} images</p>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection