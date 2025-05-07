@extends('layouts.landord')

@section('content')
<div class="main">
    <div class="post">
        <div class="post_container">
            <div class="title">
                <h1>List of Posts</h1>
                @if (auth()->user()->subscriptions()->active()->exists())
                <div class="create_post">
                    <a href="{{ route('landlord.posts.create') }}" class="btn btn-primary mb-3">Create New Post</a>
                </div>
                @else
                <div class="alert alert-warning">
                    You need to purchase a subscription package to post.
                    <a href="{{ route('landlord.package') }}" class="btn btn-warning">Buy Package Now</a>
                </div>
                @endif
            </div>

            @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
            @endif

            @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
            @endif

            @foreach ($posts as $post)
            <div class="listing">
                <div class="images">
                    @if ($post->images->isNotEmpty())
                    <img src="{{ asset('storage/' . $post->images->first()->image_path) }}" alt="Main Image">
                    <div class="grid">
                        @foreach ($post->images->slice(1, 3) as $image)
                        <img src="{{ asset('storage/' . $image->image_path) }}" alt="Additional Image">
                        @endforeach
                    </div>
                    @else
                    <img src="https://placehold.co/300x200" alt="No Image">
                    @endif
                </div>
                <div class="details">
                    <div class="header">
                        <h2>{{ $post->title }}</h2>
                    </div>
                    <div class="price">
                        {{ $post->price }}
                        <span>· {{ $post->acreage }}</span>
                    </div>
                    <div class="location">
                        <i class="fas fa-map-marker-alt"></i>
                        <span>{{ $post->address }}</span>
                    </div>
                    <p>{{ Str::limit($post->description, 200) }}</p>
                    <div class="footer">
                        <div class="profile">
                            <img src="https://placehold.co/40x40" alt="Profile">
                            <div>
                                <p>{{ $post->user->name ?? 'Landlord' }}</p>
                            </div>
                        </div>
                        <div class="actions">
                            <button>
                                <i class="fas fa-phone-alt"></i> {{ $post->user->phone ?? 'Hidden' }}
                            </button>
                            <a href="{{ route('landlord.posts.detail', ['id' => $post->id]) }}" class="btn btn-primary">
                                Details
                            </a>
                            <a href="{{ route('landlord.posts.edit', $post->id) }}" class="btn btn-warning">
                                Edit
                            </a>
                            <form action="{{ route('landlord.posts.destroy', $post->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this post?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-delete">Delete Post</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection