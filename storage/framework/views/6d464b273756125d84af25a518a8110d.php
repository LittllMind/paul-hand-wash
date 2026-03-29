<?php $__env->startSection('title', 'Erreur serveur'); ?>

<?php $__env->startSection('content'); ?>
<div class="min-h-screen flex items-center justify-center">
    <div class="text-center">
        <h1 class="text-6xl font-bold text-gray-700 mb-4">500</h1>
        <p class="text-xl text-gray-600 mb-6">Une erreur est survenue</p>
        <p class="text-gray-500 mb-6">Notre équipe a été notifiée.</p>
        <a href="<?php echo e(route('kiosque.index')); ?>" class="bg-purple-600 text-white px-6 py-3 rounded-lg hover:bg-purple-700">
            Retour au catalogue
        </a>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/aur-lien/.openclaw/workspace/projets/bougies/resources/views/errors/500.blade.php ENDPATH**/ ?>