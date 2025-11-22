<script setup lang="ts">
import { Menu, MenuButton, MenuItem, MenuItems } from '@headlessui/vue';
import { router, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';
import { route } from 'ziggy-js';

// ui components
import {
    AlertDialog,
    AlertDialogAction,
    AlertDialogCancel,
    AlertDialogContent,
    AlertDialogDescription,
    AlertDialogFooter,
    AlertDialogHeader,
    AlertDialogTitle,
} from '@/components/ui/alert-dialog';
import { Button } from '@/components/ui/button';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuGroup,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { Input } from '@/components/ui/input';

// icons
import {
    CheckIcon,
    Eye,
    Filter as FilterIconComponent,
    WrenchIcon,
    XIcon,
} from 'lucide-vue-next';

// types
import { useDatatableStore } from '@/stores/datatable';
import type { Action } from '@/types/datatable';

const props = defineProps<{
    idx: string;
}>();

const store = useDatatableStore(props.idx)();

defineSlots<{
    toolbarAction?: () => any;
}>();

const confirmAction = ref<null | {
    name: string;
    label: string;
    message: string;
}>(null);

const { url } = usePage();

const handleAction = (action: Action) => {
    if (action.rowSelected && store.selectedIds.length === 0) return;

    if (action.needConfirm) {
        confirmAction.value = action;
        return;
    }

    const routeUrl =
        store.tableData.actionRoute ??
        route(`${store.tableData.baseRoute}.actions`);
    router.visit(routeUrl, {
        method: 'post',
        data: {
            ids: store.selectedIds,
            action: action.name,
            url,
        },
        preserveScroll: true,
        preserveState: true,
    });
};

const confirmAndRunAction = () => {
    if (!confirmAction.value) return;
    const routeUrl =
        store.tableData.actionRoute ??
        route(`${store.tableData.baseRoute}.actions`);
    router.visit(routeUrl, {
        method: 'post',
        data: {
            ids: store.selectedIds,
            action: confirmAction.value.name,
        },
        preserveScroll: true,
    });
    confirmAction.value = null;
};
</script>

<template>
    <div class="flex items-center justify-between gap-2 p-2">
        <!-- Search -->
        <div class="flex items-center gap-2">
            <Input
                placeholder="Search..."
                v-model="store.searchQuery"
                class="max-w-xs sm:max-w-sm"
            />
        </div>

        <!-- Toolbar -->
        <div class="flex items-center gap-2">
            <!-- Actions -->
            <DropdownMenu v-if="store.tableData.actions.length > 0">
                <DropdownMenuTrigger as-child>
                    <Button
                        class="flex cursor-pointer items-center gap-2"
                        variant="outline"
                    >
                        <WrenchIcon />
                        <span class="hidden sm:block">Action</span>
                    </Button>
                </DropdownMenuTrigger>
                <DropdownMenuContent align="start">
                    <DropdownMenuGroup>
                        <DropdownMenuItem
                            v-for="action in store.tableData.actions"
                            :key="action.name"
                            class="cursor-pointer"
                            :disabled="
                                action.rowSelected &&
                                store.selectedIds.length === 0
                            "
                            @select.prevent="handleAction(action)"
                        >
                            {{ action.label }}
                        </DropdownMenuItem>
                        <slot name="toolbarAction" />
                    </DropdownMenuGroup>
                </DropdownMenuContent>
            </DropdownMenu>

            <!-- Filters -->
            <Menu as="div" class="relative inline-block text-left">
                <MenuButton as-child>
                    <Button variant="outline" class="gap-2">
                        <FilterIconComponent class="h-4 w-4" />
                        <span class="hidden sm:block">Filters</span>
                        <span
                            v-if="store.hasFilters"
                            class="ml-1 text-xs font-semibold"
                        >
                            {{ store.activeFilters.length }}
                        </span>
                    </Button>
                </MenuButton>

                <MenuItems
                    class="absolute left-0 z-50 mt-2 origin-top-left rounded-md border bg-background p-2 shadow-lg ring-1 ring-border focus:outline-none"
                    enter="transition ease-out duration-100"
                    enter-from="transform opacity-0 scale-95"
                    enter-to="transform opacity-100 scale-100"
                    leave="transition ease-in duration-75"
                    leave-from="transform opacity-100 scale-100"
                    leave-to="transform opacity-0 scale-95"
                >
                    <div class="space-y-1">
                        <p
                            class="px-2 py-1.5 text-sm font-medium data-[inset]:pl-8"
                        >
                            Add filter
                        </p>

                        <!-- ĐÚNG CÁCH: Dùng as="div" hoặc as="li" -->
                        <MenuItem
                            v-for="filter in store.tableData.filters.opt"
                            :key="filter.field"
                            as="div"
                            v-slot="{ active, close }"
                        >
                            <div
                                @click="
                                    () => {
                                        store.addFilter(filter.field);
                                        close();
                                    }
                                "
                                class="flex w-full cursor-pointer items-center gap-2 rounded-sm px-2 py-1.5 text-sm transition-colors select-none"
                                :class="
                                    active
                                        ? 'bg-accent text-accent-foreground'
                                        : 'hover:bg-accent hover:text-accent-foreground'
                                "
                            >
                                <CheckIcon
                                    v-if="
                                        store.activeFilters.some(
                                            (f) => f.field === filter.field,
                                        )
                                    "
                                    class="h-4 w-4"
                                />
                                <div v-else class="h-4 w-4" />
                                <span class="truncate">{{ filter.label }}</span>
                            </div>
                        </MenuItem>
                    </div>
                </MenuItems>
            </Menu>

            <!-- Columns -->
            <DropdownMenu>
                <DropdownMenuTrigger as-child>
                    <Button variant="outline" class="cursor-pointer gap-2">
                        <Eye class="h-4 w-4" />
                        <span class="hidden sm:block">Columns</span>
                    </Button>
                </DropdownMenuTrigger>
                <DropdownMenuContent align="end">
                    <DropdownMenuLabel>Toggle Columns</DropdownMenuLabel>
                    <DropdownMenuGroup>
                        <DropdownMenuItem
                            v-for="col in store.tableData.columns"
                            :key="col.name"
                            @select="store.toggleColumn(col.name)"
                            class="flex cursor-pointer items-center gap-2"
                        >
                            <component
                                :is="
                                    !store.hiddenColumns[col.name]
                                        ? CheckIcon
                                        : XIcon
                                "
                            />
                            <span>{{ col.label }}</span>
                        </DropdownMenuItem>
                    </DropdownMenuGroup>
                </DropdownMenuContent>
            </DropdownMenu>
        </div>
    </div>

    <!-- Modal konfirmasi -->
    <AlertDialog
        :open="!!confirmAction"
        @update:open="
            (open) => {
                if (!open) confirmAction = null;
            }
        "
    >
        <AlertDialogContent>
            <AlertDialogHeader>
                <AlertDialogTitle>Confirmation</AlertDialogTitle>
                <AlertDialogDescription>
                    {{
                        confirmAction?.message ??
                        `Are you sure want to run this action ${confirmAction?.label}?`
                    }}
                    <div class="mt-2 text-sm text-muted-foreground">
                        {{ store.selectedIds.join(', ') }}
                    </div>
                </AlertDialogDescription>
            </AlertDialogHeader>
            <AlertDialogFooter>
                <AlertDialogCancel>Cancel</AlertDialogCancel>
                <AlertDialogAction @click="confirmAndRunAction"
                    >Proceed</AlertDialogAction
                >
            </AlertDialogFooter>
        </AlertDialogContent>
    </AlertDialog>
</template>
