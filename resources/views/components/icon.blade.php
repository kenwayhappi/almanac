@props(['name', 'class' => ''])

@php
    $svgName = strtolower(trim($name));
    // Normalisation des variantes FontAwesome vers noms standards
    $aliases = [
        'pen' => 'edit',
        'phone-alt' => 'phone',
        'rotate-left' => 'undo',
        'map-marker-alt' => 'map-pin',
        'map-marked-alt' => 'map',
        'calendar-alt' => 'calendar',
        'calendar-day' => 'calendar',
        'calendar-plus' => 'calendar-plus',
        'calendar-times' => 'calendar-x',
        'calendar-check' => 'calendar-check',
        'bullhorn' => 'megaphone',
        'ad' => 'megaphone',
        'hiking' => 'footprints',
        'globe-africa' => 'globe',
        'hand-holding-usd' => 'coins',
        'sign-out-alt' => 'log-out',
        'power-off' => 'log-out',
        'sign-in-alt' => 'log-in',
        'external-link-alt' => 'external-link',
        'file-pdf' => 'file-text',
        'search-location' => 'search',
        'layer-group' => 'layers',
        'user-tie' => 'user-check',
        'user-shield' => 'shield',
        'shield-alt' => 'shield',
        'user-circle' => 'user',
        'user-cog' => 'user-cog',
        'exclamation-triangle' => 'alert-triangle',
        'address-book' => 'contact',
        'list-alt' => 'list',
        'facebook-f' => 'facebook',
        'linkedin-in' => 'linkedin',
    ];
    $icon = $aliases[$svgName] ?? $svgName;
@endphp

<svg {{ $attributes->merge(['class' => 'svg-icon ' . $class]) }} 
     style="width: 1em; height: 1em; vertical-align: -0.125em; display: inline-block; fill: currentColor;" 
     viewBox="0 0 24 24" 
     aria-hidden="true" 
     focusable="false">
