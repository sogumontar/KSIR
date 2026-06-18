<main class="w-full max-w-[480px] mx-auto py-xl">
    <div class="text-center mb-xl">
        <h1 class="font-headline-lg text-headline-lg text-primary font-extrabold tracking-tight">Customer Registration</h1>
        <p class="font-body-md text-on-surface-variant mt-xs">Join us to access merchant stores.</p>
    </div>

    <section class="bg-surface border border-outline-variant rounded-xl p-lg md:p-xl shadow-sm">
        <form class="space-y-md" wire:submit="register">
            <div class="flex flex-col gap-xs">
                <label class="font-label-lg text-on-surface">Name</label>
                <input wire:model="name" class="w-full h-[48px] px-md bg-surface-bright border border-outline-variant rounded-lg" type="text">
                @error('name') <span class="text-error text-body-sm">{{ $message }}</span> @enderror
            </div>
            <div class="flex flex-col gap-xs">
                <label class="font-label-lg text-on-surface">Email</label>
                <input wire:model="email" class="w-full h-[48px] px-md bg-surface-bright border border-outline-variant rounded-lg" type="email">
                @error('email') <span class="text-error text-body-sm">{{ $message }}</span> @enderror
            </div>
            <div class="flex flex-col gap-xs">
                <label class="font-label-lg text-on-surface">Phone</label>
                <input wire:model="phone" class="w-full h-[48px] px-md bg-surface-bright border border-outline-variant rounded-lg" type="text">
                @error('phone') <span class="text-error text-body-sm">{{ $message }}</span> @enderror
            </div>
            <div class="flex flex-col gap-xs">
                <label class="font-label-lg text-on-surface">Password</label>
                <input wire:model="password" class="w-full h-[48px] px-md bg-surface-bright border border-outline-variant rounded-lg" type="password">
                @error('password') <span class="text-error text-body-sm">{{ $message }}</span> @enderror
            </div>
            <div class="flex flex-col gap-xs">
                <label class="font-label-lg text-on-surface">Confirm Password</label>
                <input wire:model="password_confirmation" class="w-full h-[48px] px-md bg-surface-bright border border-outline-variant rounded-lg" type="password">
            </div>
            <button class="w-full h-[56px] mt-lg bg-secondary text-white font-label-lg rounded-lg flex items-center justify-center gap-sm" type="submit" wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="register">Register</span>
                <span wire:loading wire:target="register" class="animate-spin material-symbols-outlined">sync</span>
                <span wire:loading wire:target="register">Registering...</span>
            </button>
        </form>
    </section>

    <div class="text-center mt-lg">
        <p class="font-body-md text-on-surface-variant">
            Already have an account? 
            <a href="{{ route('login') }}" class="text-secondary font-bold hover:underline" wire:navigate>Login here</a>
        </p>
    </div>
</main>
