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

        @if (!empty($offre_concessionnaires) && count($offre_concessionnaires) > 0)
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle" id="articleTable">
                                    <thead class="table-light sticky-top">
                                        <tr>
                                            <th>#</th>
                                            <th>Fichier</th>
                                            <th>Concessionnaire</th>
                                            <th>Contact concessionnaire</th>
                                            <th>Date d'envoi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if ($offre_concessionnaires->isNotEmpty())
                                            @foreach ($offre_concessionnaires as $key => $item)
                                            <tr>
                                                <td>{{ $key + 1 }}</td>
                                                <td><a href="https://concessionnaire.tooauto.com/{{ asset($item->fichier) }}" target="_blank">{{ $item->fichier }}</a></td>
                                                <td>{{ $item->concessionnaire->name }}</td>
                                                <td>{{ $item->concessionnaire->contact }}</td>
                                                <td>{{ $item->created_at->format('d/m/Y') }}</td>
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
            <p>Aucun article enregistrer pour le moment !</p>
        @endif

    <div class="modal fade" id="addPieceAuto" tabindex="-1" role="dialog"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Ajouter un article</h5>
                    <button type="button" class="close" data-bs-dismiss="modal"
                        aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('store.article') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="libelle" class="form-label">Nom de l'article</label>
                                    <div class="input-group">
                                          <input type="text" class="form-control" name="libelle">
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="">Prix de l'article (F CFA)</label>
                                    <input type="number" class="form-control" name="amount">
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="">Photo de l'article</label>
                                    <input type="file" class="form-control" name="image">
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="">Description</label>
                                    <textarea class="form-control" name="description"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer text-center">
                            <button type="button" class="btn btn-cancel" data-bs-dismiss="modal">Fermé</button>
                            <button class="btn btn-submit">Enregistré</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@include('layouts.footer')