@switch($icon)
    @case('search')
        <path fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35M17 11a6 6 0 1 1-12 0 6 6 0 0 1 12 0Z"/>
        @break

    @case('edit')
        <path fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M17 3a2.83 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3Z"/>
        @break

    @case('trash')
        <path fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M3 6h18M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M10 11v6M14 11v6"/>
        @break

    @case('plus')
        <path fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M12 5v14M5 12h14"/>
        @break

    @case('plus-circle')
        <circle cx="12" cy="12" r="10" fill="none" stroke="currentColor" stroke-width="2"/>
        <path fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M12 8v8M8 12h8"/>
        @break

    @case('eye')
        <path fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M2 12s3.64-7 10-7 10 7 10 7-3.64 7-10 7-10-7-10-7Z"/>
        <circle cx="12" cy="12" r="3" fill="none" stroke="currentColor" stroke-width="2"/>
        @break

    @case('layers')
        <path fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="m12 2 10 5-10 5L2 7l10-5ZM2 17l10 5 10-5M2 12l10 5 10-5"/>
        @break

    @case('shield')
        <path fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10Z"/>
        @break

    @case('tree')
        <path fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="m12 2-6 7h3l-4 7h6v6h2v-6h6l-4-7h3l-6-7Z"/>
        @break

    @case('info-circle')
        <circle cx="12" cy="12" r="10" fill="none" stroke="currentColor" stroke-width="2"/>
        <path fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M12 16v-4M12 8h.01"/>
        @break

    @case('undo')
        <path fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/>
        <path fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M3 3v5h5"/>
        @break

    @case('megaphone')
        <path fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="m3 11 18-5v12L3 14v-3ZM11.6 16.8a3 3 0 1 1-5.8-1.6"/>
        @break

    @case('briefcase')
        <rect width="20" height="14" x="2" y="7" rx="2" fill="none" stroke="currentColor" stroke-width="2"/>
        <path fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>
        @break

    @case('footprints')
        <path fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="m13 4 3 3-5 5-3-3 5-5ZM6 14l3 3-5 5-3-3 5-5Z"/>
        @break

    @case('envelope')
        <rect width="20" height="16" x="2" y="4" rx="2" fill="none" stroke="currentColor" stroke-width="2"/>
        <path fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/>
        @break

    @case('user')
        <path fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/>
        <circle cx="12" cy="7" r="4" fill="none" stroke="currentColor" stroke-width="2"/>
        @break

    @case('users')
        <path fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
        <circle cx="9" cy="7" r="4" fill="none" stroke="currentColor" stroke-width="2"/>
        <path fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>
        @break

    @case('user-check')
        <path fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
        <circle cx="9" cy="7" r="4" fill="none" stroke="currentColor" stroke-width="2"/>
        <polyline points="16 11 18 13 22 9" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        @break

    @case('user-cog')
        <path fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
        <circle cx="9" cy="7" r="4" fill="none" stroke="currentColor" stroke-width="2"/>
        <circle cx="19" cy="11" r="2" fill="none" stroke="currentColor" stroke-width="2"/>
        @break

    @case('map-pin')
        <path fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/>
        <circle cx="12" cy="10" r="3" fill="none" stroke="currentColor" stroke-width="2"/>
        @break

    @case('map')
        <polygon points="3 6 9 3 15 6 21 3 21 18 15 21 9 18 3 21" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        <line x1="9" x2="9" y1="3" y2="18" stroke="currentColor" stroke-width="2"/>
        <line x1="15" x2="15" y1="6" y2="21" stroke="currentColor" stroke-width="2"/>
        @break

    @case('home')
        <path fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
        <polyline points="9 22 9 12 15 12 15 22" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        @break

    @case('phone')
        <path fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
        @break

    @case('globe')
        <circle cx="12" cy="12" r="10" fill="none" stroke="currentColor" stroke-width="2"/>
        <line x1="2" x2="22" y1="12" y2="12" stroke="currentColor" stroke-width="2"/>
        <path fill="none" stroke="currentColor" stroke-width="2" d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/>
        @break

    @case('calendar')
        <rect width="18" height="18" x="3" y="4" rx="2" fill="none" stroke="currentColor" stroke-width="2"/>
        <line x1="16" x2="16" y1="2" y2="6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
        <line x1="8" x2="8" y1="2" y2="6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
        <line x1="3" x2="21" y1="10" y2="10" stroke="currentColor" stroke-width="2"/>
        @break

    @case('calendar-plus')
        <rect width="18" height="18" x="3" y="4" rx="2" fill="none" stroke="currentColor" stroke-width="2"/>
        <line x1="16" x2="16" y1="2" y2="6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
        <line x1="8" x2="8" y1="2" y2="6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
        <line x1="3" x2="21" y1="10" y2="10" stroke="currentColor" stroke-width="2"/>
        <line x1="12" x2="12" y1="14" y2="18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
        <line x1="10" x2="14" y1="16" y2="16" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
        @break

    @case('calendar-check')
        <rect width="18" height="18" x="3" y="4" rx="2" fill="none" stroke="currentColor" stroke-width="2"/>
        <line x1="16" x2="16" y1="2" y2="6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
        <line x1="8" x2="8" y1="2" y2="6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
        <line x1="3" x2="21" y1="10" y2="10" stroke="currentColor" stroke-width="2"/>
        <polyline points="9 16 11 18 15 14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        @break

    @case('calendar-x')
        <rect width="18" height="18" x="3" y="4" rx="2" fill="none" stroke="currentColor" stroke-width="2"/>
        <line x1="16" x2="16" y1="2" y2="6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
        <line x1="8" x2="8" y1="2" y2="6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
        <line x1="3" x2="21" y1="10" y2="10" stroke="currentColor" stroke-width="2"/>
        <line x1="10" x2="14" y1="14" y2="18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
        <line x1="14" x2="10" y1="14" y2="18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
        @break

    @case('save')
        <path fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
        <polyline points="17 21 17 13 7 13 7 21" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        <polyline points="7 3 7 8 15 8" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        @break

    @case('crown')
        <path fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="m2 4 3 12h14l3-12-6 7-4-7-4 7-6-7zm3 16h14"/>
        @break

    @case('sitemap')
        <rect width="6" height="6" x="9" y="3" rx="1" fill="none" stroke="currentColor" stroke-width="2"/>
        <rect width="6" height="6" x="2" y="15" rx="1" fill="none" stroke="currentColor" stroke-width="2"/>
        <rect width="6" height="6" x="16" y="15" rx="1" fill="none" stroke="currentColor" stroke-width="2"/>
        <path fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M5 15v-3a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v3M12 9v3"/>
        @break

    @case('coins')
        <circle cx="8" cy="8" r="6" fill="none" stroke="currentColor" stroke-width="2"/>
        <path fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M18.09 10.37A6 6 0 1 1 10.34 18M7 6h1v4M17 14h.01"/>
        @break

    @case('city')
    @case('building')
        <rect width="16" height="20" x="4" y="2" rx="2" fill="none" stroke="currentColor" stroke-width="2"/>
        <path fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M9 22v-4h6v4M8 6h.01M16 6h.01M8 10h.01M16 10h.01M8 14h.01M16 14h.01"/>
        @break

    @case('book-open')
        <path fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2zM22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/>
        @break

    @case('play-circle')
        <circle cx="12" cy="12" r="10" fill="none" stroke="currentColor" stroke-width="2"/>
        <polygon points="10 8 16 12 10 16 10 8" fill="currentColor"/>
        @break

    @case('video-slash')
        <path fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="m16 16 6 4V4l-6 4M2 2l20 20M7 7H4a2 2 0 0 0-2 2v8a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-1"/>
        @break

    @case('compass')
        <circle cx="12" cy="12" r="10" fill="none" stroke="currentColor" stroke-width="2"/>
        <polygon points="16.24 7.76 14.12 14.12 7.76 16.24 9.88 9.88 16.24 7.76" fill="none" stroke="currentColor" stroke-width="2"/>
        @break

    @case('bars')
        <line x1="4" x2="20" y1="12" y2="12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
        <line x1="4" x2="20" y1="6" y2="6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
        <line x1="4" x2="20" y1="18" y2="18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
        @break

    @case('lock')
        <rect width="18" height="11" x="3" y="11" rx="2" fill="none" stroke="currentColor" stroke-width="2"/>
        <path fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M7 11V7a5 5 0 0 1 10 0v4"/>
        @break

    @case('check')
        <polyline points="20 6 9 17 4 12" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        @break

    @case('check-circle')
        <path fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
        <polyline points="22 4 12 14.01 9 11.01" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        @break

    @case('star')
        <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2" fill="currentColor"/>
        @break

    @case('landmark')
        <line x1="3" x2="21" y1="22" y2="22" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
        <line x1="6" x2="6" y1="18" y2="11" stroke="currentColor" stroke-width="2"/>
        <line x1="10" x2="10" y1="18" y2="11" stroke="currentColor" stroke-width="2"/>
        <line x1="14" x2="14" y1="18" y2="11" stroke="currentColor" stroke-width="2"/>
        <line x1="18" x2="18" y1="18" y2="11" stroke="currentColor" stroke-width="2"/>
        <polygon points="12 2 20 7 4 7" fill="none" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
        @break

    @case('paper-plane')
        <path fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="m22 2-7 20-4-9-9-4Z"/>
        <path fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M22 2 11 13"/>
        @break

    @case('file-text')
        <path fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
        <polyline points="14 2 14 8 20 8" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        <line x1="16" x2="8" y1="13" y2="13" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
        <line x1="16" x2="8" y1="17" y2="17" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
        @break

    @case('download')
        <path fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
        <polyline points="7 10 12 15 17 10" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        <line x1="12" x2="12" y1="15" y2="3" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
        @break

    @case('chart-pie')
        <path fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M21.21 15.89A10 10 0 1 1 8 2.83M22 12A10 10 0 0 0 12 2v10z"/>
        @break

    @case('chart-bar')
        <line x1="12" x2="12" y1="20" y2="10" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
        <line x1="18" x2="18" y1="20" y2="4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
        <line x1="6" x2="6" y1="20" y2="16" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
        @break

    @case('filter')
        <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        @break

    @case('sun')
        <circle cx="12" cy="12" r="4" fill="none" stroke="currentColor" stroke-width="2"/>
        <path fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M6.34 17.66l-1.41 1.41M19.07 4.93l-1.41 1.41"/>
        @break

    @case('moon')
        <path fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z"/>
        @break

    @case('clock')
        <circle cx="12" cy="12" r="10" fill="none" stroke="currentColor" stroke-width="2"/>
        <polyline points="12 6 12 12 16 14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        @break

    @case('arrow-right')
        <path fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M12 5l7 7-7 7"/>
        @break

    @case('arrow-left')
        <path fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M19 12H5M12 19l-7-7 7-7"/>
        @break

    @case('arrow-up')
        <path fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M12 19V5M5 12l7-7 7 7"/>
        @break

    @case('external-link')
        <path fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6M15 3h6v6M10 14 21 3"/>
        @break

    @case('alert-triangle')
        <path fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3ZM12 9v4M12 17h.01"/>
        @break

    @case('log-out')
        <path fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4M16 17l5-5-5-5M21 12H9"/>
        @break

    @case('log-in')
        <path fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4M10 17l5-5-5-5M15 12H3"/>
        @break

    @case('list')
        <line x1="8" x2="21" y1="6" y2="6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
        <line x1="8" x2="21" y1="12" y2="12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
        <line x1="8" x2="21" y1="18" y2="18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
        <line x1="3" x2="3.01" y1="6" y2="6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
        <line x1="3" x2="3.01" y1="12" y2="12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
        <line x1="3" x2="3.01" y1="18" y2="18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
        @break

    @case('image')
    @case('images')
        <rect width="18" height="18" x="3" y="3" rx="2" fill="none" stroke="currentColor" stroke-width="2"/>
        <circle cx="8.5" cy="8.5" r="1.5" fill="currentColor"/>
        <path fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="m21 15-5-5L5 21"/>
        @break

    @case('cog')
    @case('tools')
        <circle cx="12" cy="12" r="3" fill="none" stroke="currentColor" stroke-width="2"/>
        <path fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 1 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 1 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 1 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 1 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1Z"/>
        @break

    @case('spinner')
        <path fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" d="M21 12a9 9 0 1 1-6.219-8.56"/>
        @break

    @case('facebook')
        <path fill="currentColor" stroke="none" d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/>
        @break

    @case('twitter')
        <path fill="currentColor" stroke="none" d="M22 4s-.7 2.1-2 3.4c1.6 10-9.4 17.3-18 11.6 2.2.1 4.4-.6 6-2C3 15.5.5 9.6 3 5c2.2 2.6 5.6 4.1 9 4-.9-4.2 4-6.6 7-3.8 1.1 0 3-1.2 3-1.2z"/>
        @break

    @case('instagram')
        <rect width="20" height="20" x="2" y="2" rx="5" fill="none" stroke="currentColor" stroke-width="2"/>
        <path fill="none" stroke="currentColor" stroke-width="2" d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/>
        <line x1="17.5" x2="17.51" y1="6.5" y2="6.5" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
        @break

    @case('linkedin')
        <path fill="currentColor" stroke="none" d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6zM2 9h4v12H2zM4 6a2 2 0 1 1 0-4 2 2 0 0 1 0 4z"/>
        @break

    @case('whatsapp')
        <path fill="currentColor" stroke="none" d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.82 11.82 0 0 0-3.48-8.413z"/>
        @break

    @case('scroll')
        <path fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M19 17V5a2 2 0 0 0-2-2H4M8 21h12a2 2 0 0 0 2-2v-2H6a2 2 0 0 0-2 2v0a2 2 0 0 0 2 2h2zM4 5v14"/>
        @break

    @case('mountain')
        <path fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="m8 3 4 8 5-5 5 15H2L8 3z"/>
        @break

    @case('flag')
        <path fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1zM4 22v-7"/>
        @break

    @case('folder-open')
        <path fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="m6 14 1.45-2.9A2 2 0 0 1 9.24 10H20a2 2 0 0 1 1.94 2.5l-1.55 6a2 2 0 0 1-1.94 1.5H4a2 2 0 0 1-2-2V5c0-1.1.9-2 2-2h3.93a2 2 0 0 1 1.66.9l.82 1.2a2 2 0 0 0 1.66.9H18a2 2 0 0 1 2 2v2"/>
        @break

    @case('font')
        <polyline points="4 7 4 4 20 4 20 7" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        <line x1="9" x2="15" y1="20" y2="20" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
        <line x1="12" x2="12" y1="4" y2="20" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
        @break

    @default
        {{-- Icône générique polyvalente si non trouvée --}}
        <circle cx="12" cy="12" r="9" fill="none" stroke="currentColor" stroke-width="2"/>
        <path fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M12 8v4M12 16h.01"/>
@endswitch
</svg>
