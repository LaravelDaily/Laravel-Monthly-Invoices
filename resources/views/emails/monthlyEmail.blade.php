<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Monthly invoice</title>
</head>
<body>
    <h3>Hi, {{ $student->first_name }}</h3>
    <h4>
        Total amount for attendance: {{ $invoice->total_amount }} of period: {{ $invoice->period_from }} - {{ $invoice->period_to }}
    </h4>
</body>
</html>
