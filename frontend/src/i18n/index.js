import {createI18n} from "vue-i18n";
import de from "@/locales/de.json";
import en from "@/locales/en.json";

export const i18n = createI18n({
    locale: 'de',
    fallbackLocale: 'en',
    messages: { de, en },
})