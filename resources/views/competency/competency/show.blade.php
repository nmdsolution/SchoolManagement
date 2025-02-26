@extends('layout.master')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-2xl mx-auto bg-white shadow-md rounded-lg overflow-hidden">
        <div class="px-6 py-4">
            <div class="flex justify-between items-center mb-4">
                <h1 class="text-2xl font-bold">{{ $competency->name }}</h1>
                <a href="{{ route('competency.index') }}" 
                   class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Retour
                </a>
            </div>

            <div class="mb-4">
                <h2 class="text-lg font-semibold mb-2">Description</h2>
                <p class="text-gray-700">{{ $competency->description ?? 'Aucune description' }}</p>
            </div>

            <div class="mb-4">
                <h2 class="text-lg font-semibold mb-2">Domaine</h2>
                <p class="text-gray-700">{{ $competency->domain->name }}</p>
            </div>

            <div class="mb-4">
                <h2 class="text-lg font-semibold mb-2">Classe</h2>
                <p class="text-gray-700">{{ $competency->primaryClass->name }}</p>
            </div>

            <div class="flex space-x-4 mt-6">
                <a href="{{ route('competency.edit', $competency->id) }}" 
                   class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded">
                    Modifier
                </a>
                <form action="{{ route('competency.destroy', $competency->id) }}" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded"
                            onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette compétence ?')">
                        Supprimer
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection 