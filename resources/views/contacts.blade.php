@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Contacts</div>

                <div class="panel-body">

                    @if (isset($inserts) && $inserts > 0)
                        <div class="alert alert-success">
                            Import successful. There are <b>{{ $inserts }}</b> rows inserted to Contacts.
                        </div>
                    @endif
                    @if (isset($duplicates))
                        <div class="alert alert-danger">
                            <p><b>There are {{ sizeof($duplicates) }} duplicates found.</b></p>
                            @foreach ($duplicates as $d)
                                @if (isset($d['duplicate_of']))
                                    <p><b>Row {{ $d['row'] }}</b> was not inserted because it is a duplicate of <b>Row {{ $d['duplicate_of'] }}</b></p>
                                @else
                                    <p><b>Row {{ $d['row'] }}</b> was not inserted because the phone number {{ $d['phone_no'] }} already exists in the database.</b></p>
                                @endif
                            @endforeach
                        </div>
                    @endif

                    <div style="margin-bottom: 20px">
                        <button class="btn btn-primary"  v-on:click="showModalForm">New Contact</button>
                        <form id="importForm" action="{{ route('contacts.import') }}" method="post" enctype="multipart/form-data" style="display: inline;">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                            <label class="btn btn-default btn-file">
                                Bulk Import <input type="file" name="file" style="display: none;">
                            </label>
                        </form>
                    </div>

                    @if (session('message'))
                        <div class="alert alert-success">
                            {{ session('message') }}
                        </div>
                    @endif

                    <div id="notice"></div>

                    <table class="table table-bordered table-hover nowrap" width="100%" cellspacing="0">
                        <thead>
                        <tr>
                            <th width="40%">Name</th>
                            <th width="40%">Phone Number</th>
                            <th width="20%">Date Added</th>
                        </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Form -->
<div class="modal fade" id="modal-form" tabindex="-1" role="dialog" aria-labelledby="modal-form-label">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"></h4>
            </div>
            <div class="modal-body">
                <div id="modal-notice"></div>
                <div class="form-horizontal">
                    <div class="form-group">
                        <label for="name" class="col-md-3 control-label">Name</label>
                        <div class="col-md-8">
                            <input type="text" class="form-control" id="name" v-model="contact.name" required />
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="phoneNo" class="col-md-3 control-label">Phone No.</label>
                        <div class="col-md-8">
                            <input type="text" class="form-control" id="phone_no" v-model="contact.phone_no" required />
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm pull-left" v-show="contact.id" v-on:click="deleteContact">Delete</button>
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary btn-sm" v-on:click="saveContact">Save</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    var actionUrl = "{{ route('contacts.ajax') }}";
    var dt;

    const app = new Vue({
        el: '#app',
        data: {
            contact : {
                id : '',
                phone_no : '',
                name : ''
            }
        },
        methods: {
            showModalForm: function(data) {
                var title = data.phone_no ? "Edit Contact" : "New Contact";
                this.contact.name = data.name || '';
                this.contact.phone_no = data.phone_no || '';
                this.contact.id = data.id || '';
                $('.modal-title').html(title);
                $('.modal').modal({
                    show: true,
                    backdrop: 'static',
                    keyboard: false
                });
            },
            saveContact: function() {
                var data = {
                    _token: "{{ csrf_token() }}",
                    action: "save",
                    contact: this.contact
                };
                $.post(actionUrl, data, function(res) {
                    if (res.success) {
                        $('.modal').modal('hide');
                        $('#notice').addClass('alert alert-success').html('<b>Saving</b> Contact successful.');
                        dt.fnReloadAjax();
                    } else {
                        alert('Something is wrong, contact administrator.');
                    }
                }, 'json').fail(function(res) {
                    $('#modal-notice').addClass('alert alert-danger');
                    var errorMsg = "";
                    for (var error in res.responseJSON) {
                        errorMsg += "<p>" + res.responseJSON[error][0] + "</p>";
                    }
                    $('#modal-notice').html(errorMsg);
                    console.log(errorMsg);
                })
            },
            deleteContact: function() {
                var data = {
                    _token: "{{ csrf_token() }}",
                    action: "delete",
                    id: this.contact.id
                };
                $.post(actionUrl, data, function(res) {
                    if (res.success) {
                        $('.modal').modal('hide');
                        $('#notice').addClass('alert alert-success').html('<b>Deleting</b> Contact successful.');
                        dt.fnReloadAjax();
                    } else {
                        alert('Something is wrong, contact administrator.');
                    }
                }, 'json');
            }
        }
    });

    (function(global, $) {
        $(document).on('change', ':file', function() {
            $(':file').on('fileselect', function(event, numFiles, label) {
                $('#importForm').submit();
            });

            var input = $(this),
                numFiles = input.get(0).files ? input.get(0).files.length : 1,
                label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
            input.trigger('fileselect', [numFiles, label]);
        });

        (function setupDataTables() {
            dt = $('table').dataTable({
                "order": [[ 2, "desc" ]],
                "bDestroy": true,
                "filter": true,
                "ajax": {
                    "type": "POST",
                    "url": actionUrl,
                    "data":  {
                        _token: "{{ csrf_token() }}",
                        action: "list"
                    }
                },
                columns: [
                    { data: "name" },
                    { data: "phone_no" },
                    { data: "created_at" },
                    { data: "id", visible: false }
                ],
                "fnDrawCallback": function (oSettings) {
                    var table = $("table").dataTable();
                    $('table tbody tr').on('click', function (e) {
                        if (undefined !== e) {
                            if ($(e.target).attr('class') && $(e.target).attr('class').includes('dt-align-toggle')) {
                                return;
                            }  
                        }
                        var pos = table.fnGetPosition(this);
                        var d = table.fnGetData(pos);
                        app.showModalForm(d);
                    });
                },
                "language": {
                    "emptyTable": "No contacts found yet."
                }
            });
        }());

    }(window, jQuery));
</script>
@endsection
