<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label>Nom</label>
            <input type="text" class="form-control" name="nom" value="{{ old('nom', $adminUser->nom ?? '') }}" required>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label>Prénoms</label>
            <input type="text" class="form-control" name="prenoms" value="{{ old('prenoms', $adminUser->prenoms ?? '') }}" required>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label>Mobile</label>
            <input type="text" class="form-control" name="mobile" value="{{ old('mobile', $adminUser->mobile ?? '') }}" required>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label>Email</label>
            <input type="email" class="form-control" name="email" value="{{ old('email', $adminUser->email ?? '') }}">
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label>Rôle</label>
            <select class="form-control" name="fleet_role_id" required>
                <option value="">Sélectionnez un rôle</option>
                @foreach($roles as $role)
                    <option value="{{ $role->id }}" {{ (int) old('fleet_role_id', $adminUser->fleet_role_id ?? 0) === (int) $role->id ? 'selected' : '' }}>{{ $role->libelle }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label>Statut</label>
            <select class="form-control" name="statut">
                <option value="1" {{ (string) old('statut', $adminUser->statut ?? 1) === '1' ? 'selected' : '' }}>Actif</option>
                <option value="0" {{ (string) old('statut', $adminUser->statut ?? 1) === '0' ? 'selected' : '' }}>Inactif</option>
            </select>
        </div>
    </div>
    <div class="col-12">
        <div class="form-group">
            <label>Mot de passe</label>
            <input type="password" class="form-control" name="password" placeholder="{{ $adminUser ? 'Laisser vide pour ne pas modifier' : 'Laisser vide pour générer automatiquement' }}">
        </div>
    </div>
</div>
