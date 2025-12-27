@extends('backend.layouts.master')

@section('content')
<div class="content-wrapper">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Edit Email Template</h5>
                </div>

                <form action="{{ route('email-templates.update', $template->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Template Name</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', $template->name) }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Subject</label>
                            <input type="text" name="subject" class="form-control" value="{{ old('subject', $template->subject) }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Body</label>
                            <textarea name="body" id="body" class="form-control" rows="6" required>{{ old('body', $template->body) }}</textarea>
                        </div>
                    </div>

                    <div class="card-footer text-end">
                        <a href="{{ route('email-templates.index') }}" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">Update Template</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- CKEditor 5 Classic Build CDN -->
<script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>

<script>
    ClassicEditor
        .create(document.querySelector('#body'), {
            toolbar: {
                items: [
                    'heading', '|',
                    'bold', 'italic', 'underline', 'strikethrough', 'highlight', '|',
                    'link', 'bulletedList', 'numberedList', 'todoList', '|',
                    'outdent', 'indent', '|',
                    'blockQuote', 'insertTable', 'mediaEmbed', '|',
                    'undo', 'redo', 'code', 'codeBlock', 'removeFormat'
                ]
            },
            table: {
                contentToolbar: ['tableColumn', 'tableRow', 'mergeTableCells']
            },
            mediaEmbed: {
                previewsInData: true
            },
            codeBlock: {
                languages: [
                    { language: 'php', label: 'PHP' },
                    { language: 'javascript', label: 'JavaScript' },
                    { language: 'html', label: 'HTML' }
                ]
            }
        })
        .catch(error => {
            console.error(error);
        });
</script>
@endpush
