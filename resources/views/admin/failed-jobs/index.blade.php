<x-app-layout>
    <x-slot name="header">
        <x-ui.page-header :title="__('admin.failed_jobs.title')" :subtitle="__('admin.failed_jobs.subtitle')">
            <x-slot:actions>
                @if($jobs->isNotEmpty())
                <form method="POST" action="{{ route('admin.failed-jobs.retry-all') }}">@csrf
                    <x-ui.button type="submit" variant="outline">{{ __('admin.failed_jobs.retry_all') }}</x-ui.button>
                </form>
                @endif
            </x-slot:actions>
        </x-ui.page-header>
    </x-slot>

    <x-ui.card class="overflow-x-auto p-0">
        <table class="ui-table">
            <thead>
                <tr>
                    <th>{{ __('admin.failed_jobs.queue') }}</th>
                    <th>{{ __('admin.failed_jobs.connection') }}</th>
                    <th>{{ __('admin.failed_jobs.failed_at') }}</th>
                    <th>{{ __('admin.failed_jobs.exception') }}</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
            @forelse($jobs as $job)
            <tr class="hover:bg-slate-50/50 transition-colors align-top">
                <td class="text-sm">{{ $job->queue }}</td>
                <td class="text-sm text-slate-500">{{ $job->connection }}</td>
                <td class="text-sm text-slate-500 tabular-nums whitespace-nowrap">{{ \Illuminate\Support\Carbon::parse($job->failed_at)->format('d/m/Y H:i') }}</td>
                <td class="text-xs text-slate-600 max-w-md"><code class="break-all">{{ \Illuminate\Support\Str::limit($job->exception, 200) }}</code></td>
                <td class="text-right">
                    <form method="POST" action="{{ route('admin.failed-jobs.retry', $job->uuid) }}">@csrf
                        <x-ui.button type="submit" variant="outline" size="sm">{{ __('admin.failed_jobs.retry') }}</x-ui.button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="5" class="p-0"><x-ui.empty-state icon="dashboard" :title="__('admin.failed_jobs.empty')" class="border-0 shadow-none" /></td></tr>
            @endforelse
            </tbody>
        </table>
    </x-ui.card>
</x-app-layout>
