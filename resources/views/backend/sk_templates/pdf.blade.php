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
    </style>
</head>
<body>
    {!! $html !!}
</body>
</html>
