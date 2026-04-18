@extends('admin.layout.layout')
@section('content')
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="row">
                <div class="col-md-12 grid-margin">
                    <div class="row">
                        <div class="col-12 col-xl-8 mb-4 mb-xl-0">
                            <h3 class="font-weight-bold">Push Notifications</h3>
                            <h6 class="font-weight-normal mb-0">Create and send custom push notifications to users via Firebase</h6>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-8 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Compose Notification</h4>
                            <form class="forms-sample" action="{{ route('admin.push-notifications.send') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="form-group">
                                    <label for="title">Notification Title</label>
                                    <input type="text" class="form-control" id="title" name="title" placeholder="Enter title" required>
                                </div>
                                <div class="form-group">
                                    <label for="body">Notification Body</label>
                                    <textarea class="form-control" id="body" name="body" rows="4" placeholder="Enter message" required></textarea>
                                </div>
                                <div class="form-group">
                                    <label for="notification_image">Notification Image (Optional)</label>
                                    <input type="file" class="form-control" id="notification_image" name="image" accept="image/*" onchange="previewImage(this)">
                                    <div id="image-preview-container" class="mt-2" style="display:none;">
                                        <img id="image-preview" src="#" alt="Image Preview" style="max-width: 200px; border-radius: 5px; border: 1px solid #ddd; padding: 5px;">
                                    </div>
                                    <small class="text-muted d-block mt-1">Upload an image to be displayed in the notification.</small>
                                </div>
                                <div class="form-group">
                                    <label for="image_url">Or Image URL</label>
                                    <input type="url" class="form-control" id="image_url" name="image_url" placeholder="https://example.com/image.png">
                                    <small class="text-muted">Direct link to an image (used if no file is uploaded).</small>
                                </div>
                                <div class="form-group">
                                    <label for="user_ids">Target Users (Optional)</label>
                                    <select class="form-control select2" id="user_ids" name="user_ids[]" multiple style="width: 100%;">
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                                        @endforeach
                                    </select>
                                    <small class="text-muted">Leave empty to send to all registered devices.</small>
                                </div>
                                <button type="submit" class="btn btn-primary mr-2">Send Notification</button>
                                <button type="reset" class="btn btn-light">Cancel</button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Quick Tips</h4>
                            <ul class="list-arrow">
                                <li>Ensure users have granted notification permission in their browser/app.</li>
                                <li>Images should be ideally 2:1 ratio for best display.</li>
                                <li>Notifications are sent via Firebase Cloud Messaging (FCM).</li>
                                <li>Targeting specific users only works if they have registered their device tokens.</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- content-wrapper ends -->
        @include('admin.layout.footer')
    </div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('.select2').select2({
            placeholder: "Select users",
            allowClear: true
        });
    });

    function previewImage(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#image-preview').attr('src', e.target.result);
                $('#image-preview-container').show();
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endpush
