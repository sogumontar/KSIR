<main class="w-full max-w-[480px] mx-auto py-xl">
    <div class="text-center mb-xl">
        <h1 class="font-headline-lg text-headline-lg text-primary font-extrabold tracking-tight">Customer Registration</h1>
        <p class="font-body-md text-on-surface-variant mt-xs">Join us to access merchant stores.</p>
    </div>

    <section class="bg-surface border border-outline-variant rounded-xl p-lg md:p-xl shadow-sm">
        <form class="space-y-md" wire:submit="register">
            <div class="flex flex-col gap-xs">
                <label class="font-label-lg text-on-surface">Name</label>
                <input wire:model="name" class="w-full h-[48px] px-md bg-surface-bright border border-outline-variant rounded-lg" type="text" required>
            </div>
            <div class="flex flex-col gap-xs">
                <label class="font-label-lg text-on-surface">Email</label>
                <input wire:model="email" class="w-full h-[48px] px-md bg-surface-bright border border-outline-variant rounded-lg" type="email" required>
            </div>
            <div class="flex flex-col gap-xs">
                <label class="font-label-lg text-on-surface">Phone</label>
                <input wire:model="phone" class="w-full h-[48px] px-md bg-surface-bright border border-outline-variant rounded-lg" type="text" required>
            </div>
            <div class="flex flex-col gap-xs">
                <label class="font-label-lg text-on-surface">Password</label>
                <input wire:model="password" class="w-full h-[48px] px-md bg-surface-bright border border-outline-variant rounded-lg" type="password" required>
            </div>
            <div class="flex flex-col gap-xs">
                <label class="font-label-lg text-on-surface">Confirm Password</label>
                <input wire:model="password_confirmation" class="w-full h-[48px] px-md bg-surface-bright border border-outline-variant rounded-lg" type="password" required>
            </div>
            <button class="w-full h-[56px] mt-lg bg-secondary text-white font-label-lg rounded-lg" type="submit">Register</button>
        </form>
    </section>
</main>
