<?php $__env->startSection('content'); ?>
    <div class="relative z-1 bg-white p-6 sm:p-0 dark:bg-gray-900">
        <div class="relative flex h-screen w-full flex-col justify-center sm:p-0 lg:flex-row dark:bg-gray-900">
            <!-- Form -->
            <div class="flex w-full flex-1 flex-col lg:w-1/2">
                <div class="mx-auto flex w-full max-w-md flex-1 flex-col justify-center">
                    <div>
                        <div class="mb-5 sm:mb-8">
                            <h1 class="text-title-sm sm:text-title-md mb-2 font-semibold text-gray-800 dark:text-white/90">
                                Sign In
                            </h1>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                Masukkan email dan password untuk masuk ke Sistem Inventaris
                            </p>
                        </div>

                        
                        <?php if(session('status')): ?>
                            <div class="mb-4 text-sm font-medium text-green-600">
                                <?php echo e(session('status')); ?>

                            </div>
                        <?php endif; ?>

                        <div>
                            <form method="POST" action="<?php echo e(route('login')); ?>">
                                <?php echo csrf_field(); ?>

                                <div class="space-y-5">
                                    <!-- Email -->
                                    <div>
                                        <label for="email" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                            Email<span class="text-error-500">*</span>
                                        </label>
                                        <input type="email" id="email" name="email" value="<?php echo e(old('email')); ?>"
                                            placeholder="info@gmail.com" required autofocus autocomplete="username"
                                            class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
                                        <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                            <p class="mt-1.5 text-sm text-error-500"><?php echo e($message); ?></p>
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    </div>

                                    <!-- Password -->
                                    <div>
                                        <label for="password" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                            Password<span class="text-error-500">*</span>
                                        </label>
                                        <div x-data="{ showPassword: false }" class="relative">
                                            <input :type="showPassword ? 'text' : 'password'"
                                                id="password" name="password" required autocomplete="current-password"
                                                placeholder="Enter your password"
                                                class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 pr-11 pl-4 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
                                            <span @click="showPassword = !showPassword"
                                                class="absolute top-1/2 right-4 z-30 -translate-y-1/2 cursor-pointer text-gray-500 dark:text-gray-400">
                                                <svg x-show="!showPassword" class="fill-current" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M10.0002 13.8619C7.23361 13.8619 4.86803 12.1372 3.92328 9.70241C4.86804 7.26761 7.23361 5.54297 10.0002 5.54297C12.7667 5.54297 15.1323 7.26762 16.0771 9.70243C15.1323 12.1372 12.7667 13.8619 10.0002 13.8619ZM10.0002 4.04297C6.48191 4.04297 3.49489 6.30917 2.4155 9.4593C2.3615 9.61687 2.3615 9.78794 2.41549 9.94552C3.49488 13.0957 6.48191 15.3619 10.0002 15.3619C13.5184 15.3619 16.5055 13.0957 17.5849 9.94555C17.6389 9.78797 17.6389 9.6169 17.5849 9.45932C16.5055 6.30919 13.5184 4.04297 10.0002 4.04297ZM9.99151 7.84413C8.96527 7.84413 8.13333 8.67606 8.13333 9.70231C8.13333 10.7286 8.96527 11.5605 9.99151 11.5605H10.0064C11.0326 11.5605 11.8646 10.7286 11.8646 9.70231C11.8646 8.67606 11.0326 7.84413 10.0064 7.84413H9.99151Z" fill="#98A2B3" />
                                                </svg>
                                                <svg x-show="showPassword" class="fill-current" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                                        d="M4.63803 3.57709C4.34513 3.2842 3.87026 3.2842 3.57737 3.57709C3.28447 3.86999 3.28447 4.34486 3.57737 4.63775L4.85323 5.91362C3.74609 6.84199 2.89363 8.06395 2.4155 9.45936C2.3615 9.61694 2.3615 9.78801 2.41549 9.94558C3.49488 13.0957 6.48191 15.3619 10.0002 15.3619C11.255 15.3619 12.4422 15.0737 13.4994 14.5598L15.3625 16.4229C15.6554 16.7158 16.1302 16.7158 16.4231 16.4229C16.716 16.13 16.716 15.6551 16.4231 15.3622L4.63803 3.57709ZM12.3608 13.4212L10.4475 11.5079C10.3061 11.5423 10.1584 11.5606 10.0002 11.5606H9.99151C8.96527 11.5606 8.13333 10.7286 8.13333 9.70237C8.13333 9.5461 8.15262 9.39434 8.18895 9.24933L5.91885 6.97923C5.03505 7.69015 4.34057 8.62704 3.92328 9.70247C4.86803 12.1373 7.23361 13.8619 10.0002 13.8619C10.8326 13.8619 11.6287 13.7058 12.3608 13.4212ZM16.0771 9.70249C15.7843 10.4569 15.3552 11.1432 14.8199 11.7311L15.8813 12.7925C16.6329 11.9813 17.2187 11.0143 17.5849 9.94561C17.6389 9.78803 17.6389 9.61696 17.5849 9.45938C16.5055 6.30925 13.5184 4.04303 10.0002 4.04303C9.13525 4.04303 8.30244 4.17999 7.52218 4.43338L8.75139 5.66259C9.1556 5.58413 9.57311 5.54303 10.0002 5.54303C12.7667 5.54303 15.1323 7.26768 16.0771 9.70249Z"
                                                        fill="#98A2B3" />
                                                </svg>
                                            </span>
                                        </div>
                                        <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                            <p class="mt-1.5 text-sm text-error-500"><?php echo e($message); ?></p>
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    </div>

                                    <!-- Checkbox -->
                                    <div class="flex items-center justify-between">
                                        <div x-data="{ checkboxToggle: false }">
                                            <label for="remember" class="flex cursor-pointer items-center text-sm font-normal text-gray-700 select-none dark:text-gray-400">
                                                <div class="relative">
                                                    <input type="checkbox" id="remember" name="remember" class="sr-only" x-model="checkboxToggle" />
                                                    <div :class="checkboxToggle ? 'border-brand-500 bg-brand-500' :
                                                        'bg-transparent border-gray-300 dark:border-gray-700'"
                                                        class="mr-3 flex h-5 w-5 items-center justify-center rounded-md border-[1.25px]">
                                                        <span :class="checkboxToggle ? '' : 'opacity-0'">
                                                            <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <path d="M11.6666 3.5L5.24992 9.91667L2.33325 7" stroke="white" stroke-width="1.94437" stroke-linecap="round" stroke-linejoin="round" />
                                                            </svg>
                                                        </span>
                                                    </div>
                                                </div>
                                                Ingat saya
                                            </label>
                                        </div>
                                        <?php if(Route::has('password.request')): ?>
                                            <a href="<?php echo e(route('password.request')); ?>" class="text-brand-500 hover:text-brand-600 dark:text-brand-400 text-sm">
                                                Lupa password?
                                            </a>
                                        <?php endif; ?>
                                    </div>

                                    <!-- Button -->
                                    <div>
                                        <button type="submit"
                                            class="bg-brand-500 shadow-theme-xs hover:bg-brand-600 flex w-full items-center justify-center rounded-lg px-4 py-3 text-sm font-medium text-white transition">
                                            Sign In
                                        </button>
                                    </div>
                                </div>
                            </form>

                            <?php if(Route::has('register')): ?>
                                <div class="mt-5">
                                    <p class="text-center text-sm font-normal text-gray-700 sm:text-start dark:text-gray-400">
                                        Belum punya akun?
                                        <a href="<?php echo e(route('register')); ?>" class="text-brand-500 hover:text-brand-600 dark:text-brand-400">Sign Up</a>
                                    </p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-brand-950 relative hidden h-full w-full items-center lg:grid lg:w-1/2 dark:bg-white/5">
                <div class="z-1 flex items-center justify-center">
                    <?php if (isset($component)) { $__componentOriginalbde5387982a24421da847c9781a86dc0 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalbde5387982a24421da847c9781a86dc0 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.common.common-grid-shape','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('common.common-grid-shape'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalbde5387982a24421da847c9781a86dc0)): ?>
<?php $attributes = $__attributesOriginalbde5387982a24421da847c9781a86dc0; ?>
<?php unset($__attributesOriginalbde5387982a24421da847c9781a86dc0); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalbde5387982a24421da847c9781a86dc0)): ?>
<?php $component = $__componentOriginalbde5387982a24421da847c9781a86dc0; ?>
<?php unset($__componentOriginalbde5387982a24421da847c9781a86dc0); ?>
<?php endif; ?>
                    <div class="flex max-w-xs flex-col items-center">
                        <a href="/" class="mb-4 block">
                            <img src="/images/logo/auth-logo.svg" alt="Logo" />
                        </a>
                        <p class="text-center text-gray-400 dark:text-white/60">
                            Sistem Monitoring & Pelaporan Inventaris
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.fullscreen-layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\inventoris\sistem-inventoris-palawi\resources\views/auth/login.blade.php ENDPATH**/ ?>