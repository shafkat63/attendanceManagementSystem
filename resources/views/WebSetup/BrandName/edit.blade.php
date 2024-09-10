@extends('layout.appmain')
@section('title', '- Edit Brand Name')

@section('main')

    <main id="main" class="main">

        <div class="pagetitle">
            <h1>Edit Brand Name</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{url('/')}}">Home</a></li>
                    <li class="breadcrumb-item">Web Setup</li>
                    <li class="breadcrumb-item active">Edit Brand Name</li>
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
                                <h5 class="card-title">Edit Brand Name</h5>
                            </div>
                            <div class="col-md-2 mt-3 ">
                                <a href="{{url('BrandName')}}" type="button" class="btn btn-outline-info btn-sm text-right"> Back <i class="bi bi-arrow-left-short"></i></a>
                            </div>
                        </div>
                        <!-- Multi Columns Form -->
                        <form class="row g-3">
                            @csrf
                            <input type="hidden" id="id" name="id" value="{{ $navItem->id }}"> <!-- Hidden ID field -->

                            <div class="col-md-6">
                                <label for="name" class="form-label">Name</label>
                                <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $navItem->name) }}">
                                <div class="invalid-feedback"></div>
                            </div>

                            <div class="col-md-6">
                                <label for="details" class="form-label">Details</label>
                                <input type="text" class="form-control" id="details" name="details" value="{{ old('name', $navItem->name) }}">
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6">
                                <label for="logo" class="form-label">Logo</label>
                                <input type="file" class="form-control" id="logo" name="logo" accept="image/*">
                                <div class="invalid-feedback"></div>
                            </div>

                            <div class="col-md-6">
                                <!-- Display the existing image if available -->
                                @if ($navItem->logo)
                                    <img id="logoPreview" src="{{ asset($navItem->logo) }}" alt="Current Image" style="max-width: 20%; height: auto;"/>
                                @else
                                    <img id="logoPreview" src="" alt="Image Preview" style="display:none; max-width: 20%; height: auto;"/>
                                @endif
                            </div>
                            <div class="col-md-6">
                                <label for="status" class="form-label">Status</label>
                                <select id="status" class="form-select" name="status">
                                    <option value="">Select Status</option>
                                    <option value="A" {{ old('status', $navItem->status) == 'A' ? 'selected' : '' }}>Active</option>
                                    <option value="I" {{ old('status', $navItem->status) == 'I' ? 'selected' : '' }}>InActive</option>
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

        url = "{{ url('BrandName') }}";
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
                            window.location.href = "{{ url('BrandName') }}"; // Redirect to a different page
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
        return false;
    }
</script>
