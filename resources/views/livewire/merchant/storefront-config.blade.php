<div class="p-lg md:p-xl">
    <header class="mb-lg">
        <h1 class="font-headline-lg text-headline-lg text-primary font-bold">Storefront Configuration</h1>
        <p class="font-body-md text-on-surface-variant mt-xs">Customize your merchant profile and payment settings.</p>
    </header>

    @if (session()->has('message'))
        <div class="mb-lg p-md bg-secondary-container text-on-secondary-container rounded-lg font-label-md flex items-center gap-sm">
            <span class="material-symbols-outlined">check_circle</span>
            {{ session('message') }}
        </div>
    @endif

    <section class="bg-white p-lg md:p-xl rounded-xl border border-outline-variant shadow-sm max-w-3xl">
        <form class="space-y-md" wire:submit.prevent="save">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-md">
                <div class="flex flex-col gap-xs">
                    <label class="font-label-lg text-on-surface">Profile Photo</label>
                    <input type="file" wire:model="profile_photo" class="w-full h-[48px] px-md py-sm bg-surface-bright border border-outline-variant rounded-lg">
                </div>
                <div class="flex flex-col gap-xs">
                    <label class="font-label-lg text-on-surface">Banner Photo</label>
                    <input type="file" wire:model="banner_photo" class="w-full h-[48px] px-md py-sm bg-surface-bright border border-outline-variant rounded-lg">
                </div>
            </div>

            <div class="flex flex-col gap-xs">
                <label class="font-label-lg text-on-surface">Business Address</label>
                <input type="text" wire:model="business_address" class="w-full h-[48px] px-md bg-surface-bright border border-outline-variant rounded-lg">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-md">
                <div class="flex flex-col gap-xs">
                    <label class="font-label-lg text-on-surface">Category</label>
                    <input type="text" wire:model="category" class="w-full h-[48px] px-md bg-surface-bright border border-outline-variant rounded-lg">
                </div>
                <div class="flex flex-col gap-xs">
                    <label class="font-label-lg text-on-surface">Contact Channel</label>
                    <input type="text" wire:model="contact_channel" class="w-full h-[48px] px-md bg-surface-bright border border-outline-variant rounded-lg">
                </div>
            </div>

            <div class="flex flex-col gap-xs">
                <label class="font-label-lg text-on-surface">Payment Instructions</label>
                <textarea wire:model="payment_instructions" rows="4" class="w-full px-md py-sm bg-surface-bright border border-outline-variant rounded-lg"></textarea>
            </div>

            <button type="submit" class="w-full md:w-auto px-xl py-md bg-primary text-white rounded-lg font-label-lg hover:bg-primary/90 transition-all">
                Save Configuration
            </button>
        </form>
    </section>
</div>
