<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css" integrity="sha384-mzrmE5qonljUremFsqc01SB46JvROS7bZs3IO2EmfFsd15uHvIt+Y8vEf7N7fWAU" crossorigin="anonymous">
    <meta name="google-site-verification" content="OBHSOz2bJypVuqhse4EbUNPh6u4nlzaqmKaLoOryaqs" />
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }

        .email-container {
            background-color: #f0e8e8;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            margin: auto;
        }

        .banner {
            background-color: #69bb65;
            color: white;
            padding: 10px;
            border-radius: 5px;
            text-align: center;
        }

        .email-content {
            margin-top: 20px;
        }

        .sig {
            font-weight: bold;
        }

        a {
            color: #007bff;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }

        hr {
            border: 0;
            height: 1px;
            background: #eee;
            margin: 20px 0;
        }
    </style>
</head>

<body>
    <main>
        <div class="email-container">
            <div class="banner">
                <h1>Project Created Successfully!</h1>
            </div>
            <div class="email-content">
                <p>Hi there,</p>
                <p>We're thrilled to inform you that your project titled <strong>"<?= $title ?>"</strong> has been created successfully. Thank you for your contribution and for being a valuable part of our community.</p>
                <p>If you would like to view the project details or take further actions, please visit your dashboard.</p>
                <p>Stay tuned for updates and more exciting features coming your way!</p>
                <hr>
                <p style="margin-bottom: 0px;">Sincerely,</p>
                <p style="margin-bottom: 0px; margin-top: 0px;">The Grookr Team</p>
                <p style="margin-bottom: 0px;margin-top: 0px;">Customer Success Manager</p>

            </div>
        </div>
    </main>
</body>

</html>