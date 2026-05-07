<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        @page {
            margin: 24px 28px;
        }

        body {
            margin: 0;
            color: #111827;
        }

        {!! $customCss !!}
    </style>
</head>
<body>
    {!! $html !!}
</body>
</html>
