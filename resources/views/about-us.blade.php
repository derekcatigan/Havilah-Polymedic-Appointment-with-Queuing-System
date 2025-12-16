{{-- resources\views\about-us.blade.php --}}
@extends('layout.app')

@section('head')
    <link rel="stylesheet" href="{{ asset('assets/css/home-style.css') }}">
@endsection

@section('content')
    @include('partials.header')

    <section class="bg-gray-50 py-16">
        <div class="max-w-3xl mx-auto px-4">

            {{-- Card --}}
            <div class="bg-white rounded shadow-sm border border-gray-200 p-8 md:p-12 text-center">

                {{-- Mission --}}
                <div class="mb-12">
                    <h2 class="text-sm font-semibold tracking-widest text-primary uppercase mb-3">
                        Mission
                    </h2>
                    <p class="text-lg text-gray-700 leading-relaxed">
                        To preserve health and extend lives through a fully integrated network
                        with the highest quality and most affordable care.
                    </p>
                </div>

                <div class="border-t border-gray-200 my-10"></div>

                {{-- Vision --}}
                <div class="mb-12">
                    <h2 class="text-sm font-semibold tracking-widest text-primary uppercase mb-3">
                        Vision
                    </h2>
                    <p class="text-lg text-gray-700 leading-relaxed">
                        A community that appreciates the indispensability of health and thrives
                        on a culture where health is believed to be a fundamental right.
                    </p>
                </div>

                <div class="border-t border-gray-200 my-10"></div>

                {{-- Core Values --}}
                <div class="mb-12">
                    <h2 class="text-sm font-semibold tracking-widest text-primary uppercase mb-5">
                        Core Values
                    </h2>

                    <p class="text-2xl font-bold tracking-[6px] text-gray-900 mb-3">
                        C A R E
                    </p>

                    <p class="text-base text-gray-600 tracking-wide">
                        Compassion · Altruism · Respect · Excellence
                    </p>
                </div>

                <div class="border-t border-gray-200 my-10"></div>

                {{-- Contact --}}
                <div>
                    <h2 class="text-sm font-semibold tracking-widest text-primary uppercase mb-5">
                        Contact
                    </h2>

                    <div class="space-y-2 text-gray-700 text-base">
                        <p>0915 544 2203</p>
                        <p>0928 603 5222</p>
                        <p class="font-medium text-primary">
                            havilahpolymedic@gmail.com
                        </p>
                    </div>
                </div>

            </div>
        </div>
    </section>
@endsection