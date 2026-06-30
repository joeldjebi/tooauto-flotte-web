@include('layouts.header')
@include('layouts.menu')

<div class="page-wrapper">
    <div class="content">
        @include('layouts.fileariane')
        @if(session()->has("message"))
            <div style="padding: 10px" class="alert {{session()->get('type')}}">{{ session()->get('message') }} </div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Modifier l'alerte</h3>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('alerte.update', $alerte->id) }}" method="POST">
                                @csrf
                                @method('PUT')

                                <div class="form-group mb-3">
                                    <label for="type_alert_id">Type d'alerte @<span class="text-danger">*</span></label>
                                    <select class="form-control @error('type_alert_id') is-invalid @enderror"
                                            id="type_alert_id"
                                            name="type_alert_id"
                                            required>
                                        <option value="">Sélectionner un type d'alerte</option>
                                        @foreach($type_alertes as $type)
                                            <option value="{{ $type->id }}"
                                                {{ old('type_alert_id', $alerte->type_alert_id) == $type->id ? 'selected' : '' }}>
                                                {{ $type->libelle }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('type_alert_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                

                                <div class="form-group mb-3">
                                    <label for="vehicule_id">Véhicule <span class="text-danger">*</span></label>
                                    <select class="form-control @error('vehicule_id') is-invalid @enderror"
                                            id="vehicule_id"
                                            name="vehicule_id"
                                            required>
                                        <option value="">Sélectionner un véhicule</option>
                                        @foreach($vehicules as $vehicule)
                                            <option value="{{ $vehicule->id }}"
                                                {{ old('vehicule_id', $alerte->vehicule_id) == $vehicule->id ? 'selected' : '' }}>
                                                {{ $vehicule->matricule }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('vehicule_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label for="date_debut">Date de début <span class="text-danger">*</span></label>
                                    <input type="date"
                                           class="form-control @error('date_debut') is-invalid @enderror"
                                           id="date_debut"
                                           name="date_debut"
                                           value="{{ old('date_debut', $alerte->date_debut) }}"
                                           required>
                                    @error('date_debut')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label for="date_fin">Date de fin <span class="text-danger">*</span></label>
                                    <input type="date"
                                           class="form-control @error('date_fin') is-invalid @enderror"
                                           id="date_fin"
                                           name="date_fin"
                                           value="{{ old('date_fin', $alerte->date_fin) }}"
                                           required>
                                    @error('date_fin')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group mb-3" id="kilometrage_group" style="display: none;">
                                    <label for="kilometrage">Kilométrage</label>
                                    <input type="number"
                                           class="form-control @error('kilometrage') is-invalid @enderror"
                                           id="kilometrage"
                                           name="kilometrage"
                                           value="{{ old('kilometrage', $alerte->kilometrage) }}">
                                    @error('kilometrage')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                                    <a href="{{ route('alerte.index') }}" class="btn btn-secondary">Annuler</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@include('layouts.footer')

<script>
$(document).ready(function() {
    console.log('Script de gestion du kilométrage chargé');
    
    // Fonction pour gérer l'affichage du champ kilométrage
    function toggleKilometrageField() {
        var typeAlertId = $('#type_alert_id').val();
        var kilometrageGroup = $('#kilometrage_group');
        var kilometrageInput = $('#kilometrage');
        
        console.log('toggleKilometrageField appelée');
        console.log('Type d\'alerte sélectionné:', typeAlertId);
        console.log('Élément kilometrage_group trouvé:', kilometrageGroup.length > 0);
        console.log('Élément kilometrage trouvé:', kilometrageInput.length > 0);
        
        if (typeAlertId == 2) {
            console.log('Type d\'alerte ID 2 détecté - Affichage du champ kilométrage');
            // Afficher le champ kilométrage pour le type d'alerte ID 2 (Vidange)
            kilometrageGroup.show();
            kilometrageInput.prop('required', true);
            console.log('Champ kilométrage affiché et rendu obligatoire');
        } else {
            console.log('Type d\'alerte différent de 2 - Masquage du champ kilométrage');
            // Masquer le champ kilométrage pour les autres types
            kilometrageGroup.hide();
            kilometrageInput.prop('required', false);
            console.log('Champ kilométrage masqué et rendu optionnel');
        }
    }
    
    // Vérifier au chargement de la page si le type d'alerte est déjà ID 2
    console.log('Vérification initiale du type d\'alerte');
    toggleKilometrageField();
    
    // Gérer le changement de type d'alerte
    $('#type_alert_id').on('change', function() {
        console.log('Changement de type d\'alerte détecté');
        toggleKilometrageField();
    });
    
    // Vérification supplémentaire des éléments
    console.log('Vérification des éléments DOM:');
    console.log('- #type_alert_id:', $('#type_alert_id').length);
    console.log('- #kilometrage_group:', $('#kilometrage_group').length);
    console.log('- #kilometrage:', $('#kilometrage').length);
    console.log('- Valeur initiale type_alert_id:', $('#type_alert_id').val());
});
</script>
