<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; background: #f7f7f7; padding: 30px; }
        .card { background: white; border-radius: 12px; padding: 30px; max-width: 480px; margin: auto; text-align: center; }
        .btn { display: inline-block; background: #4f46e5; color: white; padding: 12px 24px; border-radius: 8px; text-decoration: none; }
    </style>
</head>
<body>
<div class="card">
    <h2>Подтверждение почты</h2>
    <p>Здравствуйте! Пожалуйста, подтвердите свой адрес электронной почты.</p>
    <a href="{{ $url }}" class="btn">Подтвердить</a>
</div>
</body>
</html>
