@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-[#ECECEC] px-10 py-8">
    <div class="flex items-start justify-between gap-6 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-[#111]">Account Management</h1>
            <p class="text-sm text-gray-600 mt-1">Create employee login and record</p>
        </div>
        <a href="{{ route('team.documents') }}"
           class="inline-flex items-center gap-2 rounded-lg bg-[#f6c915] px-4 py-2 text-sm font-semibold text-black hover:brightness-95 transition">
            Back to Records
        </a>
    </div>

    <div class="rounded-2xl border border-gray-400 bg-[#E6E6E6] p-6">
        @if($errors->any())
            <div class="mb-4 rounded-lg border border-red-300 bg-red-50 px-4 py-3 text-sm text-red-700">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('admin.account.store') }}" enctype="multipart/form-data" class="grid grid-cols-12 gap-6">
            @csrf

            <div class="col-span-12">
                <div class="text-sm font-semibold text-gray-800">Employee Details</div>
            </div>

            <div class="col-span-12 md:col-span-6">
                <label class="text-xs text-gray-700">Full Name</label>
                <input name="name" value="{{ old('name') }}" required
                       class="mt-2 w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm outline-none">
            </div>
            <div class="col-span-12 md:col-span-6">
                <label class="text-xs text-gray-700">Role</label>
                <input name="role" value="{{ old('role') }}" required
                       class="mt-2 w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm outline-none">
            </div>

            <div class="col-span-12 md:col-span-6">
                <label class="text-xs text-gray-700">Location</label>
                <select name="location" required
                        class="mt-2 w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm outline-none">
                    <option value="Onsite" @selected(old('location') === 'Onsite')>Onsite</option>
                    <option value="Remote" @selected(old('location') === 'Remote')>Remote</option>
                </select>
            </div>
            <div class="col-span-12 md:col-span-6">
                <label class="text-xs text-gray-700">Salary</label>
                <input name="salary" value="{{ old('salary') }}" required
                       class="mt-2 w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm outline-none">
            </div>

            <div class="col-span-12 md:col-span-6">
                <label class="text-xs text-gray-700">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required
                       class="mt-2 w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm outline-none">
            </div>
            <div class="col-span-12 md:col-span-6">
                <label class="text-xs text-gray-700">Phone</label>
                <input name="phone" value="{{ old('phone') }}" required
                       class="mt-2 w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm outline-none">
            </div>

            <div class="col-span-12 md:col-span-6">
                <label class="text-xs text-gray-700">Profile Picture (Optional)</label>
                <input type="file" name="avatar" accept=".jpg,.jpeg,.png,.webp"
                       class="mt-2 w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm outline-none file:mr-3 file:rounded file:border-0 file:bg-[#f6c915] file:px-3 file:py-1 file:text-xs file:font-semibold">
            </div>

            <div class="col-span-12 md:col-span-6">
                <label class="text-xs text-gray-700">Gender</label>
                <input name="gender" value="{{ old('gender') }}" required
                       class="mt-2 w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm outline-none">
            </div>
            <div class="col-span-12 md:col-span-6">
                <label class="text-xs text-gray-700">Date of Birth</label>
                <input type="date" name="date_of_birth" value="{{ old('date_of_birth') }}" required
                       class="mt-2 w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm outline-none">
            </div>

            <div class="col-span-12 md:col-span-6">
                <label class="text-xs text-gray-700">Nationality</label>
                <input name="nationality" value="{{ old('nationality') }}" required
                       class="mt-2 w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm outline-none">
            </div>
            <div class="col-span-12 md:col-span-6">
                <label class="text-xs text-gray-700">Address Line</label>
                <input name="address_line" value="{{ old('address_line') }}" required
                       class="mt-2 w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm outline-none">
            </div>

            <div class="col-span-12 md:col-span-6">
                <label class="text-xs text-gray-700">City</label>
                <input name="address_city" value="{{ old('address_city') }}" required
                       class="mt-2 w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm outline-none">
            </div>
            <div class="col-span-12 md:col-span-6">
                <label class="text-xs text-gray-700">State/Province</label>
                <input name="address_state" value="{{ old('address_state') }}" required
                       class="mt-2 w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm outline-none">
            </div>

            <div class="col-span-12 mt-2">
                <div class="text-sm font-semibold text-gray-800">Login Credentials</div>
            </div>

            <div class="col-span-12 md:col-span-6">
                <label class="text-xs text-gray-700">Password</label>
                <input type="password" name="password" required
                       class="mt-2 w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm outline-none">
            </div>
            <div class="col-span-12 md:col-span-6">
                <label class="text-xs text-gray-700">Confirm Password</label>
                <input type="password" name="password_confirmation" required
                       class="mt-2 w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm outline-none">
            </div>

            <div class="col-span-12 flex justify-end gap-3 mt-2">
                <button type="submit"
                        class="inline-flex items-center gap-2 rounded-lg bg-[#f6c915] px-6 py-2.5 text-sm font-semibold text-black hover:brightness-95 transition">
                    Create Account
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
