@extends('employee.layouts.app')

@section('content')
<div class="min-h-screen bg-[#ECECEC] p-6">
    <div class="mb-6 flex items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-[#111]">My Assigned Projects</h1>
            <p class="text-sm text-gray-600 mt-1">
                Projects assigned from Admin Project creation and Team Assign Roles.
            </p>
        </div>

        <form method="GET" action="{{ route('employee.projects.employeedashboard') }}" class="w-[420px] max-w-full">
            <div class="relative">
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Search projects"
                    class="w-full rounded-full border border-gray-400 bg-[#EDEDED] px-5 py-2 pr-11 text-sm outline-none"
                >
                <div class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-600">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                        <path d="M10.5 18a7.5 7.5 0 1 1 0-15 7.5 7.5 0 0 1 0 15Z" stroke="currentColor" stroke-width="2"/>
                        <path d="M16.5 16.5 21 21" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                </div>
            </div>
        </form>
    </div>

    @if(!$teamMember)
        <div class="rounded-xl border border-yellow-300 bg-yellow-50 px-4 py-3 text-sm text-yellow-900">
            Your account is not linked to a Team Member record yet. Please contact admin.
        </div>
    @endif

    <div class="mt-6">
        @if(session('error'))
            <div class="mb-4 rounded-xl border border-red-300 bg-red-50 px-4 py-3 text-sm text-red-800">
                {{ session('error') }}
            </div>
        @endif

        @if(session('success'))
            <div class="mb-4 rounded-xl border border-green-300 bg-green-50 px-4 py-3 text-sm text-green-800">
                {{ session('success') }}
            </div>
        @endif

        @if($projects->isEmpty())
            <div class="rounded-2xl border border-gray-400 bg-[#E6E6E6] p-8 text-center text-gray-700">
                No assigned projects found.
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
                @foreach($projects as $project)
                    <a
                        href="{{ route('employee.projects.show', $project->id) }}"
                        class="group rounded-2xl overflow-hidden border border-black/10 bg-[#e7e7e7] shadow-[0_8px_18px_rgba(0,0,0,0.15)] transition hover:-translate-y-1"
                    >
                        <div class="p-3 pb-2">
                            <div class="relative h-32 overflow-hidden rounded-xl bg-[#dcdcdc] shadow-[0_6px_14px_rgba(0,0,0,0.25)]">
                                @if($project->image)
                                    <img src="{{ asset('storage/' . $project->image) }}" alt="{{ $project->name }}" class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-110">
                                @else
                                    <div class="h-full w-full bg-gradient-to-br from-[#f6c915] to-[#eab308]"></div>
                                @endif
                            </div>
                        </div>

                        <div class="px-4 pb-4 pt-1">
                            <h3 class="font-semibold text-sm text-[#2b2b2b] truncate">{{ $project->name }}</h3>
                            <p class="text-xs text-[#2b2b2b]/70 mt-0.5">{{ $project->type }}</p>

                            <div class="mt-3 flex items-center justify-between text-xs">
                                <span class="text-[#2b2b2b]/70">Status</span>
                                <span class="font-semibold text-[#2b2b2b]">{{ ucfirst($project->status ?? 'pending') }}</span>
                            </div>

                            <div class="mt-1.5 w-full bg-[#d2d2d2] rounded-full h-2 overflow-hidden">
                                <div class="bg-[#f6c915] h-2 rounded-full" style="width: {{ (int) ($project->progress ?? 0) }}%"></div>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection
