@extends('layout.appmain')
@section('title', '- Edit Sidebar Nav')

@section('main')

    <main id="main" class="main">

        <div class="pagetitle">
            <h1>Edit Branch Info</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{url('/')}}">Home</a></li>
                    <li class="breadcrumb-item">User Config</li>
                    <li class="breadcrumb-item active">Edit  Side Menu</li>
                </ol>
            </nav>
        </div>
        <!-- End Page Title -->

        <section class="section dashboard">
            <div class="row">
                <div class="card">
                    <div class="card-body" id="addFrom">
                        <div class="row">
                            <div class="col-md-10">
                                <h5 class="card-title">Edit Branch Info</h5>
                            </div>
                            <div class="col-md-2 mt-3 ">
                                <a href="{{url('BranchInfo')}}" type="button" class="btn btn-outline-info btn-sm text-right"> Back <i class="bi bi-arrow-left-short"></i></a>
                            </div>
                        </div>
                        <!-- Multi Columns Form -->
                        <form class="row g-3">
                            @csrf
                            <input type="hidden" id="id" name="id" value="{{ $rowItem->id }}"> <!-- Hidden ID field -->

                            <div class="col-md-6">
                                <label for="name" class="form-label">Name</label>
                                <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $rowItem->name) }}">
                                <div class="invalid-feedback"></div>
                            </div>

                            <div class="col-md-6">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $rowItem->email) }}">
                                <div class="invalid-feedback"></div>
                            </div>

                            <div class="col-md-12">
                                <label for="address" class="form-label">Address</label>
                                <input type="text" class="form-control" id="address" name="address" value="{{ old('address', $rowItem->address) }}">
                                <div class="invalid-feedback"></div>
                            </div>

                            <div class="col-md-6">
                                <label for="phone" class="form-label">Phone</label>
                                <input type="number" class="form-control" id="phone" name="phone" value="{{ old('phone', $rowItem->phone) }}">
                                <div class="invalid-feedback"></div>
                            </div>

                            <div class="col-md-6">
                                <label for="status" class="form-label">Status</label>
                                <select id="status" class="form-select" name="status">
                                    <option value="">Select Status</option>
                                    <option value="A" {{ old('status', $rowItem->status) == 'A' ? 'selected' : '' }}>Active</option>
                                    <option value="I" {{ old('status', $rowItem->status) == 'I' ? 'selected' : '' }}>InActive</option>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>

                            <div class="text-center">
                                <button type="button" onclick="updateData()" class="btn btn-primary">Update</button>
                                <button type="reset" class="btn btn-secondary">Reset</button>
                            </div>
                        </form>
                        <!-- End Multi Columns Form -->

                    </div>
                </div>
            </div>
        </section>

    </main>
    <!-- End #main -->
@endsection
@section('script')
<script>

    function updateData() {

        url = "{{ url('BranchInfo') }}";
        $.ajax({
            url: url,
            type: "POST",
            data: new FormData($("#addFrom form")[0]),
            contentType: false,
            processData: false,
            success: function (data) {
                var dataResult = JSON.parse(data);
                if (dataResult.statusCode == 200) {
                    swal("Success", dataResult.statusMsg).then((result) => {
                            window.location.href = "{{ url('BranchInfo') }}"; // Redirect to a different page
                    });
                } else if (dataResult.statusCode == 204) {
                    showErrors(dataResult.errors);
                } else {
                    swal({
                        title: "Oops",
                        text: dataResult.statusMsg,
                        icon: "error",
                        timer: '1500'
                    });
                }
            },
            error: function (data) {
                console.log(data);
                swal({
                    title: "Oops",
                    text: "Error occurred",
                    icon: "error",
                    timer: '1500'
                });
            }
        });
        return false; // Prevent default form submission
    }
</script>
