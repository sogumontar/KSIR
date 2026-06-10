<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Transactions Export</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #1e293b;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #1e3a5f;
            padding-bottom: 15px;
        }
        .header h1 {
            margin: 0;
            font-size: 20px;
            color: #1e3a5f;
        }
        .header p {
            margin: 5px 0 0;
            color: #64748b;
            font-size: 11px;
        }
        .meta {
            margin-bottom: 20px;
            font-size: 10px;
            color: #64748b;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th {
            background-color: #1e3a5f;
            color: #ffffff;
            padding: 8px 6px;
            text-align: left;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        td {
            border-bottom: 1px solid #e2e8f0;
            padding: 7px 6px;
            font-size: 11px;
        }
        tr:nth-child(even) td {
            background-color: #f8fafc;
        }
        .text-right {
            text-align: right;
        }
        .status {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .status-delivered { background: #d1fae5; color: #065f46; }
        .status-pending { background: #fef3c7; color: #92400e; }
        .status-transit { background: #dbeafe; color: #1e40af; }
        .status-loan { background: #ede9fe; color: #5b21b6; }
        .status-failed { background: #fee2e2; color: #991b1b; }
        .footer {
            margin-top: 30px;
            padding-top: 10px;
            border-top: 1px solid #e2e8f0;
            text-align: center;
            font-size: 9px;
            color: #94a3b8;
        }
        .summary {
            margin-top: 15px;
            text-align: right;
            font-size: 12px;
        }
        .summary strong {
            color: #1e3a5f;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Inventory Pro — Transaction Report</h1>
        <p>Generated on {{ now()->format('F d, Y \a\t h:i A') }}</p>
    </div>

    <div class="meta">
        Total Records: {{ $transactions->count() }} &bull;
        Exported by: {{ auth()->user()->name ?? 'System' }}
    </div>

    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Item</th>
                <th>Recipient</th>
                <th class="text-right">Qty</th>
                <th class="text-right">Unit Price</th>
                <th class="text-right">Total Value</th>
                <th>Status</th>
                <th>Due Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transactions as $tx)
                <tr>
                    <td>{{ optional($tx->transaction_date)->format('M d, Y') ?? '-' }}</td>
                    <td>{{ $tx->item_name ?? '-' }}</td>
                    <td>{{ $tx->recipient_name ?? '-' }}</td>
                    <td class="text-right">{{ $tx->quantity ?? 0 }}</td>
                    <td class="text-right">Rp{{ number_format($tx->price ?? 0, 0) }}</td>
                    <td class="text-right">Rp{{ number_format($tx->total_price ?? 0, 0) }}</td>
                    <td><span class="status status-{{ $tx->status ?? 'pending' }}">{{ ucfirst($tx->status ?? 'Unknown') }}</span></td>
                    <td>{{ $tx->due_date ? \Carbon\Carbon::parse($tx->due_date)->format('M d, Y') : '—' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="summary">
        <strong>Grand Total: Rp{{ number_format($transactions->sum('total_price'), 0) }}</strong>
    </div>

    <div class="footer">
        Inventory Pro &copy; {{ now()->year }} — This document was automatically generated.
    </div>
</body>
</html>
