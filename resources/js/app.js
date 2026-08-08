import "../css/app.css";

import { createInertiaApp } from "@inertiajs/vue3";
import { resolvePageComponent } from "laravel-vite-plugin/inertia-helpers";
import { createApp, h } from "vue";
import { ZiggyVue } from "../../vendor/tightenco/ziggy";

import { Quasar, Dark, Loading, Notify } from "quasar";

// Import Quasar css & Material Icons (sesuai icon yang dipakai di Dashboard.vue)
import "quasar/src/css/index.sass";
import "@quasar/extras/material-icons/material-icons.css";

const appName = import.meta.env.VITE_APP_NAME || "Laravel";

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob("./Pages/**/*.vue"),
        ),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(Quasar, {
                plugins: { Dark, Loading, Notify },
            })
            .use(ZiggyVue)
            .mount(el);
    },
    progress: {
        color: "#4B5563",
    },
});
