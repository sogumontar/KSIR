<div>
    <h1>Order Management</h1>
    @if (session()->has('message'))
        <div class="alert alert-success">{{ session('message') }}</div>
    @endif
    <table class="table">
        <thead>
            <tr>
                <th>Customer</th>
                <th>Total</th>
                <th>Status</th>
                <th>Proof</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($orders as $order)
                <tr>
                    <td>{{ $order->customer->name }}</td>
                    <td>{{ $order->total_amount }}</td>
                    <td>{{ $order->status }}</td>
                    <td>
                        @if($order->payment?->proof_path)
                            <img src="{{ Storage::disk('public')->url($order->payment->proof_path) }}" alt="Proof" class="w-16">
                        @else
                            <span class="text-gray-400 text-xs italic">No proof</span>
                        @endif
                    </td>
                    <td>
                        @if($order->status === 'Pending')
                            <button wire:click="approve({{ $order->id }})" class="btn">Approve</button>
                            <button wire:click="reject({{ $order->id }})" class="btn">Reject</button>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
