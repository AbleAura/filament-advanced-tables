<div
    x-data="{
        groups: @entangle('groups').live,
        operators: @js([
            'contains'     => 'Contains',
            'not_contains' => 'Does not contain',
            'equals'       => 'Equals',
            'not_equals'   => 'Does not equal',
            'starts_with'  => 'Starts with',
            'ends_with'    => 'Ends with',
            'greater_than' => 'Greater than',
            'less_than'    => 'Less than',
            'is_null'      => 'Is empty',
            'is_not_null'  => 'Is not empty',
        ]),
        needsValue(operator) {
            return !['is_null', 'is_not_null'].includes(operator);
        },
    }"
    class="filament-advanced-tables-filter-builder-panel space-y-3 py-1"
>
    {{-- Groups --}}
    <template x-for="(group, groupIndex) in groups" :key="groupIndex">
        <div class="rounded-lg border border-gray-200 dark:border-white/10 overflow-hidden">

            {{-- Group header --}}
            <div class="flex items-center justify-between px-3 py-2 bg-gray-50 dark:bg-gray-900/40 border-b border-gray-200 dark:border-white/10">
                <div class="flex items-center gap-2">
                    <span class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Match</span>
                    <select
                        x-model="group.logic"
                        class="rounded border border-gray-300 dark:border-white/10 bg-white dark:bg-gray-700 text-xs text-gray-700 dark:text-gray-200 px-2 py-0.5 focus:outline-none focus:ring-1 focus:ring-primary-500"
                    >
                        <option value="and">All (AND)</option>
                        <option value="or">Any (OR)</option>
                    </select>
                </div>
                <button
                    @click="groups.splice(groupIndex, 1)"
                    type="button"
                    class="p-1 rounded text-gray-400 hover:text-danger-500 hover:bg-danger-50 dark:hover:bg-danger-900/20 transition"
                >
                    <x-heroicon-o-x-mark class="w-3.5 h-3.5" />
                </button>
            </div>

            {{-- Conditions --}}
            <div class="p-2 space-y-2">
                <template x-for="(condition, conditionIndex) in group.conditions" :key="conditionIndex">
                    <div class="flex items-center gap-2">

                        {{-- Column --}}
                        <select
                            x-model="condition.column"
                            class="flex-1 rounded-lg border border-gray-300 dark:border-white/10 bg-white dark:bg-gray-700 text-sm text-gray-700 dark:text-gray-200 px-2 py-1.5 focus:outline-none focus:ring-2 focus:ring-primary-500 min-w-0"
                        >
                            <option value="">Select column</option>
                            @foreach ($this->getFilterableColumns() as $col)
                                <option value="{{ $col['column'] }}">{{ $col['label'] }}</option>
                            @endforeach
                        </select>

                        {{-- Operator --}}
                        <select
                            x-model="condition.operator"
                            class="w-36 flex-shrink-0 rounded-lg border border-gray-300 dark:border-white/10 bg-white dark:bg-gray-700 text-sm text-gray-700 dark:text-gray-200 px-2 py-1.5 focus:outline-none focus:ring-2 focus:ring-primary-500"
                        >
                            <template x-for="(label, op) in operators" :key="op">
                                <option :value="op" x-text="label"></option>
                            </template>
                        </select>

                        {{-- Value --}}
                        <template x-if="needsValue(condition.operator)">
                            <input
                                x-model="condition.value"
                                type="text"
                                placeholder="Value"
                                class="flex-1 min-w-0 rounded-lg border border-gray-300 dark:border-white/10 bg-white dark:bg-gray-700 text-sm text-gray-900 dark:text-white placeholder-gray-400 px-2 py-1.5 focus:outline-none focus:ring-2 focus:ring-primary-500"
                            />
                        </template>
                        <template x-if="!needsValue(condition.operator)">
                            <div class="flex-1 min-w-0"></div>
                        </template>

                        {{-- Remove --}}
                        <button
                            @click="group.conditions.splice(conditionIndex, 1); if(group.conditions.length === 0) groups.splice(groupIndex, 1)"
                            type="button"
                            class="p-1.5 flex-shrink-0 rounded text-gray-400 hover:text-danger-500 hover:bg-danger-50 dark:hover:bg-danger-900/20 transition"
                        >
                            <x-heroicon-o-minus-circle class="w-4 h-4" />
                        </button>
                    </div>
                </template>

                {{-- Add condition --}}
                <button
                    @click="group.conditions.push({ column: '', operator: 'contains', value: '' })"
                    type="button"
                    class="flex items-center gap-1 text-xs text-primary-600 dark:text-primary-400 hover:underline mt-1"
                >
                    <x-heroicon-o-plus class="w-3.5 h-3.5" />
                    Add condition
                </button>
            </div>
        </div>
    </template>

    {{-- Add group --}}
    <button
        @click="groups.push({ logic: 'and', conditions: [{ column: '', operator: 'contains', value: '' }] })"
        type="button"
        class="w-full flex items-center justify-center gap-1.5 px-3 py-2 rounded-lg border border-dashed border-gray-300 dark:border-white/20 text-sm text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition"
    >
        <x-heroicon-o-plus class="w-4 h-4" />
        Add Filter Group
    </button>
</div>
