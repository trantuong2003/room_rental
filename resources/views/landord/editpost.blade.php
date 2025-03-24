@extends('layouts.landord')

@section('content')
<div class="main">
    <div class="create_post">
        <div class="container">
            <h1>Edit Post</h1>
            <form action="{{ route('landlord.posts.update', $post->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <h2>Location</h2>
                <div class="mb-4">
                    <label>Address</label>
                    <input type="text" name="address" value="{{ $post->address }}" required>
                </div>
                <div class="grid grid-2">
                    <div>
                        <label>Latitude</label>
                        <input type="text" name="latitude" value="{{ $post->latitude }}" required>
                    </div>
                    <div>
                        <label>Longitude</label>
                        <input type="text" name="longitude" value="{{ $post->longitude }}" required>
                    </div>
                </div>

                <h2>Rental category</h2>
                <div class="grid grid-2">
                    <div>
                        <label>Price</label>
                        <input type="text" name="price" value="{{ $post->price }}" required>
                    </div>
                    <div>
                        <label>Acreage</label>
                        <input type="text" name="acreage" value="{{ $post->acreage }}" required>
                    </div>
                </div>
                <div class="grid grid-2">
                    <div>
                        <label>Bedrooms</label>
                        <input type="number" name="bedrooms" value="{{ $post->bedrooms }}" required>
                    </div>
                    <div>
                        <label>Bathrooms</label>
                        <input type="number" name="bathrooms" value="{{ $post->bathrooms }}" required>
                    </div>
                </div>

                <h2>Additional Information</h2>
                <div class="mb-4">
                    <label>Title</label>
                    <input type="text" name="title" value="{{ $post->title }}" required>
                </div>
                <div class="mb-4">
                    <label>Description</label>
                    <textarea name="description" rows="6" required>{{ $post->description }}</textarea>
                </div>

                <h2>Images</h2>
                <div class="mb-4">
                    <label>Upload Images</label>
                    <input type="file" name="images[]" multiple accept="image/*">
                </div>

                <div class="text-right mt-6">
                    <button type="submit" class="btn-update">Update Post</button>
                </div>
            </form>

            <form action="{{ route('landlord.posts.destroy', $post->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this post?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-delete">Delete Post</button>
            </form>
        </div>
    </div>
</div>
@endsection
