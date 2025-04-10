<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h5 class="card-title mb-0">{{ __('Recent Errors') }}</h5>
                <button class="btn btn-primary btn-sm" id="clear-errors">
                    {{ __('Clear Log') }}
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover" id="error-log-table">
                        <thead>
                            <tr>
                                <th>{{ __('Time') }}</th>
                                <th>{{ __('Type') }}</th>
                                <th>{{ __('Message') }}</th>
                                <th>{{ __('File') }}</th>
                                <th>{{ __('Line') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Will be populated by JS -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>