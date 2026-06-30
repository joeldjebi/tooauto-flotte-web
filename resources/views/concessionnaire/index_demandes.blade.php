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
        <div class="row mb-2">
            <div class="col-md-9">
             <h5 class="card-title">Les demandes</h5>
            </div>
        </div>

        @if (!empty($demandes) && count($demandes) > 0)
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table datanew">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Type de demande</th>
                                            <th>Type de véhicule</th>
                                            <th>Marque</th>
                                            <th>Modèle</th>
                                            <th>Statut</th>
                                            <th>Date de demande</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if ($demandes->isNotEmpty())
                                            @foreach ($demandes as $key => $item)
                                            <div class="modal fade" id="view{{ $item->id }}" tabindex="-1" role="dialog"
                                                aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Modification de la demande</h5>
                                                            <button type="button" class="close" data-bs-dismiss="modal"
                                                                aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <form action="{{ route('update.concessionnaire-demande', ['id' => $item->id]) }}" method="post">
                                                                @csrf
                                                                @method('PUT')
                                                                <div class="mb-3">
                                                                    <label for="type_de_demande_id" class="form-label">Type de demande</label>
                                                                    <select class="form-control" name="type_de_demande_id" id="type_de_demande_id">
                                                                        @foreach ($type_de_demandes as $type_de_demande)
                                                                            <option value="{{ $type_de_demande->id }}" {{ $item->type_de_demande_id == $type_de_demande->id ? "selected" : "" }}>{{ $type_de_demande->libelle }}</option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                    <div class="mb-3">
                                                                        <label for="type_de_vehicule_id" class="form-label">Type de véhicule</label>
                                                                        <select class="form-control" name="type_de_vehicule_id" id="type_de_vehicule_id">
                                                                            @foreach ($type_de_vehicules as $type_de_vehicule)
                                                                                <option value="{{ $type_de_vehicule->id }}" {{ $item->type_de_vehicule_id == $type_de_vehicule->id ? "selected" : "" }}>{{ $type_de_vehicule->libelle }}</option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                    <div class="mb-3">
                                                                        <label for="marque_id" class="form-label">Marque</label>
                                                                        <select class="form-control" name="marque_id" id="marque_id">
                                                                            @foreach ($marques as $marque)
                                                                                <option value="{{ $marque->id }}" {{ $item->marque_id == $marque->id ? "selected" : "" }}>{{ $marque->libelle }}</option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                    <div class="mb-3">
                                                                        <label for="modele" class="form-label">Modèle</label>
                                                                        <input type="text" class="form-control" name="modele" id="modele" value="{{ $item->modele }}">
                                                                    </div>
                                                                    <div class="modal-footer text-center">
                                                                        <button type="button" class="btn btn-cancel" data-bs-dismiss="modal">Fermé</button>
                                                                        <button type="submit" class="btn btn-warning">Enregistrer</button>
                                                                    </div>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <tr>
                                                <td>{{ $key + 1 }}</td>
                                                <td>{{ $item->type_de_demande->libelle ?? "" }}</td>
                                                <td>{{ $item->type_de_vehicule->libelle ?? "" }}</td>
                                                <td>{{ $item->marque->libelle ?? "" }}</td>
                                                <td>{{ $item->modele ?? "" }}</td>
                                                <td>{{ $item->statut == 1 ? "En attente" : ($item->statut == 2 ? "Accepté" : "Refusé") }}</td>
                                                <td>{{ $item->created_at ?? "" }}</td>
                                                <td>
                                                    <div class="d-flex justify-content-between btn-actions">
                                                        <!-- Boutons Edit et Delete alignés à gauche -->
                                                            <a class="btn btn-info text-white btn-c me-2" data-bs-toggle="modal" data-bs-target="#view{{ $item->id }}">
                                                                Modifier
                                                            </a>
                                                            <a class="btn btn-danger text-white btn-c me-2" href="{{ route('destroy.concessionnaire-demande', ['id' => $item->id]) }}">
                                                                Supprimer
                                                            </a>

                                                    </div>
                                                </td>
                                            </tr>
                                            @endforeach
                                        @endif
                                    </tbody>

                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @else
            <p>Aucun concessionnaire enregistrer pour le moment !</p>
        @endif

</div>
@include('layouts.footer')
