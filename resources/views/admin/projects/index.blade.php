@extends('layouts.app')

@section('content')
<!-- Header with Search -->
<div class="sticky top-0 z-40 bg-[#e7e7e7]/95 backdrop-blur border-b border-black/10">
    <div class="px-6 py-4 flex items-center justify-between">
        <div>
            <h1 class="text-xl font-semibold text-[#2b2b2b]">Project Management</h1>
        </div>
        <div class="flex items-center gap-4">
            <form method="GET" action="{{ route('projects.index') }}" class="relative">
                <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-[#2b2b2b]/60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <input type="text" name="search" placeholder="Search" value="{{ request('search') }}" class="pl-11 pr-4 py-2 w-64 bg-[#dedede] text-[#2b2b2b] placeholder-[#2b2b2b]/50 border border-black/10 rounded-full text-sm focus:ring-2 focus:ring-[#f6c915] focus:bg-white transition">
            </form>
        </div>
    </div>
</div>

<!-- Projects Grid -->
<div class="min-h-screen bg-[#e7e7e7] p-6">
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-5">
        @foreach($projects as $project)
        <div class="project-card group relative rounded-2xl overflow-hidden cursor-pointer transition-all duration-300 bg-[#e7e7e7] border border-black/10 shadow-[0_8px_18px_rgba(0,0,0,0.15)] hover:-translate-y-1" onclick="window.location.href='{{ route('projects.show', $project->id) }}'">
            <!-- Project Image -->
            <div class="p-3 pb-2">
                <div class="relative h-32 overflow-hidden rounded-xl bg-[#dcdcdc] shadow-[0_6px_14px_rgba(0,0,0,0.25)]">
                    @if($project->image)
                    <img src="{{ asset('storage/' . $project->image) }}" alt="{{ $project->name }}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                    @else
                    <div class="w-full h-full bg-gradient-to-br from-[#f6c915] to-[#eab308]"></div>
                    @endif
                    <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-black/10 to-transparent"></div>
                    
                    @if($project->status === 'completed')
                    <div class="absolute top-2 right-2 px-2.5 py-1 bg-green-600 text-white text-xs rounded-full font-medium flex items-center gap-1">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Completed
                    </div>
                    @endif
                </div>
            </div>
            
            <!-- Project Info -->
            <div class="px-4 pb-4 pt-1">
                <h3 class="font-semibold text-sm text-[#2b2b2b] truncate group-hover:text-[#d19b00] transition">{{ $project->name }}</h3>
                <p class="text-xs text-[#2b2b2b]/70 mt-0.5">{{ $project->type }}</p>
                
                @if($project->status !== 'completed')
                <div class="mt-3">
                    <div class="flex items-center justify-between text-xs mb-1.5">
                        <span class="text-[#2b2b2b]/70 font-medium">Progress</span>
                        <span class="font-semibold text-[#2b2b2b]">{{ $project->progress }}%</span>
                    </div>
                    <div class="w-full bg-[#d2d2d2] rounded-full h-2 overflow-hidden">
                        <div class="bg-[#f6c915] h-2 rounded-full transition-all duration-500" style="width: {{ $project->progress }}%"></div>
                    </div>
                </div>
                @endif
            </div>
        </div>
        @endforeach
        
        <!-- Create Project Card -->
        <div class="create-card group relative rounded-xl border-2 border-dashed border-gray-300 cursor-pointer transition-all duration-300 h-[240px] flex flex-col items-center justify-center gap-3 bg-[#e7e7e7] hover:border-[#f6c915] hover:bg-[#e2e2e2] hover:-translate-y-1 hover:shadow-[0_8px_18px_rgba(0,0,0,0.15)]" onclick="window.location.href='{{ route('projects.create') }}'">
            <div class="w-14 h-14 rounded-full bg-[#d2d2d2] flex items-center justify-center transition-all duration-300 group-hover:bg-[#f6c915] group-hover:scale-110">
                <svg class="w-7 h-7 text-[#2b2b2b]/70 group-hover:text-black transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
            </div>
            <span class="font-semibold text-sm text-[#2b2b2b]/70 group-hover:text-[#d19b00] transition uppercase tracking-wide">Create Project</span>
        </div>
    </div>
</div>

<style>
    .project-card:hover {
        box-shadow: 0 14px 22px -8px rgba(0, 0, 0, 0.18);
    }
</style>
@endsection
