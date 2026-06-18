<div>
    <h1>Checkout</h1>
    <ul>
        @foreach($cart as $item)
            <li>{{ $item['name'] }} - {{ $item['quantity'] }} x {{ $item['price'] }}</li>
        @endforeach
    </ul>
    
    <form wire:submit.prevent="checkout">
        <input type="file" wire:model="proof" required>
        <button type="submit">Place Order</button>
    </form>
</div>
