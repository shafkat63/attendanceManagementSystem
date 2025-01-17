@extends('layout.public')
@section('title', ' Login')

@section('publicMain')
    <div class="container">

        <section class="section register min-vh-100 d-flex flex-column align-items-center justify-content-center py-4">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-4 col-md-6 d-flex flex-column align-items-center justify-content-center">

                        <div class="d-flex justify-content-center py-4">
                            <a href="index.html" class="logo d-flex align-items-center w-auto">
                                <img src="" alt="" id="logoPreview">
                                <span class="d-none d-lg-block" id="brandName"></span>
                            </a>
                        </div><!-- End Logo -->

                        <div class="card mb-3">

                            <div class="card-body UserLogin">

                                <div class="pt-4 pb-2">
                                    <h5 class="card-title text-center pb-0 fs-4">Login to Your Account</h5>
                                    <p class="text-center small">Enter your username & password to login</p>
                                </div>

                                <form class="row g-3 needs-validation" novalidate>@csrf
                                    <input type="hidden" name="latitude" id="latitude">
                                    <input type="hidden" name="longitude" id="longitude">
                                    <div class="col-12">
                                        <label for="yourUsername" class="form-label">Username</label>
                                        <div class="input-group has-validation">
                                            <span class="input-group-text" id="inputGroupPrepend">@</span>
                                            <input type="text" name="email" class="form-control" id="email"
                                                   required>
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <label for="yourPassword" class="form-label">Password</label>
                                        <input type="password" name="password" class="form-control" id="password"
                                               required>
                                        <div class="invalid-feedback"></div>
                                    </div>

                                    <div class="col-12">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="remember" value="true"
                                                   id="rememberMe">
                                            <label class="form-check-label" for="rememberMe">Remember me</label>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <button class="btn btn-primary w-100" type="button" onclick="loginNow()">Login</button>
                                    </div>
                                    {{--<div class="col-12">
                                        <p class="small mb-0">Don't have account? <a href="pages-register.html">Create an account</a></p>
                                    </div>--}}
                                </form>

                            </div>
                        </div>

                        <div class="credits">
                            Designed by <a target="_blank" href="https://sourceofcapacity.com/">SOC</a>
                        </div>

                    </div>
                </div>
            </div>

        </section>

    </div>
    <!-- End #main -->
@endsection

@section('script')
    <script>
        getLocation();
        function getLocation() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        var latitude = position.coords.latitude;
                        var longitude = position.coords.longitude;
                        document.getElementById('latitude').value = latitude;
                        document.getElementById('longitude').value = longitude;
                    },
                    function(error) {
                        console.error('Error getting location:', error);
                        var retry = confirm('Location access is required for this feature. Please enable location services and try again. Click "OK" to retry or "Cancel" to submit without location.');
                        if (retry) {
                            getLocation();
                        }
                    }
                );
            } else {
                console.error('Geolocation is not supported by this browser.');
                alert('Geolocation is not supported by this browser. Some features may not work properly.');
            }
        }

        function loginNow() {
            url = "{{ url('requestLogin') }}";
            $.ajax({
                url: url,
                type: "POST",
                data: new FormData($(".UserLogin form")[0]),
                contentType: false,
                processData: false,
                success: function (data) {
                    console.log(data);
                    var dataResult = JSON.parse(data);
                    if (dataResult.statusCode == 200) {
                        window.location.href = dataResult.route;
                        $('.UserLogin form')[0].reset();
                    }else if (dataResult.statusCode == 204) {
                        showErrors(dataResult.errors);
                    }else{
                            swal({
                                title: "Oops",
                                text: dataResult.statusMsg,
                                icon: "error",
                                timer: '1500'
                            });

                        }
                }, error: function (data) {
                    swal({
                        title: "Oops",
                        text: "Error occured",
                        icon: "error",
                        timer: '1500'
                    });
                }
            });
            return false;


        };

        function showErrors(errors) {
            $('.form-control').removeClass('is-invalid');
            $('.invalid-feedback').remove();

            $.each(errors, function (key, errorMessages) {
                var inputElement = $('#' + key);
                inputElement.addClass('is-invalid');

                var errorDiv = $('<div class="invalid-feedback"></div>').text(errorMessages[0]);
                inputElement.after(errorDiv);
            });
        }
    </script>
@endsection
