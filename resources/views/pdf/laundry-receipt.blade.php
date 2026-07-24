<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Receipt {{ $order->order_code }}</title>
    <style>
        @page { margin: 10px; }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10px;
            color: #000;
            margin: 0;
            padding: 0;
            line-height: 1.2;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .merchant-name { font-size: 14px; font-weight: bold; margin-bottom: 5px; }
        .divider { border-bottom: 1px dashed #000; margin: 5px 0; }
        table { width: 100%; border-collapse: collapse; }
        td { padding: 2px 0; vertical-align: top; }
        .item-name { max-width: 130px; word-wrap: break-word; }
        .qr-code { text-align: center; margin: 10px 0; }
        .qr-code img { max-width: 100px; max-height: 100px; }
        .footer { font-size: 8px; text-align: center; margin-top: 10px; }
    </style>
</head>
<body>
    <div class="text-center">
        <div class="merchant-name">{{ $order->user->name ?? 'Laundry Shop' }}</div>
        <div>Receipt</div>
    </div>
    
    <div class="divider"></div>
    
    <table>
        <tr>
            <td>Date:</td>
            <td class="text-right">{{ $order->created_at->format('d/m/Y H:i') }}</td>
        </tr>
        <tr>
            <td>Order:</td>
            <td class="text-right">{{ $order->order_code }}</td>
        </tr>
        <tr>
            <td>Customer:</td>
            <td class="text-right">{{ $order->customer_name }}</td>
        </tr>
        @if($order->customer_phone)
        <tr>
            <td>Phone:</td>
            <td class="text-right">{{ $order->customer_phone }}</td>
        </tr>
        @endif
        <tr>
            <td>Delivery:</td>
            <td class="text-right">{{ ucfirst($order->delivery_type) }}</td>
        </tr>
    </table>
    
    <div class="divider"></div>
    
    <table>
        @foreach($order->items as $item)
            <tr>
                <td class="item-name">
                    {{ $item->service_name_snapshot }}<br>
                    <span style="font-size: 8px;">(Est: {{ \Carbon\Carbon::parse($item->date_estimated_done)->format('d/m') }})</span>
                </td>
                <td class="text-right">
                    @if($item->is_free)
                        FREE
                    @elseif($item->final_price < $item->price_snapshot)
                        <span style="text-decoration: line-through; color: #888; font-size: 8px;">Rp{{ number_format($item->price_snapshot, 0) }}</span><br>
                        <span class="font-bold">Rp{{ number_format($item->final_price, 0) }}</span>
                    @else
                        Rp{{ number_format($item->price_snapshot, 0) }}
                    @endif
                </td>
            </tr>
        @endforeach
    </table>
    
    <div class="divider"></div>
    
    <table>
        <tr>
            <td>Subtotal:</td>
            <td class="text-right">Rp{{ number_format($order->subtotal, 0) }}</td>
        </tr>
        @if($order->discount > 0)
        <tr>
            <td>Discount:</td>
            <td class="text-right">-Rp{{ number_format($order->discount, 0) }}</td>
        </tr>
        @endif
        <tr>
            <td class="font-bold">Total:</td>
            <td class="text-right font-bold">Rp{{ number_format($order->total, 0) }}</td>
        </tr>
    </table>

    <div class="divider"></div>
    
    <div class="text-center font-bold" style="margin: 5px 0;">
        {{ strtoupper($order->payment_status) }}
    </div>

    @if($order->user->merchantSetting && $order->user->merchantSetting->qr_code_path && $order->payment_status === 'unpaid')
        <div class="qr-code">
            <?php 
                $imagePath = storage_path('app/public/' . $order->user->merchantSetting->qr_code_path);
                if (file_exists($imagePath)) {
                    $imageData = base64_encode(file_get_contents($imagePath));
                    $src = 'data:image/jpeg;base64,' . $imageData;
                    echo '<img src="'.$src.'">';
                }
            ?>
        </div>
    @endif
    
    @if($order->user->merchantSetting && $order->user->merchantSetting->payment_notes)
        <div class="text-center" style="margin-top: 5px; font-size: 9px; white-space: pre-wrap;">
            {{ $order->user->merchantSetting->payment_notes }}
        </div>
    @endif

    <div class="footer">
        Thank you for trusting us!<br>
        Powered by Inventory Pro
    </div>
</body>
</html>
