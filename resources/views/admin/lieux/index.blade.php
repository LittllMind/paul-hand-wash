<x-layouts.admin title="Gestion des lieux">
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 bg-white border-b border-gray-200">
            <h2 class="text-2xl font-bold mb-4">Liste des lieux</h2>

            @if($lieux->isEmpty())
                <p class="text-gray-500">Aucun lieu enregistré.</p>
            @else
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nom</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Adresse</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ville</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Code postal</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($lieux as $lieu)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $lieu->nom }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $lieu->adresse }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $lieu->ville }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $lieu->code_postal }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <a href="#" class="text-indigo-600 hover:text-indigo-900 mr-2">Voir</a>
                                    <a href="#" class="text-blue-600 hover:text-blue-900 mr-2">Modifier</a>
                                    <a href="#" class="text-red-600 hover:text-red-900">Supprimer</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="mt-4">
                    {{ $lieux->links() }}
                </div>
            @endif
        </div>
    </div>
</x-layouts.admin>
