@extends('layout.appmain')
@section('title', '- Edit Sidebar Nav')

@section('main')

    <main id="main" class="main">

        <div class="pagetitle">
            <h1>Edit Side Menu</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{url('/')}}">Home</a></li>
                    <li class="breadcrumb-item">Web Setup</li>
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
                                <h5 class="card-title">Edit Sidebar Nav</h5>
                            </div>
                            <div class="col-md-2 mt-3 ">
                                <a href="{{url('SidebarNav')}}" type="button" class="btn btn-outline-info btn-sm text-right"> Back <i class="bi bi-arrow-left-short"></i></a>
                            </div>
                        </div>
                        <!-- Multi Columns Form -->
                        <form class="row g-3">
                            @csrf
                            <input type="hidden" id="id" name="id" value="{{ $navItem->id }}"> <!-- Hidden ID field -->

                            <div class="col-md-6">
                                <label for="parent_id" class="form-label">Parent Menu</label>
                                <select id="parent_id" class="form-select" name="parent_id">
                                    <option value="">Select Parent Menu</option>
                                    @foreach($prentMenu as $value)
                                        <option value="{{ $value->id }}" {{ $value->id == $navItem->parent_id ? 'selected' : '' }}>
                                            {{ $value->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>

                            <div class="col-md-6">
                                <label for="name" class="form-label">Name</label>
                                <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $navItem->name) }}">
                                <div class="invalid-feedback"></div>
                            </div>

                            <div class="col-md-6">
                                <label for="icon" class="form-label">Icon</label>
                                <input type="text" class="form-control" id="icon" name="icon" value="{{ old('icon', $navItem->icon) }}">
                                <div class="invalid-feedback"></div>
                            </div>

                            <div class="col-md-6">
                                <label for="url" class="form-label">URL</label>
                                <input type="text" class="form-control" id="url" name="url" value="{{ old('url', $navItem->url) }}">
                                <div class="invalid-feedback"></div>
                            </div>

                            <div class="col-md-6">
                                <label for="order" class="form-label">Order</label>
                                <input type="text" class="form-control" id="order" name="order" value="{{ old('order', $navItem->order) }}">
                                <div class="invalid-feedback"></div>
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

                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="is_collapsed" name="is_collapsed" value="1" {{ old('is_collapsed', $navItem->is_collapsed) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_collapsed">IS Collapsed</label>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="is_heading" name="is_heading" value="1" {{ old('is_heading', $navItem->is_heading) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_heading">IS Heading</label>
                                    <div class="invalid-feedback"></div>
                                </div>
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

        url = "{{ url('SidebarNav') }}";
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
                            window.location.href = "{{ url('SidebarNav') }}"; // Redirect to a different page
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
