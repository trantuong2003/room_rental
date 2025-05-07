@extends('layouts.customer')

@section('content')
<div class="container">
    <div class="edit-post-customer">
        <div class="card layout">
            <div class="left-section">
                <h1 class="header">Edit Roommate Post</h1>
                <p class="sub-header">Update your post content</p>

                @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <form action="{{ route('customer.roommates.update', $post->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <!-- Title and Description -->
                    <div class="form-group">
                        <label for="title">Title</label>
                        <input type="text" name="title" id="title" class="form-control"
                            value="{{ old('title', $post->title) }}" required>
                    </div>
                    <div class="form-group">
                        <label for="content">Content</label>
                        <textarea name="content" id="content" class="form-control" rows="8"
                            required>{{ old('content', $post->content) }}</textarea>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="submit-btn">Update Post</button>
                        <a href="{{ route('customer.roommates.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>

            <div class="right-section">
                <div class="price-info">
                    <h3>Notes for Editing</h3>
                    <ul>
                        <li>The post will be updated immediately</li>
                        <li>Ensure the information is accurate and complete</li>
                        <li>Avoid violating community guidelines</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection