@extends('layouts.app')

@section('title', 'View Announcement')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="fw-bold text-dark">Announcement Details</h2>
                <div class="btn-group">
                    <a href="{{ route('announcements.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Back to List
                    </a>
                    <a href="{{ route('announcements.edit', $announcement) }}" class="btn btn-primary">
                        <i class="fas fa-edit me-2"></i>Edit
                    </a>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h4 class="mb-2">{{ $announcement->title }}</h4>
                            <div class="d-flex gap-2">
                                <span class="badge {{ $announcement->getTypeBadgeClass() }}">
                                    {{ ucfirst($announcement->type) }}
                                </span>
                                @if($announcement->is_active && !$announcement->isExpired())
                                    <span class="badge bg-success">Active</span>
                                @elseif($announcement->isExpired())
                                    <span class="badge bg-secondary">Expired</span>
                                @else
                                    <span class="badge bg-warning">Inactive</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <h6 class="fw-bold mb-3">Content</h6>
                            <div class="announcement-content">
                                {!! nl2br(e($announcement->content)) !!}
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="border-start ps-4">
                                <h6 class="fw-bold mb-3">Details</h6>

                                <div class="mb-3">
                                    <small class="text-muted d-block">Created By</small>
                                    <strong>{{ $announcement->creator->name }}</strong>
                                </div>

                                <div class="mb-3">
                                    <small class="text-muted d-block">Created On</small>
                                    <strong>{{ $announcement->created_at->format('M d, Y \a\t g:i A') }}</strong>
                                </div>

                                @if($announcement->updated_at != $announcement->created_at)
                                    <div class="mb-3">
                                        <small class="text-muted d-block">Last Updated</small>
                                        <strong>{{ $announcement->updated_at->format('M d, Y \a\t g:i A') }}</strong>
                                    </div>
                                @endif

                                <div class="mb-3">
                                    <small class="text-muted d-block">Valid Until</small>
                                    <strong>
                                        @if($announcement->valid_until)
                                            {{ $announcement->valid_until->format('M d, Y') }}
                                            @if($announcement->isExpired())
                                                <span class="text-danger">(Expired)</span>
                                            @endif
                                        @else
                                            Never
                                        @endif
                                    </strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-footer bg-light">
                    <div class="d-flex justify-content-between">
                        <div class="btn-group">
                            <form action="{{ route('announcements.toggle', $announcement) }}" method="POST" class="d-inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit"
                                        class="btn btn-{{ $announcement->is_active ? 'warning' : 'success' }}">
                                    <i class="fas fa-{{ $announcement->is_active ? 'pause' : 'play' }} me-2"></i>
                                    {{ $announcement->is_active ? 'Deactivate' : 'Activate' }}
                                </button>
                            </form>
                        </div>

                        <form action="{{ route('announcements.destroy', $announcement) }}"
                              method="POST"
                              class="d-inline"
                              onsubmit="return confirm('Are you sure you want to delete this announcement? This action cannot be undone.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-trash me-2"></i>Delete
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
