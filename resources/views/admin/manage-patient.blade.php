{{-- resources\views\admin\manage-patient.blade.php --}}
@extends('layout.layout')

@section('content')
    {{-- Header --}}
    <div class="flex justify-between items-center flex-wrap p-5">
        <div>
            <form id="searchForm" class="flex items-center gap-2">
                <label class="input">
                    <svg class="h-[1em] opacity-50" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                        <g stroke-linejoin="round" stroke-linecap="round" stroke-width="2.5" fill="none"
                            stroke="currentColor">
                            <circle cx="11" cy="11" r="8"></circle>
                            <path d="m21 21-4.3-4.3"></path>
                        </g>
                    </svg>
                    <input type="text" name="search" class="w-full" placeholder="Search" autocomplete="off" />
                </label>

                <button type="submit" class="btn btn-sm btn-primary">Search</button>
            </form>
        </div>

        <div>
            <a href="#" class="btn btn-sm btn-primary">Add Patients</a>
        </div>
    </div>

    {{-- Body --}}
    <div class="w-full">
        <div class="rounded-box border border-gray-300 bg-base-100 h-[600px]">
            <table class="table table-zebra w-full">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="text-center"></th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="6" class="text-center">No patients found</td>
                    </tr>

                </tbody>
            </table>
            <div class="p-5">
                {{-- {{ $accounts->links() }} --}}
            </div>
        </div>
    </div>
@endsection