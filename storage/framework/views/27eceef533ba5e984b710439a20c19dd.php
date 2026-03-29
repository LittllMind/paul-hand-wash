<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $__env->yieldContent('title', 'Fundisc'); ?></title>
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
    <?php echo $__env->yieldPushContent('head'); ?>
</head>
<body class="bg-gray-900 text-gray-100 min-h-screen flex flex-col" x-data="{ mobileMenuOpen: false }">

    <!-- Navigation - Style unifié avec la landing -->
    <nav class="bg-gray-800/90 backdrop-blur-md fixed w-full z-50 border-b border-gray-700">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo avec lien Accueil -->
                <a href="/" class="flex items-center space-x-2">
                    <span class="text-2xl">💿</span>
                    <span class="text-xl sm:text-2xl font-bold bg-gradient-to-r from-purple-400 to-pink-500 bg-clip-text text-transparent tracking-tight">
                        Fundisc
                    </span>
                </a>
                
                <!-- Desktop Menu -->
                <div class="hidden md:flex items-center space-x-8">
                    <a href="/" class="text-gray-300 hover:text-white transition">Accueil</a>
                    <a href="/kiosque" class="text-gray-300 hover:text-white transition">Catalogue</a>
                    <a href="/about" class="text-gray-300 hover:text-white transition">Le Concept</a>
                    <a href="/contact" class="text-gray-300 hover:text-white transition">Contact</a>
                </div>
                
                <!-- Desktop Auth -->
                <div class="hidden md:flex items-center space-x-4">
                    <?php if(auth()->guard()->guest()): ?>
                        <a href="/login" class="text-sm text-gray-300 hover:text-white transition">Connexion</a>
                        <a href="/register" class="bg-purple-600 hover:bg-purple-700 px-4 py-2 rounded-lg text-sm font-medium transition">
                            S'inscrire
                        </a>
                    <?php else: ?>
                        <a href="/cart" class="text-sm text-gray-300 hover:text-white transition">🛒 Panier</a>
                        <a href="/dashboard" class="text-sm text-yellow-400 hover:text-yellow-300 transition">🔧 Dashboard</a>
                        <form method="POST" action="/logout" class="inline">
                            <?php echo csrf_field(); ?>
                            <button type="submit" class="text-sm text-gray-400 hover:text-white transition">Déconnexion</button>
                        </form>
                    <?php endif; ?>
                </div>
                
                <!-- Mobile menu button -->
                <button @click="mobileMenuOpen = !mobileMenuOpen" class="md:hidden text-gray-300 p-2">
                    <svg x-show="!mobileMenuOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                    <svg x-show="mobileMenuOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <!-- Mobile menu -->
            <div x-show="mobileMenuOpen" @click.away="mobileMenuOpen = false" x-cloak x-transition class="md:hidden mt-4 pb-4 space-y-3 border-t border-gray-700">
                <a href="/" @click="mobileMenuOpen = false" class="block text-white font-medium py-2 pt-4">Accueil</a>
                <a href="/kiosque" @click="mobileMenuOpen = false" class="block text-purple-400 font-semibold py-2">Catalogue</a>
                <a href="/about" @click="mobileMenuOpen = false" class="block text-gray-300 hover:text-white py-2">Le Concept</a>
                <a href="/contact" @click="mobileMenuOpen = false" class="block text-gray-300 hover:text-white py-2">Contact</a>
                
                <?php if(auth()->guard()->check()): ?>
                    <div class="border-t border-gray-700 pt-4 mt-4 space-y-3">
                        <a href="/cart" @click="mobileMenuOpen = false" class="block text-gray-300 hover:text-white py-2">🛒 Mon Panier</a>
                        <a href="<?php echo e(route('orders.my')); ?>" @click="mobileMenuOpen = false" class="block text-gray-300 hover:text-white py-2">📦 Mes commandes</a>
                        <a href="/dashboard" @click="mobileMenuOpen = false" class="block text-yellow-400 font-semibold py-2">🔧 Dashboard</a>
                        <form method="POST" action="/logout" class="pt-2">
                            <?php echo csrf_field(); ?>
                            <button type="submit" class="text-red-400 py-2">Déconnexion</button>
                        </form>
                    </div>
                <?php else: ?>
                    <div class="border-t border-gray-700 pt-4 mt-4 flex flex-col gap-3">
                        <a href="/login" @click="mobileMenuOpen = false" class="block text-center text-gray-300 hover:text-white py-2 border border-gray-600 rounded-lg">Connexion</a>
                        <a href="/register" @click="mobileMenuOpen = false" class="block text-center bg-purple-600 hover:bg-purple-700 py-2 rounded-lg font-medium">
                            S'inscrire
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- Spacer pour la navbar fixed -->
    <div class="h-16"></div>

    <!-- Page Content -->
    <main class="container mx-auto px-4 py-8 flex-grow">
        <?php if(session('success')): ?>
            <div class="alert alert-success bg-green-600 text-white px-4 py-3 rounded-lg mb-4">
                <?php echo e(session('success')); ?>

            </div>
        <?php endif; ?>

        <?php if(session('error')): ?>
            <div class="alert alert-error bg-red-600 text-white px-4 py-3 rounded-lg mb-4">
                <?php echo e(session('error')); ?>

            </div>
        <?php endif; ?>

        <?php echo $__env->yieldContent('content'); ?>
    </main>

    <!-- Footer -->
    <footer class="bg-gray-800 border-t border-gray-700 py-8 mt-auto">
        <div class="container mx-auto px-4 text-center text-gray-400">
            <p>© 2026 Fundisc - Artisanat & Passion</p>
        </div>
    </footer>

</body>
</html><?php /**PATH /home/aur-lien/.openclaw/workspace/projets/bougies/resources/views/layouts/app.blade.php ENDPATH**/ ?>