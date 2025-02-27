@extends('layout.master')

@section('title')
    {{ __('Honor Roll Certificate Template Editor') }}
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">{{ __('Honor Roll Certificate Template Editor') }}</h4>
                        <p class="card-description">
                            {{ __('Edit the certificate template before generating PDFs') }}
                        </p>

                        <form action="{{ url('students/generate-honor-roll-certificates') }}" method="post" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="exam_report_id" value="{{ $exam_report_ids }}">

                            <div class="row mb-4">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="template_name">{{ __('Name') }} <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="template_name" name="template_name" placeholder="Name" required>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>{{ __('Type') }} <span class="text-danger">*</span></label>
                                        <div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="certificate_type" id="type_student" value="student" checked>
                                                <label class="form-check-label" for="type_student">{{ __('Student') }}</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="certificate_type" id="type_staff" value="staff">
                                                <label class="form-check-label" for="type_staff">{{ __('Staff') }}</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="page_layout">{{ __('Page Layout') }} <span class="text-danger">*</span></label>
                                        <select class="form-control" id="page_layout" name="page_layout">
                                            <option value="a4_landscape">{{ __('A4 Landscape') }}</option>
                                            <option value="a4_portrait">{{ __('A4 Portrait') }}</option>
                                            <option value="letter_landscape">{{ __('Letter Landscape') }}</option>
                                            <option value="letter_portrait">{{ __('Letter Portrait') }}</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-4">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="height">{{ __('Height (MM)') }} <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control" id="height" name="height" value="210" required>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="width">{{ __('Width (MM)') }} <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control" id="width" name="width" value="297" required>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="user_image_shape">{{ __('User Image Shape') }} <span class="text-danger">*</span></label>
                                        <select class="form-control" id="user_image_shape" name="user_image_shape">
                                            <option value="round">{{ __('Round') }}</option>
                                            <option value="square">{{ __('Square') }}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="image_size">{{ __('Image Size (PX)') }} <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="image_size" name="image_size" placeholder="Image Size">
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="background_image">{{ __('Background Image') }}</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" id="thumbnail" name="thumbnail" placeholder="Thumbnail" readonly>
                                            <div class="input-group-append">
                                                <button class="btn btn-primary" type="button" id="upload_button">{{ __('Upload') }}</button>
                                                <input type="file" name="background_image" id="background_image" style="display: none;">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Hidden fields for certificate_title and certificate_heading -->
                            <input type="hidden" id="certificate_title" name="certificate_title" value="{{ __('STUDENT HONOR ROLL CERTIFICATE') }}">
                            <input type="hidden" id="certificate_heading" name="certificate_heading" value="{{ __('This is certify that') }}">

                            <!-- Hidden textarea for form submission -->
                            <textarea class="form-control d-none" id="certificate_text" name="certificate_text">{{ $honor_roll_text }}</textarea>

                            <div class="row mb-4">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="description">{{ __('Description') }} <span class="text-danger">*</span></label>
                                        <div class="editor-container">
                                            <ul class="nav nav-tabs mb-3" id="editorTabs" role="tablist">
                                                <li class="nav-item">
                                                    <a class="nav-link active" id="visual-tab" data-toggle="tab" href="#visual-editor" role="tab">Visual Editor</a>
                                                </li>
                                                <li class="nav-item">
                                                    <a class="nav-link" id="html-tab" data-toggle="tab" href="#html-editor" role="tab">HTML Editor</a>
                                                </li>
                                            </ul>

                                            <div class="tab-content" id="editorTabContent">
                                                <!-- Visual Editor Tab -->
                                                <div class="tab-pane fade show active" id="visual-editor" role="tabpanel" aria-labelledby="visual-tab">
                                                    <div class="btn-toolbar mb-2">
                                                        <button type="button" class="btn btn-sm btn-outline-secondary" id="shortcode-help-btn-visual">
                                                            <i class="mdi mdi-code-tags"></i> Insert Shortcode
                                                        </button>
                                                        <button type="button" class="btn btn-info ml-2" id="preview-certificate-visual">
                                                            <i class="mdi mdi-eye"></i> Preview
                                                        </button>
                                                    </div>
                                                    <!-- TinyMCE Visual Editor -->
                                                    <textarea id="tinymce-visual-editor"></textarea>
                                                </div>

                                                <!-- HTML Editor Tab -->
                                                <div class="tab-pane fade" id="html-editor" role="tabpanel" aria-labelledby="html-tab">
                                                    <div class="btn-toolbar mb-2">
                                                        <button type="button" class="btn btn-sm btn-outline-secondary" id="format-html">
                                                            <i class="mdi mdi-format-align-left"></i> Format HTML
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-outline-secondary ml-2" id="shortcode-help-btn-html">
                                                            <i class="mdi mdi-code-tags"></i> Insert Shortcode
                                                        </button>
                                                        <button type="button" class="btn btn-info ml-2" id="preview-certificate-html">
                                                            <i class="mdi mdi-eye"></i> Preview
                                                        </button>
                                                    </div>
                                                    <!-- CodeMirror HTML Editor -->
                                                    <div id="html-editor-container" style="height: 300px; border: 1px solid #ddd;"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-4">
                                <div class="col-md-12">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="save_as_default" name="save_as_default">
                                        <label class="form-check-label" for="save_as_default">
                                            {{ __('Save as default template') }}
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4">
                                <button type="submit" class="btn btn-primary">{{ __('Generate Certificates') }}</button>
                                <a href="{{ url('honor-roll') }}" class="btn btn-secondary ml-2">{{ __('Cancel') }}</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Shortcode Modal -->
    <div class="modal fade" id="shortcodeModal" tabindex="-1" role="dialog" aria-labelledby="shortcodeModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="shortcodeModalLabel">Insert Shortcode</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="list-group">
                        <button type="button" class="list-group-item list-group-item-action" data-shortcode="{!! '{' . '{' . 'school_logo' . '}' . '}' !!}">School Logo</button>
                        <button type="button" class="list-group-item list-group-item-action" data-shortcode="{!! '{' . '{' . 'school_name' . '}' . '}' !!}">School Name</button>
                        <button type="button" class="list-group-item list-group-item-action" data-shortcode="{!! '{' . '{' . 'student_name' . '}' . '}' !!}">Student Full Name</button>
                        <button type="button" class="list-group-item list-group-item-action" data-shortcode="{!! '{' . '{' . 'session_year' . '}' . '}' !!}">Academic Session</button>
                        <button type="button" class="list-group-item list-group-item-action" data-shortcode="{!! '{' . '{' . 'exam_term' . '}' . '}' !!}">Exam Term</button>
                        <button type="button" class="list-group-item list-group-item-action" data-shortcode="{!! '{' . '{' . 'class_section' . '}' . '}' !!}">Class and Section</button>
                        <button type="button" class="list-group-item list-group-item-action" data-shortcode="{!! '{' . '{' . 'rank' . '}' . '}' !!}">Student Rank</button>
                        <button type="button" class="list-group-item list-group-item-action" data-shortcode="{!! '{' . '{' . 'avg' . '}' . '}' !!}">Student Average</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <!-- Include TinyMCE -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/5.10.5/tinymce.min.js" integrity="sha512-TXT0EzcpG1Qx6HxMFWEi90IQsZSqUuSo/pBl0RHDy7ln/yvlOKL3wEJxE/MYE1FIm/upGhSVY/YUjPTiPZLLWA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <!-- Include CodeMirror for HTML tab -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.62.0/codemirror.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.62.0/mode/htmlmixed/htmlmixed.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.62.0/mode/xml/xml.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.62.0/mode/javascript/javascript.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.62.0/mode/css/css.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.62.0/codemirror.min.css">

    <script>
        $(document).ready(function() {
            // Initialize TinyMCE for visual editor
            tinymce.init({
                selector: '#tinymce-visual-editor',
                height: 600,
                menubar: true,
                plugins: [
                    'advlist autolink lists link image charmap print preview anchor',
                    'searchreplace visualblocks code fullscreen',
                    'insertdatetime media table paste code help wordcount'
                ],
                toolbar: 'undo redo | formatselect | ' +
                    'bold italic underline | alignleft aligncenter ' +
                    'alignright alignjustify | bullist numlist outdent indent | ' +
                    'removeformat | help',
                content_style: 'body { font-family:Arial,sans-serif; font-size:14px }',
                init_instance_callback: function (editor) {
                    // Set initial content from hidden textarea
                    editor.setContent($('#certificate_text').val() || '');
                }
            });

            // Initialize CodeMirror for HTML editing
            const codeMirrorEditor = CodeMirror(document.getElementById('html-editor-container'), {
                mode: 'htmlmixed',
                lineNumbers: true,
                theme: 'default',
                indentWithTabs: false,
                indentUnit: 4,
                lineWrapping: true,
                value: $('#certificate_text').val() || ''
            });

            // Set up tab switching
            $('#editorTabs a').on('click', function(e) {
                e.preventDefault();
                $(this).tab('show');

                if ($(this).attr('href') === '#visual-editor') {
                    // Update TinyMCE from CodeMirror
                    tinymce.get('tinymce-visual-editor').setContent(codeMirrorEditor.getValue());
                } else {
                    // Update CodeMirror from TinyMCE
                    codeMirrorEditor.setValue(tinymce.get('tinymce-visual-editor').getContent());
                }
            });

            // Format HTML button
            $('#format-html').on('click', function() {
                const html = codeMirrorEditor.getValue();
                const formattedHtml = formatHTML(html);
                codeMirrorEditor.setValue(formattedHtml);
            });

            // Upload button handler
            $('#upload_button').on('click', function() {
                $('#background_image').click();
            });

            // Display filename when selected
            $('#background_image').on('change', function() {
                const fileName = $(this).val().split('\\').pop();
                $('#thumbnail').val(fileName || 'Thumbnail');
            });

            // Simple HTML formatter function
            function formatHTML(html) {
                let formatted = '';
                let indent = '';

                const tags = html.replace(/>\s+</g, '><').replace(/</g, '\n<').replace(/>/g, '>\n').split('\n');

                tags.forEach(function(tag) {
                    tag = tag.trim();
                    if (!tag) return;

                    if (tag.match(/^<\//)) {
                        indent = indent.substring(2);
                        formatted += indent + tag + '\n';
                    }
                    else if (tag.match(/\/>$/)) {
                        formatted += indent + tag + '\n';
                    }
                    else if (tag.match(/^</)) {
                        formatted += indent + tag + '\n';
                        if (!(tag.match(/<(br|hr|img|input|link|meta)/) || tag.match(/^<(area|base|col|command|embed|keygen|param|source|track|wbr)/))) {
                            indent += '  ';
                        }
                    }
                    else {
                        formatted += indent + tag + '\n';
                    }
                });

                return formatted;
            }

            // Shortcode insertion for Visual Editor
            $('#shortcode-help-btn-visual').on('click', function() {
                $('#shortcodeModal').modal('show');
                window.currentEditor = 'visual';
            });

            // Shortcode insertion for HTML Editor
            $('#shortcode-help-btn-html').on('click', function() {
                $('#shortcodeModal').modal('show');
                window.currentEditor = 'html';
            });

            $('.list-group-item').on('click', function() {
                const shortcode = $(this).data('shortcode');

                if (window.currentEditor === 'visual') {
                    // Insert at cursor in TinyMCE editor
                    tinymce.get('tinymce-visual-editor').execCommand('mceInsertContent', false, shortcode);
                } else {
                    // Insert at cursor in CodeMirror
                    const cursor = codeMirrorEditor.getCursor();
                    codeMirrorEditor.replaceRange(shortcode, cursor);
                }

                $('#shortcodeModal').modal('hide');
            });

            // Preview buttons
            $('#preview-certificate-visual').on('click', function() {
                previewCertificate(tinymce.get('tinymce-visual-editor').getContent());
            });

            $('#preview-certificate-html').on('click', function() {
                previewCertificate(codeMirrorEditor.getValue());
            });

            // Function to create certificate preview
            function previewCertificate(content) {
                const certificateContent = `
                    <html>
                    <head>
                        <title>Certificate Preview</title>
                        <style>
                            body { font-family: Arial, sans-serif; padding: 20px; }
                            .certificate { width: 100%; max-width: 800px; margin: 0 auto; padding: 20px; border: 2px solid #000; position: relative; }
                            .watermark { position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%) rotate(-45deg); font-size: 60px; opacity: 0.1; z-index: -1; }
                        </style>
                    </head>
                    <body>
                        <div class="certificate">
                            <div class="watermark">PREVIEW</div>
                            ${content}
                        </div>
                    </body>
                    </html>
                `;

                const win = window.open('', 'Certificate Preview', 'width=800,height=600');
                win.document.write(certificateContent);
                win.document.close();
            }

            // Form submission - update hidden textarea
            $('form').on('submit', function() {
                // Get content based on which tab is active
                if ($('#visual-tab').hasClass('active')) {
                    $('#certificate_text').val(tinymce.get('tinymce-visual-editor').getContent());
                } else {
                    $('#certificate_text').val(codeMirrorEditor.getValue());
                }

                // Extract title and heading from content
                try {
                    const tempDiv = $('<div>').html($('#certificate_text').val());
                    const h1 = tempDiv.find('h1').first();
                    if (h1.length) {
                        $('#certificate_title').val(h1.text());
                    }

                    const heading = h1.next('p');
                    if (heading.length) {
                        $('#certificate_heading').val(heading.text());
                    }
                } catch (e) {
                    console.error("Error extracting title/heading:", e);
                }
            });
        });
    </script>
@endsection