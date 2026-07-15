<script setup lang="ts">
import { Button } from '@/components/ui/button';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { useLocale } from '@/composables/useLocale';
import { Languages } from 'lucide-vue-next';

const { currentLocale, availableLocales, setLocale, getLocaleName } =
    useLocale();
</script>

<template>
    <DropdownMenu>
        <DropdownMenuTrigger as-child>
            <Button variant="ghost" size="icon">
                <Languages class="h-5 w-5" />
                <span class="sr-only">Switch Language</span>
            </Button>
        </DropdownMenuTrigger>
        <DropdownMenuContent align="end">
            <DropdownMenuItem
                v-for="locale in availableLocales"
                :key="locale"
                @click="setLocale(locale)"
                :class="{ 'bg-accent': currentLocale === locale }"
            >
                {{ getLocaleName(locale) }}
            </DropdownMenuItem>
        </DropdownMenuContent>
    </DropdownMenu>
</template>
