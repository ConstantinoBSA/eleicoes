<!-- email-template.php -->
<html>
<head>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            color: #333333;
        }
        p {
            font-size: 16px;
            line-height: 1.5;
            color: #666666;
        }
        a {
            color: #007BFF;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Verificação de E-mail</h1>
        <p>Olá, {nome_usuario}</p>
        <p>Clique no link abaixo para verificar seu e-mail:</p>
        <p><a href='{verificationLink}'>Verificar E-mail</a></p>
        <p>Obrigado!</p>
    </div>
</body>
</html>
