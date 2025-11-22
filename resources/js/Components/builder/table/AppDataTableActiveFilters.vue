<script lang="ts" setup>
import { Button } from '@/components/ui/button';
import {
    Popover,
    PopoverContent,
    PopoverTrigger,
} from '@/components/ui/popover';
import { cn } from '@/lib/utils';
import { useDatatableStore } from '@/stores/datatable';
import { X } from 'lucide-vue-next';
import AppDataTableFilterInput from './AppDataTableFilterInput.vue';
import AppDataTableFilterOperator from './AppDataTableFilterOperator.vue';

interface Props {
    idx: string;
}

const props = defineProps<Props>();

const store = useDatatableStore(props.idx)();
</script>

<template>
    <div
        :class="
            cn(
                'flex flex-wrap items-center gap-2 px-2',
                store.activeFilters.length > 0 ? 'mb-2' : '',
            )
        "
    >
        <template v-for="filter in store.activeFilters" :key="filter.field">
            <Popover v-model:open="store.openFilterPopovers[filter.field]">
                <div class="group relative flex items-center">
                    <PopoverTrigger as-child>
                        <Button
                            variant="outline"
                            size="sm"
                            class="gap-2 border-dashed border-muted-foreground pr-1 transition-colors hover:bg-accent"
                        >
                            <span class="text-muted-foreground">
                                {{
                                    store.tableData.filters.opt?.find(
                                        (f) => f.field === filter.field,
                                    )?.label
                                }}
                            </span>
                            <span class="text-muted-foreground">
                                {{ filter.operator ?? '' }}
                            </span>
                            <span class="font-semibold">
                                {{
                                    store.getDisplayFilter(
                                        filter,
                                        store.tableData.filters.opt?.find(
                                            (f) => f.field === filter.field,
                                        )!,
                                    )
                                }}
                            </span>
                            <span
                                class="ml-1 flex h-5 w-5 items-center justify-center rounded-sm transition-colors hover:bg-destructive/10 hover:text-destructive"
                                @click.stop.prevent="
                                    store.removeFilter(filter.field)
                                "
                            >
                                <X class="h-3 w-3" />
                            </span>
                        </Button>
                    </PopoverTrigger>
                </div>

                <PopoverContent class="w-full p-2" align="start">
                    <div class="flex w-full flex-col gap-2">
                        <AppDataTableFilterOperator
                            :idx="idx"
                            :activeFilter="filter"
                        />
                        <AppDataTableFilterInput
                            :idx="idx"
                            :activeFilter="filter"
                        />
                    </div>
                </PopoverContent>
            </Popover>
        </template>
    </div>
</template>
