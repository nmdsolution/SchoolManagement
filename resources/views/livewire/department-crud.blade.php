<div class="container mt-4">
    <h4 class="mb-4">Gestion des Départements</h4>

    <div class="row">
        <div class="col-5">
            {{-- Formulaire --}}
    <form wire:submit.prevent="{{ $isEditMode ? 'update' : 'store' }}" class="mb-4">
        <div class="mb-3">
            <label for="name" class="form-label">Nom du Département</label>
            <input type="text" id="name" wire:model="name" class="form-control" placeholder="Nom">
            @error('name') <div class="text-danger">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
            <label for="responsible" class="form-label">Responsable</label>
            <select id="responsible" wire:model="responsibleId" class="form-select">
                <option value="">Aucun</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}">{{ $user->full_name }}</option>
                @endforeach
            </select>
            @error('responsibleId') <div class="text-danger">{{ $message }}</div> @enderror
        </div>

        <button type="submit" class="btn btn-primary">
            {{ $isEditMode ? 'Modifier' : 'Créer' }}
        </button>
        <button type="button" wire:click="resetForm" class="btn btn-secondary">Annuler</button>
    </form>
        </div>
        <div class="col-7">
            
    {{-- Liste des départements --}}
    <table class="table table-bordered">
        <thead class="table-light">
            <tr>
                <th>Nom</th>
                <th>Responsable</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($departments as $department)
                <tr>
                    <td>{{ $department->name }}</td>
                    <td>{{ $department->responsible ? $department->responsible->full_name : 'Aucun' }}</td>
                    <td>
                        <button wire:click="edit({{ $department->id }})" class="btn btn-warning btn-sm"><i class="fa fa-edit"></i></button>
                        <button wire:click="delete({{ $department->id }})" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i></button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
        </div>
    </div>
</div>
