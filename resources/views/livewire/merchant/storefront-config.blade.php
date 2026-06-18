<!-- Page Content -->
<div class="flex-1 p-margin-mobile md:p-margin-desktop max-w-[1440px] w-full mx-auto">
    <div class="mb-lg">
        <p class="font-body-md text-body-md text-on-surface-variant max-w-2xl">Customize how your store appears to customers. Ensure your branding is consistent and your details are up to date.</p>
    </div>

    @if (session()->has('message'))
        <div class="mb-lg p-md bg-secondary-container text-on-secondary-container rounded-lg font-label-md flex items-center gap-sm">
            <span class="material-symbols-outlined">check_circle</span>
            {{ session('message') }}
        </div>
    @endif

    <form wire:submit.prevent="save" class="space-y-gutter">
        <!-- Section: Visual Branding (Hero Style) -->
        <section class="bg-surface-container-lowest rounded-xl border border-outline-variant overflow-hidden card-shadow">
            <div class="p-lg border-b border-outline-variant bg-surface-container-lowest">
                <h3 class="font-headline-md text-headline-md text-on-surface">Visual Branding</h3>
                <p class="font-body-sm text-body-sm text-on-surface-variant mt-xs">Update your store's cover image and profile picture.</p>
            </div>
            <div class="relative">
                <!-- Cover Image Area -->
                <input type="file" wire:model="banner_photo" class="hidden" id="banner_input" accept="image/*">
                <label for="banner_input" class="h-48 md:h-64 w-full bg-surface-container-high relative group cursor-pointer block"
                    style="background-image: url('{{ $banner_photo ? $banner_photo->temporaryUrl() : (auth()->user()->banner_photo ? asset('storage/' . auth()->user()->banner_photo) : 'https://lh3.googleusercontent.com/aida-public/AB6AXuCmeXQj0HYzz-a8WJinV5aEnUhMWwXQqcvaL5vmcSTp3eznp9cBlsQ5equxTtzL2ZdrWfzXFbB-S-9Nd5HBMpuuaY_X8exQRaa2CVGp00f8elKiLnmGsDkebSo_yP29-V1vZqhUioeoMoLgdbtqtKzYf1nW3ncMfC0pEeLxdnv8mgmurWGGbgLvxXvdTTXE4iPBqDoNWSSK874XtFvN0NprhEtk9g3ThZFMfRUqv5Cpk9CL658WNYouLVqFG3S8E2C-cAJaPcTG1WM') }}'); background-size: cover; background-position: center;">
                    <div class="absolute inset-0 bg-primary/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                        <span class="flex items-center gap-xs text-on-primary font-body-md text-body-md font-medium px-md py-sm bg-primary/80 rounded-lg backdrop-blur-sm">
                            <span class="material-symbols-outlined">photo_camera</span> Change Cover Image
                        </span>
                    </div>
                    <div wire:loading wire:target="banner_photo" class="absolute inset-0 bg-surface-container-high/50 flex items-center justify-center">
                        <span class="animate-spin material-symbols-outlined text-primary">sync</span>
                    </div>
                </label>
                <!-- Profile Picture Area -->
                <div class="absolute -bottom-12 left-lg flex items-end">
                    <div class="relative group">
                        <input type="file" wire:model="profile_photo" class="hidden" id="profile_input" accept="image/*">
                        <label for="profile_input" class="relative group cursor-pointer block">
                            <div class="w-24 h-24 md:w-32 md:h-32 rounded-xl bg-surface-container-lowest border-4 border-surface-container-lowest shadow-md overflow-hidden flex items-center justify-center">
                                @if($profile_photo)
                                    <img class="w-full h-full object-cover" src="{{ $profile_photo->temporaryUrl() }}">
                                @elseif(auth()->user()->profile_photo)
                                    <img class="w-full h-full object-cover" src="{{ asset('storage/' . auth()->user()->profile_photo) }}">
                                @else
                                    <span class="text-4xl text-outline">{{ strtoupper(substr($store_name ?: auth()->user()->name, 0, 1)) }}</span>
                                @endif
                            </div>
                            <div class="absolute inset-0 bg-primary/40 rounded-xl flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300 border-4 border-transparent">
                                <span class="material-symbols-outlined text-on-primary">edit</span>
                            </div>
                            <div wire:loading wire:target="profile_photo" class="absolute inset-0 bg-surface-container-lowest/50 rounded-xl flex items-center justify-center">
                                <span class="animate-spin material-symbols-outlined text-primary">sync</span>
                            </div>
                        </label>
                    </div>
                </div>
            </div>
            <div class="p-lg pt-16 md:pt-16 bg-surface-container-lowest flex justify-end gap-sm">
                <button wire:click="removeImages" class="px-md py-sm rounded-lg font-body-sm text-body-sm font-medium text-on-surface-variant hover:bg-surface-container-low transition-colors" type="button">Remove Images</button>
            </div>
        </section>

        @error('profile_photo') <p class="text-error text-body-sm mt-xs">{{ $message }}</p> @enderror
        @error('banner_photo') <p class="text-error text-body-sm mt-xs">{{ $message }}</p> @enderror

        <!-- Grid for Store Info and Business Details -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-gutter">
            <!-- Section: Store Information -->
            <section class="lg:col-span-7 bg-surface-container-lowest rounded-xl border border-outline-variant p-lg card-shadow space-y-md">
                <div>
                    <h3 class="font-headline-md text-headline-md text-on-surface">Store Information</h3>
                    <p class="font-body-sm text-body-sm text-on-surface-variant mt-xs">Primary contact and descriptive details.</p>
                </div>
                <div class="space-y-md pt-sm">
                    <div>
                        <label class="font-label-caps text-label-caps text-on-surface-variant block mb-xs">Store Name <span class="text-error">*</span></label>
                        <input wire:model="store_name" class="form-input w-full rounded-lg px-md py-sm font-body-md text-body-md text-on-surface" placeholder="e.g. Acme Corp" type="text"/>
                        @error('store_name') <p class="text-error text-body-sm mt-xs">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="font-label-caps text-label-caps text-on-surface-variant block mb-xs">Store Description</label>
                        <textarea wire:model="store_description" class="form-input w-full rounded-lg px-md py-sm font-body-md text-body-md text-on-surface resize-none" placeholder="Briefly describe what your store offers..." rows="4"></textarea>
                        <div class="flex justify-between items-center mt-xs">
                            @error('store_description') <p class="text-error text-body-sm">{{ $message }}</p> @enderror
                            <p class="font-body-sm text-body-sm text-on-surface-variant opacity-70 ml-auto">{{ strlen($store_description) }} / 500</p>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-md">
                        <div>
                            <label class="font-label-caps text-label-caps text-on-surface-variant block mb-xs">Public Email <span class="text-error">*</span></label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-sm flex items-center pointer-events-none">
                                    <span class="material-symbols-outlined text-outline text-[20px]">mail</span>
                                </div>
                                <input wire:model="public_email" class="form-input w-full rounded-lg pl-[40px] pr-md py-sm font-body-md text-body-md text-on-surface" type="email"/>
                            </div>
                            @error('public_email') <p class="text-error text-body-sm mt-xs">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="font-label-caps text-label-caps text-on-surface-variant block mb-xs">Support Phone</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-sm flex items-center pointer-events-none">
                                    <span class="material-symbols-outlined text-outline text-[20px]">call</span>
                                </div>
                                <input wire:model="support_phone" class="form-input w-full rounded-lg pl-[40px] pr-md py-sm font-body-md text-body-md text-on-surface data-mono" type="tel"/>
                            </div>
                            @error('support_phone') <p class="text-error text-body-sm mt-xs">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>
            </section>
            <!-- Section: Business Details -->
            <section class="lg:col-span-5 bg-surface-container-lowest rounded-xl border border-outline-variant p-lg card-shadow space-y-md flex flex-col">
                <div>
                    <h3 class="font-headline-md text-headline-md text-on-surface">Business Details</h3>
                    <p class="font-body-sm text-body-sm text-on-surface-variant mt-xs">Categorization and operational parameters.</p>
                </div>
                <div class="space-y-md pt-sm flex-1">
                    <div>
                        <label class="font-label-caps text-label-caps text-on-surface-variant block mb-xs">Primary Category</label>
                        <div class="relative">
                            <select wire:model="category" class="form-input w-full rounded-lg px-md py-sm font-body-md text-body-md text-on-surface appearance-none bg-transparent relative z-10">
                                <option value="software">Enterprise Software</option>
                                <option value="hardware">Hardware Components</option>
                                <option value="services">Consulting Services</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 pr-sm flex items-center pointer-events-none">
                                <span class="material-symbols-outlined text-outline">expand_more</span>
                            </div>
                        </div>
                        @error('category') <p class="text-error text-body-sm mt-xs">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="font-label-caps text-label-caps text-on-surface-variant block mb-xs">Operating Status</label>
                        <div class="flex items-center justify-between p-sm border border-outline-variant rounded-lg bg-surface-container-lowest">
                            <div class="flex items-center gap-sm">
                                <div class="w-3 h-3 rounded-full {{ $operating_status ? 'bg-secondary-fixed' : 'bg-outline' }}"></div>
                                <span class="font-body-md text-body-md text-on-surface font-medium">{{ $operating_status ? 'Accepting Orders' : 'Paused' }}</span>
                            </div>
                            <!-- Simple Toggle UI -->
                            <div wire:click="$set('operating_status', {{ $operating_status ? 'false' : 'true' }})" 
                                 class="w-10 h-6 {{ $operating_status ? 'bg-primary border-primary' : 'bg-outline-variant border-outline-variant' }} rounded-full relative cursor-pointer border-2 transition-colors duration-200">
                                <div class="absolute {{ $operating_status ? 'right-0.5' : 'left-0.5' }} top-0.5 w-4 h-4 bg-on-primary rounded-full transition-all duration-200"></div>
                            </div>
                        </div>
                        @error('operating_status') <p class="text-error text-body-sm mt-xs">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="font-label-caps text-label-caps text-on-surface-variant block mb-xs">Timezone</label>
                        <div class="relative">
                            <select wire:model="timezone" class="form-input w-full rounded-lg px-md py-sm font-body-md text-body-md text-on-surface appearance-none bg-transparent relative z-10">
                                <option value="PST">(GMT-08:00) Pacific Time</option>
                                <option value="EST">(GMT-05:00) Eastern Time</option>
                                <option value="UTC">UTC (Universal Coordinated Time)</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 pr-sm flex items-center pointer-events-none">
                                <span class="material-symbols-outlined text-outline">expand_more</span>
                            </div>
                        </div>
                        @error('timezone') <p class="text-error text-body-sm mt-xs">{{ $message }}</p> @enderror
                    </div>
                </div>
            </section>
        </div>

        <!-- Action Bar -->
        <div class="flex items-center justify-end gap-md pt-lg border-t border-outline-variant mt-xl">
            <button class="px-lg py-sm rounded-lg font-body-md text-body-md font-medium text-on-surface border border-outline-variant bg-surface hover:bg-surface-container-high transition-colors" type="button" onclick="window.history.back()">
                Cancel
            </button>
            <button class="px-lg py-sm rounded-lg font-body-md text-body-md font-medium text-on-primary bg-primary hover:bg-on-primary-fixed transition-colors shadow-sm flex items-center gap-xs" type="submit">
                <span class="material-symbols-outlined text-[20px]" wire:loading.remove wire:target="save">save</span>
                <span class="animate-spin material-symbols-outlined text-[20px]" wire:loading wire:target="save">sync</span>
                Save Changes
            </button>
        </div>
    </form>
</div>
