<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Aurateria Company</title>
    <style>
        /* Body Styling */
        body {
            margin: 0;
            height: 100vh;
            overflow: hidden;
            background: linear-gradient(135deg, #d9afd9, #97d9e1);
            font-family: 'Roboto', sans-serif;            color: #333;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        /* Card Styling */
        .welcome-card {
            position: relative;
            text-align: center;
            background: linear-gradient(135deg, #ffffff, #e8f1ff);
            padding: 50px;
            border-radius: 15px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.2);
            max-width: 600px;
        }

        /* Text Animation Styling */
        h1, h2, p {
            display: inline-block;
            animation: text-run 5s linear infinite;
        }

        @keyframes text-run {
            0% { transform: translateX(0); }
            25% { transform: translateX(10px); }
            50% { transform: translateX(-10px); }
            75% { transform: translateX(10px); }
            100% { transform: translateX(0); }
        }

        h1 {
            font-size: 32px;
            color: #2b3d6b;
            margin-bottom: 20px;
        }

        h2 {
            font-size: 24px;
            color: #3a4f87;
            margin: 5px 0;
        }

        p {
            color: #555;
            font-size: 18px;
            margin: 8px 0;
        }

        .info-section {
            margin-top: 20px;
            background: #f9f9f9;
            padding: 15px;
            border-radius: 12px;
            box-shadow: inset 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        
    </style>
</head>
<body>
    <div class="welcome-card">
        <h1>Welcome to Aurateria Company</h1>
        <h2>Employee: Rahul Yadav</h2>
        <p>Role: Laravel Developer</p>

        <div class="info-section">
            <h2>Company Information</h2>
            <p>Address: Jaipur, Rajasthan</p>
            <p>Total Employees: 5</p>
            <p>Rahul Yadav is actively working as a Laravel Developer.</p>
        </div>
    </div>
</body>
</html>
