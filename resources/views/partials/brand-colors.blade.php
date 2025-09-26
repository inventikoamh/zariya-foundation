@php(
    $primary = \App\Models\SystemSetting::get('primary_color', '#4f46e5')
)
@php(
    $secondary = \App\Models\SystemSetting::get('secondary_color', '#0ea5e9')
)
<style>
    :root {
        --brand-primary: {{ $primary }};
        --brand-secondary: {{ $secondary }};
    }
    /* Primary mappings (indigo → brand primary) */
    .bg-indigo-600 { background-color: var(--brand-primary) !important; }
    .hover\:bg-indigo-700:hover { background-color: var(--brand-primary) !important; }
    .text-indigo-600 { color: var(--brand-primary) !important; }
    .bg-indigo-100 { background-color: color-mix(in srgb, var(--brand-primary) 15%, white) !important; }
    .text-indigo-900 { color: color-mix(in srgb, var(--brand-primary) 85%, black) !important; }

    /* Secondary mappings (blue → brand secondary) */
    .bg-blue-600 { background-color: var(--brand-secondary) !important; }
    .hover\:bg-blue-700:hover { background-color: var(--brand-secondary) !important; }
    .text-blue-600 { color: var(--brand-secondary) !important; }
    .bg-blue-100 { background-color: color-mix(in srgb, var(--brand-secondary) 15%, white) !important; }
</style>


