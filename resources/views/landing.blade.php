<x-layouts.app title="Paolo Wash - Lavage Auto à Domicile | Votre voiture étincelante"
          description="Paolo Wash - Service professionnel de lavage auto à domicile. Lavage extérieur, intérieur et rénovation automobile à votre domicile. Réservez en ligne !"
          keywords="lavage auto, domicile, voiture, detailing, Paolo Wash, rénovation automobile, Paris"
          ogType="website">
    <!-- Hero -->
    <div class="bg-gradient-to-br from-blue-600 to-blue-800 text-white py-20 px-4">
        <div class="max-w-4xl mx-auto text-center">
            <h1 class="text-5xl font-bold mb-4">Paolo Wash</h1>
            <p class="text-2xl mb-6">Votre voiture étincelante, sans bouger</p>
            @if(isset($domaines) && $domaines->count() > 0)
                <div class="bg-white/20 rounded-xl p-4 mt-6">
                    <p class="text-lg mb-2">📍 Domaines disponibles :</p>
                    <div class="flex flex-wrap justify-center gap-4">
                        @foreach($domaines as $domaine)
                            <span class="bg-white/30 px-3 py-1 rounded-full text-sm">{{ $domaine->nom }}</span>
                        @endforeach
                    </div>
                </div>
            @else
                <p class="mt-6 opacity-80">📅 Services disponibles sur réservation</p>
            @endif
        </div>
    </div>

    <!-- Tarifs -->
    <div class="py-16 px-4 max-w-6xl mx-auto">
        <h2 class="text-3xl font-bold text-center mb-12">Nos Prestations</h2>
        <div class="grid md:grid-cols-3 gap-8">
            <div class="bg-white rounded-xl shadow-lg p-6 text-center hover:shadow-xl transition">
                <div class="text-4xl mb-4">🚗</div>
                <h3 class="text-xl font-bold mb-2">Express</h3>
                <p class="text-gray-600 mb-4">Lavage extérieur rapide</p>
                <p class="text-3xl font-bold text-blue-600">35€</p>
            </div>
            <div class="bg-white rounded-xl shadow-lg p-6 text-center hover:shadow-xl transition border-2 border-blue-500">
                <div class="text-4xl mb-4">✨</div>
                <h3 class="text-xl font-bold mb-2">Essentiel</h3>
                <p class="text-gray-600 mb-4">Intérieur + Extérieur</p>
                <p class="text-3xl font-bold text-blue-600">55€</p>
                <span class="inline-block bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded mt-2">Populaire</span>
            </div>
            <div class="bg-white rounded-xl shadow-lg p-6 text-center hover:shadow-xl transition">
                <div class="text-4xl mb-4">🌟</div>
                <h3 class="text-xl font-bold mb-2">Premium</h3>
                <p class="text-gray-600 mb-4">Complet + Finitions</p>
                <p class="text-3xl font-bold text-blue-600">75€</p>
            </div>
        </div>
    </div>

    <!-- CTA -->
    <div class="bg-blue-50 py-16 px-4">
        <div class="max-w-xl mx-auto text-center">
            <h2 class="text-3xl font-bold mb-6">Réservez maintenant</h2>
            <p class="text-lg mb-8 text-gray-700">Choisissez votre créneau et laissez Paolo s'occuper de votre voiture !</p>
            <a href="{{ route('reserver') }}" class="inline-block bg-blue-600 text-white px-8 py-4 rounded-xl text-lg font-bold hover:bg-blue-700 transition">
                Voir les disponibilités →
            </a>
        </div>
    </div>

    <!-- Contact -->
    <div class="bg-gray-100 py-16 px-4">
        <div class="max-w-xl mx-auto text-center">
            <h2 class="text-3xl font-bold mb-8">Contactez Paolo</h2>
            <p class="text-lg mb-4">📞 Téléphone : <a href="tel:0600000000" class="text-blue-600 hover:underline">06 00 00 00 00</a></p>
            <p class="text-lg">📧 Email : <a href="mailto:contact@paolo-wash.fr" class="text-blue-600 hover:underline">contact@paolo-wash.fr</a></p>
            <p class="mt-8 text-gray-600">Réservation par téléphone ou en ligne</p>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-8 text-center">
        <p>Paolo Wash © {{ date('Y') }} — Lavage auto à domicile</p>
        <p class="text-gray-400 text-sm mt-2">
            <a href="{{ route('sitemap') }}" class="hover:text-white">Plan du site</a>
        </p>
    </footer>
</x-layouts.app>
