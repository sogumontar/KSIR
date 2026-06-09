<!DOCTYPE html>
<html class="light" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Login | Inventory Pro' }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@400;600;700;800&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <style>
        body {
            font-family: 'Public Sans', sans-serif;
            background-color: #f8f9ff;
        }
        .login-card {
            background-color: #ffffff;
            border: 1px solid #E2E8F0;
            box-shadow: 0px 10px 15px -3px rgba(15, 23, 42, 0.05);
        }
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
            vertical-align: middle;
        }
    </style>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script id="tailwind-config">
        tailwind.config = {
          darkMode: "class",
          theme: {
            extend: {
              "colors": {
                      "on-secondary": "#ffffff",
                      "on-tertiary-fixed": "#001a42",
                      "tertiary-fixed-dim": "#adc6ff",
                      "outline-variant": "#c5c6cd",
                      "on-surface-variant": "#45474c",
                      "on-primary-container": "#8590a6",
                      "on-error": "#ffffff",
                      "error": "#ba1a1a",
                      "secondary": "#006c49",
                      "surface-container": "#e5eeff",
                      "on-tertiary-fixed-variant": "#004395",
                      "inverse-primary": "#bcc7de",
                      "on-primary": "#ffffff",
                      "surface-container-highest": "#d3e4fe",
                      "primary-fixed": "#d8e3fb",
                      "on-background": "#0b1c30",
                      "primary-container": "#1e293b",
                      "surface-container-lowest": "#ffffff",
                      "on-tertiary": "#ffffff",
                      "surface-container-high": "#dce9ff",
                      "on-tertiary-container": "#4c8dff",
                      "surface-bright": "#f8f9ff",
                      "on-secondary-fixed-variant": "#005236",
                      "error-container": "#ffdad6",
                      "on-secondary-container": "#00714d",
                      "on-primary-fixed-variant": "#3c475a",
                      "tertiary-container": "#00275b",
                      "secondary-fixed-dim": "#4edea3",
                      "on-primary-fixed": "#111c2d",
                      "inverse-on-surface": "#eaf1ff",
                      "secondary-fixed": "#6ffbbe",
                      "primary-fixed-dim": "#bcc7de",
                      "background": "#f8f9ff",
                      "on-secondary-fixed": "#002113",
                      "tertiary": "#001334",
                      "inverse-surface": "#213145",
                      "on-surface": "#0b1c30",
                      "surface-variant": "#d3e4fe",
                      "surface": "#f8f9ff",
                      "surface-container-low": "#eff4ff",
                      "surface-dim": "#cbdbf5",
                      "outline": "#75777d",
                      "tertiary-fixed": "#d8e2ff",
                      "on-error-container": "#93000a",
                      "secondary-container": "#6cf8bb",
                      "primary": "#091426",
                      "surface-tint": "#545f73"
              },
              "borderRadius": {
                      "DEFAULT": "0.125rem",
                      "lg": "0.25rem",
                      "xl": "0.5rem",
                      "full": "0.75rem"
              },
              "spacing": {
                      "lg": "2rem",
                      "container-max": "1440px",
                      "base": "4px",
                      "xs": "0.5rem",
                      "xl": "3rem",
                      "sm": "1rem",
                      "md": "1.5rem",
                      "gutter": "1.5rem"
              },
              "fontFamily": {
                      "body-md": ["Public Sans"],
                      "body-lg": ["Public Sans"],
                      "label-lg": ["Public Sans"],
                      "display": ["Public Sans"],
                      "headline-lg-mobile": ["Public Sans"],
                      "headline-lg": ["Public Sans"],
                      "label-md": ["Public Sans"],
                      "headline-md": ["Public Sans"]
              },
              "fontSize": {
                      "body-md": ["16px", {"lineHeight": "24px", "fontWeight": "400"}],
                      "body-lg": ["18px", {"lineHeight": "28px", "fontWeight": "400"}],
                      "label-lg": ["16px", {"lineHeight": "20px", "letterSpacing": "0.01em", "fontWeight": "600"}],
                      "display": ["36px", {"lineHeight": "44px", "letterSpacing": "-0.02em", "fontWeight": "700"}],
                      "headline-lg-mobile": ["24px", {"lineHeight": "32px", "fontWeight": "700"}],
                      "headline-lg": ["28px", {"lineHeight": "36px", "fontWeight": "700"}],
                      "label-md": ["14px", {"lineHeight": "16px", "fontWeight": "600"}],
                      "headline-md": ["20px", {"lineHeight": "28px", "fontWeight": "600"}]
              }
            },
          },
        }
    </script>
</head>
<body class="min-h-screen flex flex-col justify-center items-center p-gutter">
    {{ $slot }}

    <!-- Visual Background Element (Subtle) -->
    <div class="fixed inset-0 -z-10 overflow-hidden pointer-events-none">
        <div class="absolute top-[-10%] right-[-5%] w-[400px] h-[400px] rounded-full bg-surface-container opacity-50 blur-3xl"></div>
        <div class="absolute bottom-[-10%] left-[-5%] w-[600px] h-[600px] rounded-full bg-surface-container opacity-30 blur-3xl"></div>
    </div>
</body>
</html>
