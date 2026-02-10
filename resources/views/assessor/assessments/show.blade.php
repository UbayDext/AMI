<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Assessment Details') }}
            </h2>
            <div class="flex gap-2">
                <a href="{{ route('assessor.assessments.report', $assessment) }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                    <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                    </svg>
                    Print Report
                </a>
                <a href="{{ route('assessor.assessments.fill', $assessment) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Continue Filling
                </a>
                <a href="{{ route('assessor.assessments.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                    Back to List
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Assessment Info -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="px-6 py-5 border-b border-gray-100">
                    <h3 class="text-lg font-medium text-gray-900">Information</h3>
                </div>
                <div class="px-6 py-5 bg-gray-50">
                    <dl class="grid grid-cols-1 md:grid-cols-3 gap-x-4 gap-y-6">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Unit Name</dt>
                            <dd class="mt-1 text-sm text-gray-900 font-semibold">{{ $assessment->unit_name }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Accreditation Year</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $assessment->accreditationYear->year ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Status</dt>
                            <dd class="mt-1">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $assessment->status === 'submitted' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ ucfirst($assessment->status) }}
                                </span>
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Findings Summary -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="px-6 py-5 border-b border-gray-100">
                    <h3 class="text-lg font-medium text-gray-900">Recorded Findings</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Code</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Standard</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Area</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Severity</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($assessment->findings as $f)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $f->code }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $f->standard->code }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $f->audit_area_names }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $f->title }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            {{ $f->severity === 'major' ? 'bg-red-100 text-red-800' : ($f->severity === 'critical' ? 'bg-purple-100 text-purple-800' : 'bg-yellow-100 text-yellow-800') }}">
                                        {{ ucfirst($f->severity) }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">No findings recorded.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Responses Summary -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="text-lg font-medium text-gray-900">Responses Overview</h3>
                    <span class="text-xs text-gray-500">Showing snippet of responses</span>
                </div>
                <div class="p-6 space-y-4">
                    @forelse($assessment->answers->take(5) as $ans)
                    <div class="border rounded-md p-4 bg-gray-50">
                        <div class="text-sm font-medium text-gray-900 mb-2">{{ $ans->question->label ?? 'Question deleted' }}</div>
                        <div class="flex items-center gap-4 text-xs text-gray-500">
                            <span>Status: <strong class="text-gray-700">{{ ucfirst($ans->status) }}</strong></span>
                            @if($ans->file_path)
                            <span>Attachment: <a href="{{ asset('storage/'.$ans->file_path) }}" target="_blank" class="text-indigo-600 hover:underline">View File</a></span>
                            @endif
                        </div>
                    </div>
                    @empty
                    <div class="text-sm text-gray-500 text-center">No responses yet.</div>
                    @endforelse

                    @if($assessment->answers->count() > 5)
                    <div class="text-center mt-4">
                        <a href="{{ route('assessor.assessments.fill', $assessment) }}" class="text-sm text-indigo-600 hover:text-indigo-900 font-medium">View all responses in Fill Mode &rarr;</a>
                    </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</x-app-layout>