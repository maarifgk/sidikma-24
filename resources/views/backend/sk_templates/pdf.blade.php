<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        @page {
            margin: 36px 42px;
        }

        body {
            margin: 0;
            color: #111827;
            background: #ffffff;
        }

        {!! $customCss !!}

        .header-logo-cell {
            width: 148px !important;
        }

        .header-logo-wrap {
            width: 136px !important;
            height: 136px !important;
        }

        .header-logo {
            height: 122px !important;
            width: auto !important;
            max-width: 136px !important;
        }
    </style>
</head>
<body>
    {!! $html !!}
</body>
</html>
