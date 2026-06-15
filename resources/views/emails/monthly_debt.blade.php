<!DOCTYPE html>
<html>
<head>
    <title>Monthly Debt Reminder</title>
</head>
<body>
    <h1>Monthly Debt Reminder</h1>
    <p>Hello,</p>
    <p>This is your monthly reminder from Inventory Pro. You currently have an outstanding total debt of <strong>${{ number_format($totalDebt, 2) }}</strong> across your groups.</p>
    
    <h3>Breakdown:</h3>
    <table border="1" cellpadding="5" cellspacing="0">
        <thead>
            <tr>
                <th>Group Name</th>
                <th>Amount Owed</th>
            </tr>
        </thead>
        <tbody>
            @foreach($groupDebts as $debt)
                <tr>
                    <td>{{ $debt['group_name'] }}</td>
                    <td>${{ number_format($debt['amount'], 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <p>Please log in to your account to review your groups and settle your balances.</p>
    <p>Thank you,<br>Inventory Pro Team</p>
</body>
</html>
