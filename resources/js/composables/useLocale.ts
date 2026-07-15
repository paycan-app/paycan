import { usePage } from '@inertiajs/vue3';
import { loadLanguageAsync, trans } from 'laravel-vue-i18n';
import { computed } from 'vue';

export function useLocale() {
    const page = usePage();

    const currentLocale = computed(() => page.props.locale as string);
    const availableLocales = computed(() => page.props.locales as string[]);

    const setLocale = async (locale: string) => {
        if (!availableLocales.value.includes(locale)) {
            console.error(`Locale ${locale} is not supported`);
            return;
        }

        // Save to localStorage (user's personal preference)
        localStorage.setItem('locale', locale);

        // Also set cookie for backend to read
        document.cookie = `locale=${locale}; path=/; max-age=31536000; SameSite=Lax`;

        // Load the language file
        await loadLanguageAsync(locale);

        // Navigate with the new locale parameter to trigger server-side change
        window.location.href = `${window.location.pathname}?lang=${locale}`;
    };

    const getLocaleName = (locale: string): string => {
        const localeNames: Record<string, string> = {
            en: 'English',
            ar: 'العربية',
        };
        return localeNames[locale] || locale;
    };

    return {
        currentLocale,
        availableLocales,
        setLocale,
        getLocaleName,
        trans,
    };
}
