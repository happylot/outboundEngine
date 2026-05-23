<div>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">
        <div class="sm:flex sm:justify-between sm:items-center mb-8">
            <div class="mb-4 sm:mb-0">
                <h1 class="text-2xl md:text-3xl text-slate-800 font-bold">Sales Brain Knowledge Base ✨</h1>
            </div>
            <div class="grid grid-flow-col sm:auto-cols-max justify-start sm:justify-end gap-2">
                <button wire:click="$set('showModal', true)" class="btn bg-indigo-500 hover:bg-indigo-600 text-white">Add Entry</button>
            </div>
        </div>

        @if(session()->has('message'))
            <div class="mb-4 px-4 py-3 bg-green-100 border border-green-200 text-green-700 rounded">
                {{ session('message') }}
            </div>
        @endif

        @if($showModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity bg-slate-900 bg-opacity-75" aria-hidden="true" wire:click="$set('showModal', false)"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div class="inline-block px-4 pt-5 pb-4 overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                    <div>
                        <h3 class="text-lg font-medium leading-6 text-slate-900" id="modal-title">Add Knowledge Base Entry</h3>
                        <div class="mt-4 space-y-4 text-left">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Title</label>
                                <input type="text" wire:model="title" class="w-full px-3 py-2 border rounded-md border-slate-300 focus:outline-none focus:ring-1 focus:ring-indigo-500 text-sm" placeholder="e.g. Handling Pricing Objection">
                                @error('title') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Type</label>
                                <select wire:model="type" class="w-full px-3 py-2 border rounded-md border-slate-300 focus:outline-none focus:ring-1 focus:ring-indigo-500 text-sm">
                                    <option value="product_info">Product Info</option>
                                    <option value="sales_deck">Sales Deck</option>
                                    <option value="objection_handling">Objection Handling</option>
                                    <option value="target_persona">Target Persona</option>
                                </select>
                                @error('type') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Content</label>
                                <textarea wire:model="content" class="w-full px-3 py-2 border rounded-md border-slate-300 focus:outline-none focus:ring-1 focus:ring-indigo-500 text-sm" rows="4" placeholder="Enter the detailed knowledge here..."></textarea>
                                @error('content') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Tags (comma separated)</label>
                                <input type="text" wire:model="tags" class="w-full px-3 py-2 border rounded-md border-slate-300 focus:outline-none focus:ring-1 focus:ring-indigo-500 text-sm" placeholder="e.g. pricing, saas, objection">
                                @error('tags') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                    <div class="mt-5 sm:mt-6 sm:flex sm:flex-row-reverse">
                        <button wire:click="save" type="button" class="inline-flex justify-center w-full px-4 py-2 text-base font-medium text-white bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">Save Entry</button>
                        <button wire:click="$set('showModal', false)" type="button" class="inline-flex justify-center w-full px-4 py-2 mt-3 text-base font-medium bg-white border rounded-md shadow-sm text-slate-700 border-slate-300 hover:bg-slate-50 focus:outline-none sm:mt-0 sm:w-auto sm:text-sm">Cancel</button>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <div class="bg-white shadow-lg rounded-sm border border-slate-200">
            <div class="p-3">
                <table class="table-auto w-full">
                    <thead class="text-xs font-semibold uppercase text-slate-400 bg-slate-50">
                        <tr>
                            <th class="p-2 whitespace-nowrap"><div class="font-semibold text-left">Title</div></th>
                            <th class="p-2 whitespace-nowrap"><div class="font-semibold text-left">Type</div></th>
                            <th class="p-2 whitespace-nowrap"><div class="font-semibold text-left">Tags</div></th>
                            <th class="p-2 whitespace-nowrap"><div class="font-semibold text-center">Actions</div></th>
                        </tr>
                    </thead>
                    <tbody class="text-sm divide-y divide-slate-100">
                        @foreach($entries as $entry)
                        <tr>
                            <td class="p-2 whitespace-nowrap"><div class="text-left font-medium text-slate-800">{{ $entry->title }}</div></td>
                            <td class="p-2 whitespace-nowrap"><div class="text-left">{{ $entry->type }}</div></td>
                            <td class="p-2 whitespace-nowrap"><div class="text-left">{{ $entry->tags }}</div></td>
                            <td class="p-2 whitespace-nowrap"><div class="text-center text-indigo-500 cursor-pointer">Edit</div></td>
                        </tr>
                        @endforeach
                        @if($entries->isEmpty())
                        <tr>
                            <td colspan="4" class="p-4 text-center text-slate-500">No knowledge base entries yet.</td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
