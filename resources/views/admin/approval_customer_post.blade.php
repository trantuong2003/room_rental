@extends('layouts.admin')

@section('content')
<div class="">
    <div class="card">
        <div class="card-header">
            <h2>Quản lý bài đăng</h2>
        </div>
        
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Tiêu đề</th>
                            <th>Người đăng</th>
                            <th>Thời gian</th>
                            <th>Trạng thái</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($posts as $post)
                        <tr>
                            <td>{{ Str::limit($post->title, 30) }}</td>
                            <td>{{ $post->user->name }}</td>
                            <td>{{ $post->created_at->diffForHumans() }}</td>
                            <td>
                                @if($post->status === 'pending')
                                    <span class="badge bg-warning">Chờ duyệt</span>
                                @elseif($post->status === 'approved')
                                    <span class="badge bg-success">Đã duyệt</span>
                                @else
                                    <span class="badge bg-danger">Từ chối</span>
                                @endif
                            </td>
                            <td>
                                @if($post->status === 'pending')
                                    <form action="{{ route('moderation.customer.approve', $post->id) }}" 
                                          method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success">
                                            Duyệt
                                        </button>
                                    </form>
                                    
                                    <form action="{{ route('moderation.customer.reject', $post->id) }}" 
                                          method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            Từ chối
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            {{ $posts->links() }}
        </div>
    </div>
</div>
@endsection