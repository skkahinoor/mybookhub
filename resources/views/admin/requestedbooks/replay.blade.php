@extends('admin.layout.layout')

@section('content')
<style>
    .request-reply-page .card {
        border: 1px solid #e8edf5;
        border-radius: 14px;
        box-shadow: 0 6px 20px rgba(21, 38, 67, 0.06);
    }
    .request-reply-page .info-box {
        border: 1px solid #e8edf5;
        border-radius: 10px;
        background: #f8fbff;
        padding: 12px;
        margin-bottom: 14px;
    }
    .request-reply-page .info-label {
        font-size: 12px;
        font-weight: 700;
        color: #64748b;
        margin-bottom: 4px;
        text-transform: uppercase;
    }
    .request-reply-page .info-value {
        color: #1e293b;
        font-weight: 600;
    }
    .request-reply-page .thread-wrap {
        background: #f8fafc;
        border: 1px solid #e8edf5;
        padding: 15px;
        border-radius: 10px;
        max-height: 400px;
        overflow-y: auto;
    }
</style>
<div class="main-panel">
    <div class="content-wrapper request-reply-page">
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title mb-1">Reply to Book Query</h4>
                        <p class="text-muted mb-4">Review request details and continue the conversation.</p>

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
                            <div class="col-md-6">
                                <div class="info-box">
                                    <div class="info-label">Customer Name</div>
                                    <div class="info-value">{{ $bookRequest->user->name ?? 'N/A' }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-box">
                                    <div class="info-label">Customer Email</div>
                                    <div class="info-value">{{ $bookRequest->user->email ?? 'N/A' }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-box">
                                    <div class="info-label">Customer Contact Number</div>
                                    <div class="info-value">{{ $bookRequest->user->mobile ?? 'N/A' }} ({{ $bookRequest->user->email ?? 'N/A' }})</div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="info-box">
                                    <div class="info-label">Book Title</div>
                                    <div class="info-value">{{ $bookRequest->book_title }}</div>
                                </div>
                            </div>
                            @if($bookRequest->author_name)
                            <div class="col-md-12">
                                <div class="info-box">
                                    <div class="info-label">Author Name</div>
                                    <div class="info-value">{{ $bookRequest->author_name }}</div>
                                </div>
                            </div>
                            @endif
                            <div class="col-md-12">
                                <div class="info-box">
                                    <div class="info-label">Original Message</div>
                                    <div class="info-value">{{ $bookRequest->message ?? 'No message provided' }}</div>
                                </div>
                            </div>
                        </div>

                        <!-- Conversation Thread -->
                        @if(isset($query['replies']) && count($query['replies']) > 0)
                            <div class="row" style="margin-top: 20px;">
                                <div class="col-md-12">
                                    <h5>Conversation Thread</h5>
                                    <div class="thread-wrap">
                                        @foreach($query['replies'] as $reply)
                                            @if($reply['reply_by'] == 'admin')
                                                <div style="background: #e8f5e9; padding: 12px; border-radius: 6px; margin-bottom: 10px; border-left: 4px solid #28a745;">
                                                    <strong style="color: #28a745;">
                                                        @if($reply['vendor_id'])
                                                            Vendor ({{ $reply['vendor']['user']['name'] ?? ('#' . $reply['vendor_id']) }}):
                                                        @else
                                                            Admin:
                                                        @endif
                                                    </strong>
                                                    <p style="margin: 5px 0; color: #333; {{ isset($reply['is_ended']) && $reply['is_ended'] ? 'font-style: italic; color: #777;' : '' }}">{{ $reply['message'] }}</p>
                                                    <small style="color: #999;">{{ date('F d, Y h:i A', strtotime($reply['created_at'])) }}</small>
                                                </div>
                                            @else
                                                <div style="background: #e3f2fd; padding: 12px; border-radius: 6px; margin-bottom: 10px; border-left: 4px solid #2196f3;">
                                                    <strong style="color: #2196f3;">Customer:</strong>
                                                    <p style="margin: 5px 0; color: #333; {{ isset($reply['is_ended']) && $reply['is_ended'] ? 'font-style: italic; color: #777;' : '' }}">{{ $reply['message'] }}</p>
                                                    @if(isset($reply['is_ended']) && $reply['is_ended'])
                                                        <span class="badge badge-danger">Conversation Ended</span>
                                                    @endif
                                                    <small style="color: #999;">{{ date('F d, Y h:i A', strtotime($reply['created_at'])) }}</small>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endif

                        @php
                            $status = $bookRequest->status;
                            $isFinal = ($status === 'available' || $status === 'not_available');
                            $isConversationEnded = collect($query['replies'])->where('is_ended', true)->count() > 0;
                        @endphp

                        @if($isFinal || $isConversationEnded)
                            <!-- Final Status or Ended Conversation - Show Message -->
                            <div class="row">
                                <div class="col-md-12">
                                    @if($isConversationEnded)
                                        <div class="alert alert-warning" style="padding: 30px; text-align: center; border-left: 4px solid #ffc107;">
                                            <div style="font-size: 48px; margin-bottom: 15px;">🚫</div>
                                            <h4 style="color: #856404; margin-bottom: 10px; font-weight: 600;">Conversation Ended</h4>
                                            <p style="margin: 0; color: #856404; font-size: 16px;">The student has ended this conversation. No further replies can be sent.</p>
                                        </div>
                                    @elseif($status === 'available')
                                        <div class="alert alert-success" style="padding: 30px; text-align: center; border-left: 4px solid #28a745;">
                                            <div style="font-size: 48px; margin-bottom: 15px;">✅</div>
                                            <h4 style="color: #155724; margin-bottom: 10px; font-weight: 600;">Book Confirmed Available!</h4>
                                            <p style="margin: 0; color: #155724; font-size: 16px;">This book has been marked as available.</p>
                                        </div>
                                    @elseif($status === 'not_available')
                                        <div class="alert alert-danger" style="padding: 30px; text-align: center; border-left: 4px solid #dc3545;">
                                            <div style="font-size: 48px; margin-bottom: 15px;">❌</div>
                                            <h4 style="color: #721c24; margin-bottom: 10px; font-weight: 600;">Book Not Available</h4>
                                            <p style="margin: 0; color: #721c24; font-size: 16px;">This request has been marked as not available.</p>
                                        </div>
                                    @endif

                                    <!-- Option to Change Status -->
                                    @php
                                        $submitRoute = ($adminType === 'vendor') ? 'vendor.requestbook.reply' : 'requestbook.reply';
                                    @endphp
                                    <form action="{{ route($submitRoute, $bookRequest->id) }}" method="post">
                                        @csrf
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label>Change Status (if needed)</label>
                                                    <select class="form-control" name="status" onchange="this.form.submit()">
                                                        <option value="awaiting_response" {{ $status == 'awaiting_response' ? 'selected' : '' }}>🟡 Awaiting Vendor Response</option>
                                                        <option value="vendor_replied" {{ $status == 'vendor_replied' ? 'selected' : '' }}>🔵 Vendor Replied</option>
                                                        <option value="available" {{ $status == 'available' ? 'selected' : '' }}>🟢 Confirmed Available</option>
                                                        <option value="not_available" {{ $status == 'not_available' ? 'selected' : '' }}>🔴 Not Available</option>
                                                    </select>
                                                    <input type="hidden" name="admin_reply" value="{{ $bookRequest->admin_reply ?? '' }}">
                                                </div>
                                            </div>
                                        </div>
                                    </form>

                                    @php
                                        $indexRoute = ($adminType === 'vendor') ? 'vendor.requestbook.index' : 'requestbook.index';
                                    @endphp
                                    <div style="margin-top: 20px;">
                                        <a href="{{ route($indexRoute) }}" class="btn btn-secondary">Back to Book Requests</a>
                                    </div>
                                </div>
                            </div>
                        @else
                            <!-- Not Resolved - Show Reply Form -->
                            @php
                                $submitRoute = ($adminType === 'vendor') ? 'vendor.requestbook.reply' : 'requestbook.reply';
                            @endphp
                            <form action="{{ route($submitRoute, $bookRequest->id) }}" method="post">
                                @csrf
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Reply <span class="text-danger">*</span></label>
                                            <textarea class="form-control" name="admin_reply" rows="5" required></textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Status <span class="text-danger">*</span></label>
                                            <select class="form-control" name="status" required>
                                                <option value="awaiting_response" {{ $status == 'awaiting_response' ? 'selected' : '' }}>🟡 Awaiting Vendor Response</option>
                                                <option value="vendor_replied" {{ $status == 'vendor_replied' ? 'selected' : '' }}>🔵 Vendor Replied</option>
                                                <option value="available" {{ $status == 'available' ? 'selected' : '' }}>🟢 Confirmed Available</option>
                                                <option value="not_available" {{ $status == 'not_available' ? 'selected' : '' }}>🔴 Not Available</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        @php
                                            $indexRoute = ($adminType === 'vendor') ? 'vendor.requestbook.index' : 'requestbook.index';
                                        @endphp
                                        <button type="submit" class="btn btn-primary">Submit Reply</button>
                                        <a href="{{ route($indexRoute) }}" class="btn btn-secondary">Back</a>
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

