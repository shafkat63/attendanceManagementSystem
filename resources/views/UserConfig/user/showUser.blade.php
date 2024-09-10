@extends('layout.appmain')
@section('title', '- User')

@section('main')

    <main id="main" class="main">

        <div class="pagetitle">
            <h1>User Info</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{url('/')}}">Home</a></li>
                    <li class="breadcrumb-item">User Config</li>
                    <li class="breadcrumb-item active">User Info</li>
                </ol>
            </nav>
        </div>
        <!-- End Page Title -->

        <section class="section dashboard">
            <div class="row">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-10">
                                <h5 class="card-title">User Info</h5>
                            </div>
                            <div class="col-md-2 mt-3 ">
                                <a href="{{route('User.create')}}" type="button" class="btn btn-outline-success btn-sm text-right"> Add New <i class="bi bi-plus"></i></a>
                            </div>
                        </div>
                        <form action="#" id="fromData" style="display: none">@csrf</form>
                        <table class="table table-hover table-responsive table-sm" id="dataTableItem">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Phone</th>
                                <th>Email</th>
                                <th>Role</th>

                                <th>Actions</th>
                            </tr>
                            </thead>
                        </table>
                        <!-- End Table with hoverable rows -->

                    </div>
                </div>
            </div>
        </section>

    </main>
    <!-- End #main -->
@endsection
@section('script')
    <script>
        var TableData;
        var url = "{{ route('all.User') }}";

        function LoadDataTable() {
            $('#dataTableItem').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: {
                    url: url,
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: function(d) {
                        d.form_data = $("#fromData").serialize(); // Send form data if needed
                    },
                    error: function(xhr, error, thrown) {
                        console.error('AJAX Error:', thrown);
                    }
                },
                columns: [
                    { data: 'id' },
                    { data: 'name' },
                    { data: 'phone' },
                    { data: 'email' },
                    
                    { data: 'roles_html' },
                    {
                        data: null,
                        orderable: false,
                        defaultContent: "NO Data",
                        render: function(data, type, row) {
                            return `
                    <button type="button" class="btn btn-outline-info btn-sm edit-button" data-id="${row.uid}"><i class="bi bi-pencil-fill"></i></button>
                    <button type="button" class="btn btn-outline-danger btn-sm delete-button" data-id="${row.uid}"><i class="bi bi-x-circle-fill"></i></button>
                `;
                        }
                    }
                ],
                drawCallback: function(settings) {
                    console.log('DataTables settings:', settings);
                }
            });

            // Event delegation for handling click events
            $('#dataTableItem').on('click', '.edit-button', function() {
                var id = $(this).data('id');
                showData(id);
            });

            $('#dataTableItem').on('click', '.delete-button', function() {
                var id = $(this).data('id');
                deleteData(id);
            });
        }

        $(document).ready(function() {
            LoadDataTable();
        });

        function showData(id) {
            var url = "{{ route('User.edit', ':id') }}"; // Use named route with placeholder
            var fullUrl = url.replace(':id', id); // Replace placeholder with actual ID
            window.location.href = fullUrl; // Redirect to the constructed URL
        }

        function  deleteData(id) {
            var csrf_token = $('meta[name="csrf-token"]').attr('content');
            swal({
                title: "Are you sure?",
                text: "Once deleted, you will not be able to recover this imaginary file!",
                icon: "warning",
                buttons: true,
                dangerMode: true,
            })
                .then((willDelete) => {
                    if (willDelete) {
                        $.ajax({
                            url: "{{ url('User') }}" + '/' + id,
                            type: "POST",
                            data: {'_method': 'DELETE', '_token': csrf_token},
                            success: function (data) {
                                console.log(data);
                                var dataResult = JSON.parse(data);
                                if (dataResult.statusCode == 200) {
                                    $('#dataTableItem').DataTable().ajax.reload();
                                    swal({
                                        title: "Delete Done",
                                        text: "Poof! Your data file has been deleted!",
                                        icon: "success",
                                        button: "Done"
                                    });
                                } else {
                                    swal("Error occured !!");
                                }
                            }, error: function (data) {
                                console.log(data);
                                swal({
                                    title: "Opps...",
                                    text: "Error occured !",
                                    icon: "error",
                                    button: 'Ok ',
                                });
                            }
                        });
                    } else {
                        swal("Your imaginary file is safe!");
                    }
                });
        }

    </script>
@endsection
