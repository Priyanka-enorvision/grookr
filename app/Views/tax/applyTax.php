<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tax Calculator</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }

        .container {
            max-width: 400px;
            margin: auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 8px;
        }

        input[type="number"] {
            width: calc(100% - 22px);
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        button {
            width: 100%;
            padding: 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        button:hover {
            background-color: #45a049;
        }

        .result {
            margin-top: 20px;
            font-size: 18px;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>Old Tax Regime Calculator</h2>
        <label for="monthlySalary">Monthly Salary:</label>
        <input type="number" id="monthlySalary" placeholder="Enter Monthly Salary" />

        <label for="annualCTC">Annual CTC:</label>
        <input type="number" id="annualCTC" placeholder="Enter Annual CTC" />

        <button onclick="calculateTax()">Calculate Tax</button>

        <div class="result" id="result"></div>
    </div>
    <script src="script.js"></script>
</body>

</html>


<script>
    function calculateTax() {
        const monthlySalary = parseFloat(document.getElementById('monthlySalary').value);
        const annualCTC = parseFloat(document.getElementById('annualCTC').value);

        let annualSalary;

        if (monthlySalary) {
            annualSalary = monthlySalary * 12;
        } else if (annualCTC) {
            annualSalary = annualCTC;
        } else {
            document.getElementById('result').innerText = 'Please enter either Monthly Salary or Annual CTC.';
            return;
        }

        let tax;
        if (annualSalary <= 250000) {
            tax = 0;
        } else if (annualSalary <= 500000) {
            tax = (annualSalary - 250000) * 0.05;
        } else if (annualSalary <= 1000000) {
            tax = 250000 * 0.05 + (annualSalary - 500000) * 0.20;
        } else {
            tax = 250000 * 0.05 + 500000 * 0.20 + (annualSalary - 1000000) * 0.30;
        }

        const cess = tax * 0.04;
        const totalTax = tax + cess;

        document.getElementById('result').innerText = `Income Tax: ₹${totalTax.toFixed(2)}`;
    }
</script>