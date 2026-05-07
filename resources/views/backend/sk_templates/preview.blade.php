<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }}</title>
    <style>
        body {
            margin: 0;
            padding: 24px;
            background: #e9eef6;
            color: #111827;
            font-family: Arial, sans-serif;
        }

        .preview-sheet {
            width: 794px;
            min-height: 1123px;
            margin: 0 auto;
            padding: 36px 42px;
            background: #fff;
            border-radius: 0;
            box-shadow: 0 12px 38px rgba(15, 23, 42, .15);
            box-sizing: border-box;
        }

        {!! $customCss !!}
    </style>
</head>
<body>
    <div class="preview-sheet">
        {!! $html !!}
    </div>
</body>
</html>
