{{-- resources\views\welcome.blade.php --}}
@extends('layout.app')

@section('head')
    <link rel="stylesheet" href="{{ asset('assets/css/home-style.css') }}">
@endsection

@section('content')
    @include('partials.header')

    <main>
        <section class="flex justify-center items-center">
            <div class="banner shadow-lg border border-blue-500">
                <div class="flex-1 flex flex-col p-3 min-w-[200px]">
                    <h1 class="text-3xl font-bold text-white">
                        BOOK APPOINTMENT WITH TRUSTED DOCTORS
                    </h1>
                    <p class="text-sm text-white">
                        Connect with certified and highly experienced doctors
                        who are committed to providing safe, reliable, and
                        personalized care for every patient, ensuring the
                        quality healthcare you truly deserve.
                    </p>
                    <a href="{{ route('home.doctor') }}" class="mt-4 btn rounded-md font-medium">
                        Book Appointment
                    </a>
                </div>

                <div class="flex-1 flex items-center justify-center min-w-[200px] bg-white p-10 rounded">
                    <img src="{{ asset('assets/images/logo/logoH.png') }}" alt="Havilah Polymedic Logo">
                </div>
            </div>
        </section>
    </main>
@endsection

@section('script')
    <script>
        $(document).ready(function () {
            $(".menu-toggle").click(function () {
                $(".links-container").toggleClass("active");
            });
        });
    </script>
@endsection