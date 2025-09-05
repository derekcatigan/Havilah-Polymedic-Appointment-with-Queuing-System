{{-- resources\views\staff\walkin-create.blade.php --}}
@extends('layout.layout')

@section('content')
    <div class="max-w-2xl mx-auto p-6 bg-white shadow-md rounded-xl">
        <h2 class="text-2xl font-bold mb-4">Add Walk-in Appointment</h2>

        <form action="{{ route('walkin.store') }}" method="POST" class="space-y-4">
            @csrf

            {{-- Patient Name --}}
            <div>
                <label class="block font-medium">Patient Name</label>
                <input type="text" name="patient_name" class="input input-bordered w-full" required>
            </div>

            {{-- Select Doctor --}}
            <div>
                <label class="block font-medium">Assign Doctor</label>
                <select name="doctor_user_id" class="select select-bordered w-full" required>
                    <option value="">-- Select Doctor --</option>
                    @foreach($doctors as $doctor)
                        <option value="{{ $doctor->id }}">
                            {{ $doctor->name }} ({{ ucfirst($doctor->doctor->specialty ?? 'N/A') }})
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Actions --}}
            <div class="flex justify-end gap-3">
                <a href="{{ route('manage.appointment') }}" class="btn">Cancel</a>
                <button type="submit" class="btn btn-success">Book Walk-in</button>
            </div>
        </form>
    </div>
@endsection