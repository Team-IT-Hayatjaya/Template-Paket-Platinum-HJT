@php
    $primaryColor = $siteSetting->primary_color ?? '#14304F';
    $secondaryColor = $siteSetting->secondary_color ?? '#C9A227';
    $faviconUrl = !empty($siteSetting->favicon_path) ? asset('storage/' . $siteSetting->favicon_path) : asset('favicon.ico');
@endphp

<link rel="icon" type="image/x-icon" href="{{ $faviconUrl }}">
<style>
    :root {
        --site-primary: {{ $primaryColor }};
        --site-primary-container: {{ $primaryColor }};
        --site-secondary: {{ $secondaryColor }};
        --site-secondary-container: {{ $secondaryColor }};
        --gov-primary: {{ $primaryColor }};
        --gov-secondary: {{ $secondaryColor }};
    }
</style>
