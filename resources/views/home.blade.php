@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Send a message</div>

                <div class="panel-body">
                    @if (session('message'))
                        <div class="alert alert-success">
                            {{ session('message') }}
                        </div>
                    @endif

                    <form class="form-horizontal" method="POST" action="{{ route('send') }}">
                        {{ csrf_field() }}

                        <div class="form-group{{ $errors->has('contact') ? ' has-error' : '' }}">
                            <label for="contactNo" class="col-md-3 control-label">Contact No.</label>

                            <div class="col-md-8">
                                <select class="form-control" id="contact" multiple="multiple"></select>
                                <input type="hidden" id="contacts" name="contacts" />

                                @if ($errors->has('contacts'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('contacts') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('message') ? ' has-error' : '' }}">
                            <label for="message" class="col-md-3 control-label">Message</label>

                            <div class="col-md-8">
                                <textarea id="message" class="form-control" name="message" required rows="5">{{ old('message') }}</textarea>

                                @if ($errors->has('message'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('message') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('message') ? ' has-error' : '' }}">
                            <label for="message" class="col-md-3 control-label">Media Url</label>

                            <div class="col-md-8">
                                <input class="form-control" name="mediaUrl[]" value="{{ old('mediaUrl.0') }}" style="margin-bottom: 5px;"></input>
                                <input class="form-control" name="mediaUrl[]" value="{{ old('mediaUrl.1') }}"></input>

                                @if ($errors->has('mediaUrl.*'))
                                    <span class="help-block">
                                        <strong>A media url is invalid.</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-8 col-md-offset-3">
                                <button type="submit" class="btn btn-primary">
                                    Send
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    (function(global, $) {

        // setup select2 contacts
        (function setupSelect2Fields() {
            $('#contact').select2({
                allowClear: true,
                data: <?php echo json_encode($contacts); ?>,
                closeOnSelect: false,
                placeholder: {
                    id: "",
                    placeholder: "Select a contact"
                }
            }).on('change', function() {
                var contactsString = $(this).val().join('^');
                $('#contacts').val(contactsString);
            });
        }());

    }(window, jQuery));
</script>
@endsection
