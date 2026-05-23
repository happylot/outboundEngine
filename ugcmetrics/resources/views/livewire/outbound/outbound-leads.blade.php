<div>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">
        <div class="sm:flex sm:justify-between sm:items-center mb-5">
            <div class="mb-4 sm:mb-0">
                <h1 class="text-2xl md:text-3xl text-slate-800 font-bold">Unified Leads CRM 🎯</h1>
            </div>
            <div class="grid grid-flow-col sm:auto-cols-max justify-start sm:justify-end gap-2">
                <button class="btn bg-indigo-500 hover:bg-indigo-600 text-white">Import Leads</button>
            </div>
        </div>

        <!-- Tabs -->
        <div class="mb-5 border-b border-slate-200">
            <ul class="flex flex-wrap -mb-px text-sm font-medium text-center text-slate-500">
                <li class="mr-2">
                    <button wire:click="setTab('outbound')" class="inline-block p-4 rounded-t-lg border-b-2 {{ $activeTab === 'outbound' ? 'text-indigo-600 border-indigo-600 active' : 'border-transparent hover:text-slate-600 hover:border-slate-300' }}">
                        Outbound Campaigns
                    </button>
                </li>
                <li class="mr-2">
                    <button wire:click="setTab('seeding')" class="inline-block p-4 rounded-t-lg border-b-2 {{ $activeTab === 'seeding' ? 'text-indigo-600 border-indigo-600 active' : 'border-transparent hover:text-slate-600 hover:border-slate-300' }}">
                        Seeding Campaigns
                    </button>
                </li>
            </ul>
        </div>

        <div class="bg-white shadow-lg rounded-sm border border-slate-200">
            <div class="p-3">
                @if($activeTab === 'outbound')
                <!-- Outbound Leads Table -->
                <table class="table-auto w-full">
                    <thead class="text-xs font-semibold uppercase text-slate-400 bg-slate-50">
                        <tr>
                            <th class="p-2 whitespace-nowrap"><div class="font-semibold text-left">Name</div></th>
                            <th class="p-2 whitespace-nowrap"><div class="font-semibold text-left">Salon</div></th>
                            <th class="p-2 whitespace-nowrap"><div class="font-semibold text-left">Status</div></th>
                            <th class="p-2 whitespace-nowrap"><div class="font-semibold text-center">Score</div></th>
                            <th class="p-2 whitespace-nowrap"><div class="font-semibold text-center">Actions</div></th>
                        </tr>
                    </thead>
                    <tbody class="text-sm divide-y divide-slate-100">
                        @foreach($outboundLeads as $lead)
                        <tr>
                            <td class="p-2 whitespace-nowrap"><div class="text-left font-medium text-slate-800">{{ $lead->name }}</div></td>
                            <td class="p-2 whitespace-nowrap"><div class="text-left">{{ $lead->salon_name }}</div></td>
                            <td class="p-2 whitespace-nowrap">
                                <div class="text-left">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        {{ ucfirst($lead->status) }}
                                    </span>
                                </div>
                            </td>
                            <td class="p-2 whitespace-nowrap"><div class="text-center font-medium">{{ $lead->score }}</div></td>
                            <td class="p-2 whitespace-nowrap"><div class="text-center text-indigo-500 cursor-pointer">View / Message</div></td>
                        </tr>
                        @endforeach
                        @if($outboundLeads->isEmpty())
                        <tr>
                            <td colspan="5" class="p-4 text-center text-slate-500">No outbound leads found. Import some to get started.</td>
                        </tr>
                        @endif
                    </tbody>
                </table>
                @endif

                @if($activeTab === 'seeding')
                <!-- Seeding Leads Table -->
                <table class="table-auto w-full">
                    <thead class="text-xs font-semibold uppercase text-slate-400 bg-slate-50">
                        <tr>
                            <th class="p-2 whitespace-nowrap"><div class="font-semibold text-left">Customer</div></th>
                            <th class="p-2 whitespace-nowrap"><div class="font-semibold text-left">Platform</div></th>
                            <th class="p-2 whitespace-nowrap"><div class="font-semibold text-left">Contact Info</div></th>
                            <th class="p-2 whitespace-nowrap"><div class="font-semibold text-center">Sync Status</div></th>
                        </tr>
                    </thead>
                    <tbody class="text-sm divide-y divide-slate-100">
                        @foreach($seedingLeads as $lead)
                        <tr>
                            <td class="p-2 whitespace-nowrap">
                                <div class="text-left font-medium text-slate-800">{{ $lead->customer_name }}</div>
                                <div class="text-left text-xs text-slate-500">UID: {{ $lead->customer_uid }}</div>
                            </td>
                            <td class="p-2 whitespace-nowrap"><div class="text-left">{{ ucfirst($lead->platform) }}</div></td>
                            <td class="p-2 whitespace-nowrap">
                                <div class="text-left">{{ $lead->phone ?? '-' }}</div>
                                <div class="text-left text-xs text-slate-500">{{ $lead->email ?? '-' }}</div>
                            </td>
                            <td class="p-2 whitespace-nowrap">
                                <div class="text-center">
                                    @if($lead->status === 'synced')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Synced</span>
                                    @elseif($lead->status === 'failed')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800" title="{{ $lead->sync_error }}">Failed</span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">Pending</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                        @if($seedingLeads->isEmpty())
                        <tr>
                            <td colspan="4" class="p-4 text-center text-slate-500">No seeding leads found. Use the Tester Workspace to log leads.</td>
                        </tr>
                        @endif
                    </tbody>
                </table>
                @endif
            </div>
        </div>
    </div>
</div>
