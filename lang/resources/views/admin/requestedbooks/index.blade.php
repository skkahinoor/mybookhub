@extends('admin.layout.layout') {{-- Adjust this if your layout is different --}}

@section('content')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Book Request</h4>
                        {{-- <a style="max-width: 150px; float: right; display: inline-block;" href="{{ url('admin/add-edit-language') }}" class="btn btn-block btn-primary">Add Language</a> --}}
                        @if(Session::has('success_message'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <strong>Success:</strong> {{ Session::get('success_message') }}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif
                        <div class="table-responsive pt-3">
                            <table class="table table-bordered" id="request">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Author Name</th>
                                        <th>Message</th>
                                        <th>Requested By User</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($bookRequests as $key => $book)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $book['book_title'] }}</td>
                                        <td>{{ $book['author_name'] }}</td>
                                        <td>{{ $book['message'] }}</td>
                                        <td>{{ $book->user->name ?? 'User not found' }}</td>
                                        <td>
                                            @if($book['status']==1)
                                                <a class="updateBookStatus" id="book-{{ $book['id'] }}" book_id="{{ $book['id'] }}" href="javascript:void(0)">
                                                <i style="font-size:25px;" title="Book Available" class="mdi mdi-bookmark-check" status="Active"></i>
                                                </a>
                                            @else
                                                <a class="updateBookStatus" id="book-{{ $book['id'] }}" book_id="{{ $book['id'] }}" href="javascript:void(0)">
                                                <i style="font-size:25px;" title="Book Requested" class="mdi mdi-bookmark-outline" status="Inactive"></i>
                                                </a>
                                            @endif
                                        </td>
                                        <td>
                                            {{-- <a href="{{ url('admin/add-edit-language/'.$language['id']) }}"><i style="font-size:25px;" class="mdi mdi-pencil-box"></i></a> --}}


                                            {{-- <a href="javascript:void(0)" class="confirmDelete" module="book" moduleid="{{ $book['id'] }}"><i style="font-size:25px;" class="mdi mdi-file-excel-box"></i></a> --}}

                                            {{-- <a title="book" class="confirmDelete" href="{{route('bookrequests.delete',$book->id )}}"><i style="font-size:25px;" class="mdi mdi-file-excel-box"></i></a> --}}

                                            <form action="{{route('bookrequests.delete',$book->id )}}" method="POST" onsubmit="return confirm('Are you sure?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" style="border: none; padding: 0;background: transparent;"><i style=" font-size:25px;" class="mdi mdi-file-excel-box"></i></button>
                                            </form>

                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('admin.layout.footer')
</div>




    <!-- DataTables Bootstrap 4 CSS CDN -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap4.min.css">

    <!-- jQuery CDN (required for DataTables) -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <!-- DataTables JS CDN -->
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap4.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#request').DataTable();
        });
    </script>
<script>
$(document).ready(function(){
    $('.updateBookStatus').click(function(){
        var book_id = $(this).attr('book_id');
        var $icon = $(this).find('i');
        $.ajax({
            type: 'POST',
            url: '{{ route("bookrequests.updateStatus") }}',
            data: {
                _token: '{{ csrf_token() }}',
                book_id: book_id
            },
            success: function(resp){
                if(resp.status == 1){
                    $icon.removeClass('mdi-bookmark-outline').addClass('mdi-bookmark-check')
                        .attr('title', 'Book Available').attr('status', 'Active');
                } else {
                    $icon.removeClass('mdi-bookmark-check').addClass('mdi-bookmark-outline')
                        .attr('title', 'Book Requested').attr('status', 'Inactive');
                }
            },
            error: function(xhr){
                alert('Failed to update status.');
            }
        });
    });
});
</script>


@endsection


