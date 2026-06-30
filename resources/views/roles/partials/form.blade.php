<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label>Libellé</label>
            <input type="text" class="form-control" name="libelle" value="{{ old('libelle', $role->libelle ?? '') }}" required>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label>Statut</label>
            <select class="form-control" name="statut">
                <option value="1" {{ (string) old('statut', $role->statut ?? 1) === '1' ? 'selected' : '' }}>Actif</option>
                <option value="0" {{ (string) old('statut', $role->statut ?? 1) === '0' ? 'selected' : '' }}>Inactif</option>
            </select>
        </div>
    </div>
    <div class="col-12">
        <div class="form-group">
            <label>Description</label>
            <textarea class="form-control" name="description" rows="2">{{ old('description', $role->description ?? '') }}</textarea>
        </div>
    </div>
    <div class="col-12">
        <label>Permissions</label>
        <div class="role-feature-box">
            @foreach($menu_features as $group => $features)
                <div class="role-feature-group">
                    <span class="role-feature-title">{{ $group }}</span>
                    <div class="role-feature-grid">
                        @foreach($features as $feature)
                            <label class="role-feature-check">
                                <input type="checkbox" name="menu_features[]" value="{{ $feature->key }}" @checked(in_array($feature->key, $selectedFeatures ?? [], true))>
                                <span>{{ $feature->libelle }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
