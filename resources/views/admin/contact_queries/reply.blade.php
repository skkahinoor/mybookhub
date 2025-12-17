@extends('admin.layout.layout')

@section('content')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Reply to Query</h4>

                        @if(Session::has('success_message'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <strong>Success:</strong> {{ Session::get('success_message') }}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif

                        @if($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                @foreach($errors->all() as $error)
                                    <div>{{ $error }}</div>
                                @endforeach
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Customer Name:</label>
                                    <input type="text" class="form-control" value="{{ $query['name'] }}" readonly>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Customer Email:</label>
                                    <input type="text" class="form-control" value="{{ $query['email'] }}" readonly>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Subject:</label>
                                    <input type="text" class="form-control" value="{{ $query['subject'] }}" readonly>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Original Message:</label>
                                    <textarea class="form-control" rows="5" readonly>{{ $query['message'] }}</textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Conversation Thread -->
                        @if(isset($query['replies']) && count($query['replies']) > 0)
                            <div class="row" style="margin-top: 20px;">
                                <div class="col-md-12">
                                    <h5>Conversation Thread</h5>
                                    <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; max-height: 400px; overflow-y: auto;">
                                        @foreach($query['replies'] as $reply)
                                            @if($reply['reply_by'] == 'admin')
                                                <div style="background: #e8f5e9; padding: 12px; border-radius: 6px; margin-bottom: 10px; border-left: 4px solid #28a745;">
                                                    <strong style="color: #28a745;">Admin:</strong>
                                                    <p style="margin: 5px 0; color: #333;">{{ $reply['message'] }}</p>
                                                    <small style="color: #999;">{{ date('F d, Y h:i A', strtotime($reply['created_at'])) }}</small>
                                                </div>
                                            @else
                                                <div style="background: #e3f2fd; padding: 12px; border-radius: 6px; margin-bottom: 10px; border-left: 4px solid #2196f3;">
                                                    <strong style="color: #2196f3;">Customer:</strong>
                                                    <p style="margin: 5px 0; color: #333;">{{ $reply['message'] }}</p>
                                                    <small style="color: #999;">{{ date('F d, Y h:i A', strtotime($reply['created_at'])) }}</small>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if($query['status'] == 'resolved')
                            <!-- Resolved Status - Show Success Message -->
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="alert alert-success" style="padding: 30px; text-align: center; border-left: 4px solid #28a745;">
                                        <div style="font-size: 48px; margin-bottom: 15px;">✅</div>
                                        <h4 style="color: #155724; margin-bottom: 10px; font-weight: 600;">Query Successfully Resolved!</h4>
                                        <p style="margin: 0; color: #155724; font-size: 16px;">This query has been marked as resolved. No further action is required.</p>
                                        <p style="margin-top: 10px; color: #155724; font-size: 14px;">If you need to reopen this query, you can change the status below.</p>
                                    </div>
                                    
                                    <!-- Option to Change Status -->
                                    <form action="{{ url('admin/contact-queries/reply/'.$query['id']) }}" method="post">
                                        @csrf
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label>Change Status (if needed)</label>
                                                    <select class="form-control" name="status" onchange="this.form.submit()">
                                                        <option value="pending" {{ $query['status'] == 'pending' ? 'selected' : '' }}>Pending</option>
                                                        <option value="in_progress" {{ $query['status'] == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                                        <option value="resolved" {{ $query['status'] == 'resolved' ? 'selected' : '' }}>Resolved</option>
                                                    </select>
                                                    <input type="hidden" name="admin_reply" value="{{ $query['admin_reply'] ?? '' }}">
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                    
                                    <div style="margin-top: 20px;">
                                        <a href="{{ url('admin/contact-queries') }}" class="btn btn-secondary">Back to Queries</a>
                                    </div>
                                </div>
                            </div>
                        @else
                            <!-- Not Resolved - Show Reply Form -->
                            <form action="{{ url('admin/contact-queries/reply/'.$query['id']) }}" method="post">
                                @csrf
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Admin Reply <span class="text-danger">*</span></label>
                                            <textarea class="form-control" name="admin_reply" rows="5" required>{{ old('admin_reply', $query['admin_reply']) }}</textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Status <span class="text-danger">*</span></label>
                                            <select class="form-control" name="status" required>
                                                <option value="pending" {{ $query['status'] == 'pending' ? 'selected' : '' }}>Pending</option>
                                                <option value="in_progress" {{ $query['status'] == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                                <option value="resolved" {{ $query['status'] == 'resolved' ? 'selected' : '' }}>Resolved</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <button type="submit" class="btn btn-primary">Submit Reply</button>
                                        <a href="{{ url('admin/contact-queries') }}" class="btn btn-secondary">Back</a>
                                    </div>
                                </div>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('admin.layout.footer')
</div>
@endsection

