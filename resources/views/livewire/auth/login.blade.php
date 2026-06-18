<!-- Focused Login Container -->
<main class="w-full max-w-[480px]">
    <!-- Brand Identity Header -->
    <div class="text-center mb-xl">
        <h1 class="font-headline-lg text-headline-lg text-primary font-extrabold tracking-tight">
            Inventory Pro
        </h1>
        <p class="font-body-md text-body-md text-on-surface-variant mt-xs">
            Enterprise Logistics Management
        </p>
    </div>
    <!-- Login Card -->
    <section class="login-card rounded-xl p-lg md:p-xl">
        <header class="mb-lg">
            <h2 class="font-display text-display text-on-surface">
                Welcome back
            </h2>
            <p class="font-body-md text-body-md text-on-surface-variant mt-xs">
                Please enter your details to sign in
            </p>
        </header>

        <!-- Validation Error -->
        @error('email')
            <div class="mb-md p-sm bg-error-container text-on-error-container rounded-lg font-body-md text-body-md flex items-center gap-xs">
                <span class="material-symbols-outlined text-on-error-container">error</span>
                {{ $message }}
            </div>
        @enderror

        <!-- Sign In Form -->
        <form class="space-y-md" wire:submit="authenticate">
            <!-- Username or Email Field -->
            <div class="flex flex-col gap-xs">
                <label class="font-label-lg text-label-lg text-on-surface" for="email">
                    Username or Email address
                </label>
                <div class="relative">
                    <input wire:model="email" class="w-full h-[48px] px-md py-sm bg-surface-bright border border-outline-variant rounded-lg text-body-md focus:outline-none focus:ring-2 focus:ring-on-tertiary-container focus:ring-offset-2 transition-all" id="email" name="email" placeholder="Enter 'admin' or email..." required type="text"/>
                </div>
            </div>
            <!-- Password Field -->
            <div class="flex flex-col gap-xs" x-data="{ show: false }">
                <label class="font-label-lg text-label-lg text-on-surface" for="password">
                    Password
                </label>
                <div class="relative">
                    <input wire:model="password" :type="show ? 'text' : 'password'" class="w-full h-[48px] px-md py-sm bg-surface-bright border border-outline-variant rounded-lg text-body-md focus:outline-none focus:ring-2 focus:ring-on-tertiary-container focus:ring-offset-2 transition-all" id="password" name="password" placeholder="••••••••" required/>
                    <button aria-label="Toggle password visibility" class="absolute right-md top-1/2 -translate-y-1/2 text-on-surface-variant hover:text-primary transition-colors focus:outline-none" x-on:click="show = !show" type="button">
                        <span class="material-symbols-outlined" x-text="show ? 'visibility_off' : 'visibility'">visibility</span>
                    </button>
                </div>
            </div>
            <!-- Remember & Forgot Row -->
            <div class="flex items-center justify-between pt-xs">
                <label class="flex items-center gap-xs cursor-pointer group">
                    <input wire:model="remember" class="w-5 h-5 rounded border-outline-variant text-secondary focus:ring-secondary transition-all" type="checkbox"/>
                    <span class="font-body-md text-body-md text-on-surface-variant group-hover:text-on-surface transition-colors">
                            Remember me
                        </span>
                </label>
                <a class="font-label-md text-label-md text-on-tertiary-fixed-variant hover:underline underline-offset-4" href="#">
                    Forgot password?
                </a>
            </div>
            <!-- Action Button -->
            <button class="w-full h-[56px] mt-lg bg-secondary hover:bg-on-secondary-fixed-variant text-white font-label-lg text-label-lg rounded-lg shadow-sm active:opacity-90 transition-all flex items-center justify-center gap-sm" type="submit" wire:loading.attr="disabled">
                <span wire:loading.remove>
                    Sign In
                    <span class="material-symbols-outlined" data-icon="login">login</span>
                </span>
                <span wire:loading wire:target="authenticate" class="flex items-center gap-sm">
                    <span class="material-symbols-outlined animate-spin" data-icon="progress_activity">progress_activity</span>
                    Authenticating...
                </span>
            </button>
        </form>
    </section>
    <!-- Optional Footer / Registration Link -->
    <footer class="mt-xl text-center">
        <p class="font-body-md text-body-md text-on-surface-variant">
            Don't have an account?
            <a class="text-secondary font-bold hover:underline underline-offset-4" href="{{ route('customer.register') }}">Register here</a> or contact your administrator.
        </p>
        <div class="mt-lg flex justify-center gap-md opacity-60">
            <a class="font-label-md text-label-md text-on-surface-variant hover:text-on-surface" href="#">Privacy Policy</a>
            <span class="text-outline-variant">•</span>
            <a class="font-label-md text-label-md text-on-surface-variant hover:text-on-surface" href="#">Terms of Service</a>
        </div>
    </footer>
</main>
