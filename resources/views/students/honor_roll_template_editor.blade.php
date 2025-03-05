@extends('layout.mastertrismeter')

@section('title')
    {{ __('Honor Roll Certificate Template Editor') }}
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="card-title">{{ __('Honor Roll Certificate Template Editor') }}</h4>
                                <p class="card-description">
                                    {{ __('Edit the certificate template before generating PDFs') }}
                                </p>
                            </div>
                            <!-- Move template selector here -->
                            <div class="col-md-4">
                                <div class="form-group">
                                    <select class="form-control" id="existing_templates">
                                        <option value="">{{ __('Load Template') }}</option>
                                        @foreach($templates as $template)
                                            <option value="{{ $template->id }}">{{ $template->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
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
                                            <option value="A4_portrait">{{ __('A4 Portrait (210×297mm)') }}</option>
                                            <option value="A4_landscape">{{ __('A4 Landscape (297×210mm)') }}</option>
                                            <option value="letter_portrait">{{ __('Letter Portrait (216×279mm)') }}</option>
                                            <option value="letter_landscape">{{ __('Letter Landscape (279×216mm)') }}</option>
                                            <option value="A3_portrait">{{ __('A3 Portrait (297×420mm)') }}</option>
                                            <option value="A3_landscape">{{ __('A3 Landscape (420×297mm)') }}</option>
                                            <option value="custom">{{ __('Custom Size') }}</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-4">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="height">{{ __('Height (MM)') }} <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control" id="height" name="height" value="297" required>
                                        <small class="form-text text-muted">{{ __('Standard A4 portrait height') }}</small>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="width">{{ __('Width (MM)') }} <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control" id="width" name="width" value="210" required>
                                        <small class="form-text text-muted">{{ __('Standard A4 portrait width') }}</small>
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
                                        <input type="number" class="form-control" id="image_size" name="image_size" value="100" placeholder="Image Size">
                                        <small class="form-text text-muted">{{ __('Recommended: 100-150px') }}</small>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="background_image">{{ __('Background Image') }}</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" id="thumbnail" name="thumbnail" placeholder="Thumbnail" readonly>

                                            <input type="hidden" id="background_image_data" name="background_image_data">
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
                                                        <div class="btn-group ml-2">
                                                            <button type="button" class="btn btn-sm btn-outline-primary" id="add-header-btn">
                                                                <i class="mdi mdi-page-layout-header"></i> Add Header
                                                            </button>
                                                            <button type="button" class="btn btn-sm btn-outline-primary" id="add-body-btn">
                                                                <i class="mdi mdi-page-layout-body"></i> Add Body
                                                            </button>
                                                        </div>
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
                                                        <div class="btn-group ml-2">
                                                            <button type="button" class="btn btn-sm btn-outline-primary" id="add-header-btn-html">
                                                                <i class="mdi mdi-page-layout-header"></i> Add Header
                                                            </button>
                                                            <button type="button" class="btn btn-sm btn-outline-primary" id="add-body-btn-html">
                                                                <i class="mdi mdi-page-layout-body"></i> Add Body
                                                            </button>
                                                        </div>
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
                                <button type="submit" name="action" value="generate" class="btn btn-primary generate-btn" formaction="{{ route('generate.honor.roll.certificates') }}">
                                    {{ __('Generate PDF') }}
                                </button>
                                <button type="submit" name="action" value="save" class="btn btn-primary save-btn" formaction="{{ route('save.certificate.template') }}">
                                    {{ __('Save Template') }}
                                </button>
                            </div>
                            <input type="hidden" id="template_data_json" name="template_data_json">
                            <!-- Add this somewhere in your form -->
                            <input type="hidden" id="content_scale" name="content_scale" value="1">
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Add this after your existing form div in the view -->
    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Delete</h5>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete the template "<span id="template-name"></span>"?
                    <input type="hidden" id="delete_template_id">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="confirm-delete-btn">Delete</button>
                </div>
            </div>
        </div>
    </div>
    <div class="content-wrapper">
        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div>
                                <h4 class="card-title">{{ __('Certificate Templates') }}</h4>
                                <p class="card-description">
                                    {{ __('View and manage certificate templates') }}
                                </p>
                            </div>
                            <div>
                                <a class="btn btn-primary btn-create-new-template" >
                                    <i class="mdi mdi-plus"></i> {{ __('Create New Template') }}
                                </a>
                            </div>
                        </div>

                        <!-- Filter options (optional) -->
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="filter_type">{{ __('Filter by Type') }}</label>
                                    <select class="form-control" id="filter_type">
                                        <option value="">{{ __('All Types') }}</option>
                                        <option value="student">{{ __('Student') }}</option>
                                        <option value="staff">{{ __('Staff') }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover" id="templates-table">
                                <thead>
                                <tr>
                                    <th>{{ __('Name') }}</th>
                                    <th>{{ __('Type') }}</th>
                                    <th>{{ __('Page Layout') }}</th>
                                    <th>{{ __('Dimensions (mm)') }}</th>
                                    <th>{{ __('Image Settings') }}</th>

                                    <th>{{ __('Default') }}</th>
                                    <th>{{ __('Actions') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @if(count($templates) > 0)
                                    @foreach($templates as $template)
                                        <tr>
                                            <td>{{ $template->name }}</td>
                                            <td>
                                                    <span class="badge {{ $template->type == 'student' ? 'badge-info' : 'badge-success' }}">
                                                        {{ ucfirst($template->type) }}
                                                    </span>
                                            </td>
                                            <td>{{ str_replace('_', ' ', ucfirst($template->page_layout)) }}</td>
                                            <td>{{ $template->width }} × {{ $template->height }}</td>
                                            <td>
                                                <span>{{ __('Shape:') }} {{ ucfirst($template->user_image_shape) }}</span><br>
                                                <span>{{ __('Size:') }} {{ $template->image_size }}px</span>
                                            </td>

                                            <td>
                                                @if($template->is_default)
                                                    <span class="badge badge-success">{{ __('Default') }}</span>
                                                @else
                                                    <form action="{{ url('students/set-default-template') }}" method="post" class="d-inline">
                                                        @csrf
                                                        <input type="hidden" name="template_id" value="{{ $template->id }}">
                                                        <span class="badge badge-warning">{{ __('Not_Default') }}</span>
                                                    </form>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="dropdown">
                                                    <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" id="dropdownMenuButton{{ $template->id }}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                        {{ __('Actions') }}
                                                    </button>
                                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton{{ $template->id }}">
                                                        <a class="dropdown-item load-template" href="#" data-template-id="{{ $template->id }}">
                                                            <i class="mdi mdi-file-document"></i> {{ __('Load in Editor') }}
                                                        </a>
                                                        <a class="dropdown-item text-danger delete-template" href="#" data-template-id="{{ $template->id }}" data-template-name="{{ $template->name }}">
                                                            <i class="mdi mdi-delete"></i> {{ __('Delete') }}
                                                        </a>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="8" class="text-center">{{ __('No certificate templates found') }}</td>
                                    </tr>
                                @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">{{ __('Delete Template') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>{{ __('Are you sure you want to delete the template:') }} <span id="template-name"></span>?</p>
                    <p class="text-danger">{{ __('This action cannot be undone.') }}</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Cancel') }}</button>
                    <form id="deleteTemplateForm" action="{{ url('students/delete-certificate-template') }}" method="post">
                        @csrf
                        @method('DELETE')
                        <input type="hidden" name="template_id" id="delete_template_id">
                        <button type="submit" class="btn btn-danger">{{ __('Delete') }}</button>
                    </form>
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
                        <button type="button" class="list-group-item list-group-item-action" data-shortcode="{!! '{' . '{' . 'honor_roll_text' . '}' . '}' !!}">Predefined Honor Roll Text</button>
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
            initializeEditorBackground();
            // Store background image data across operations
            let backgroundImageData = '';
            // Initial load of templates

            // Refresh button click handler
            $('#refresh-templates').on('click', function() {
                loadTemplates();
            });
            // Delegate event handler for delete template in dropdown
            $(document).on('click', '.delete-template', function(e) {
                e.preventDefault();

                // Get template details from data attributes
                var templateId = $(this).data('template-id');
                var templateName = $(this).data('template-name');

                // Populate delete modal
                $('#delete_template_id').val(templateId);
                $('#template-name').text(templateName);

                // Show delete confirmation modal
                $('#deleteModal').modal('show');
            });


            // Initialize TinyMCE with enhanced background handling
            tinymce.init({
                selector: '#tinymce-visual-editor',
                height: 700,
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
                setup: function(editor) {
                    // Apply persistent background when editor initializes
                    editor.on('init', function() {
                        applyBackgroundToEditor();
                    });

                    // Prevent typing from affecting background
                    editor.on('NodeChange', function() {
                        maintainBackgroundSize();
                    });
                },
                init_instance_callback: function(editor) {
                    // Set initial conteFnt from hidden textarea
                    editor.setContent($('#certificate_text').val() || '');

                    // Apply background image if stored in the form
                    if ($('#background_image_data').val()) {
                       // backgroundImageData = $('#background_image_data').val();
                        //applyBackgroundToEditor();
                    }
                }
            });
            // Function to refresh the currently selected template after page load
            function refreshSelectedTemplate() {
                // Wait 2 seconds before executing
                setTimeout(function() {
                    // Get the dropdown element
                    const templateDropdown = $('#existing_templates');

                    // Check if the dropdown exists
                    if (templateDropdown.length > 0) {
                        // Get the currently selected value
                        const selectedValue = templateDropdown.val();

                        if (selectedValue && selectedValue !== "") {
                            // If a template is already selected, trigger the change event to refresh it
                            templateDropdown.trigger('change');
                            console.log('Refreshed selected template ID:', selectedValue);
                        } else {
                            // If no template is selected, select the first available one
                            const firstTemplateOption = templateDropdown.find('option[value!=""]').first();

                            if (firstTemplateOption.length > 0) {
                                const firstTemplateId = firstTemplateOption.val();
                                templateDropdown.val(firstTemplateId).trigger('change');
                                console.log('Auto-selected first template ID:', firstTemplateId);
                            } else {
                                console.log('No templates available to select');
                            }
                        }
                    } else {
                        console.log('Template dropdown not found');
                    }
                }, 2000); // 2-second delay
            }

            // Call the function on page load
            refreshSelectedTemplate();
            // Function to maintain background size and position
            function maintainBackgroundSize() {
                const editor = tinymce.get('tinymce-visual-editor');
                if (!editor) return;

                const editorBody = editor.getBody();
                const width = $('#width').val();
                const height = $('#height').val();

                // Set fixed dimensions based on the certificate size
                $(editorBody).css({
                    'width': '100%',
                    'min-height': '600px',
                    'box-sizing': 'border-box',
                    'position': 'relative'
                });
            }
            // Function to apply background to editor with proper sizing
            function applyBackgroundToEditor() {
                if (!backgroundImageData) return;

                const editor = tinymce.get('tinymce-visual-editor');
                if (!editor) return;

                const editorBody = editor.getBody();
                const width = $('#width').val();
                const height = $('#height').val();

                // Calculate aspect ratio for proper display
                const aspectRatio = width / height;

                // Set background image with dimensions matched to paper size
                $(editorBody).css({
                    'background-image': `url(${backgroundImageData})`,
                    'background-size': 'cover',
                    'background-position': 'center',
                    'background-repeat': 'no-repeat',
                    'aspect-ratio': aspectRatio,
                    'width': '100%',
                    'min-height': '600px',
                    'box-sizing': 'border-box',
                    'position': 'relative'
                });

                // Create a container for editor content if it doesn't exist
                if ($(editorBody).find('.certificate-content-container').length === 0) {
                    // Wrap existing content in a container that respects paper dimensions
                    const editorContent = $(editorBody).contents().not('style, script');
                    editorContent.wrapAll('<div class="certificate-content-container"></div>');
                }

                // Style the container to match paper dimensions
                $(editorBody).find('.certificate-content-container').css({
                    'position': 'relative',
                    'width': '100%',
                    'height': '100%',
                    'aspect-ratio': aspectRatio,
                    'z-index': 2
                });
            }

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

            // Enhanced background image upload handler
            $('#background_image').on('change', function() {
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        // Store the background image data globally and in form field
                        backgroundImageData = e.target.result;
                        $('#background_image_data').val(backgroundImageData);

                        // Update filename display
                        const fileName = $('#background_image').val().split('\\').pop();
                        $('#thumbnail').val(fileName || 'Thumbnail');

                        // Apply background to editor
                        applyBackgroundToEditor();
                    };
                    reader.readAsDataURL(file);
                }
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
            // Handle template loading with background persistence
            // When a template is selected from the dropdown
            $('#existing_templates').on('change', function () {
                const templateId = $(this).val();
                if (!templateId) return; // Exit if no template is selected

                loadTemplateData(templateId); // Reuse the function for loading template
            });

            // When "Load in Editor" is clicked
            $('.load-template').on('click', function (e) {
                e.preventDefault(); // Prevent the default link behavior

                const templateId = $(this).data('template-id'); // Get the template ID from data attribute
                if (!templateId) return; // Exit if no template ID is provided

                loadTemplateData(templateId); // Reuse the function for loading template

                // Scroll to the top of the page after loading the template
                $('html, body').animate({
                    scrollTop: 0 // Scroll to the top of the page
                }, 500); // You can adjust the speed (500ms here)
            });
// Function to reset all form fields
            function resetTemplateForm() {
                // Reset form inputs
                $('#template_name').val('');
                $('#certificate_title').val('');
                $('#certificate_heading').val('');
                $('input[name="certificate_type"]').prop('checked', false);
                $('#page_layout').val('');
                $('#height').val('');
                $('#width').val('');
                $('#user_image_shape').val('');
                $('#image_size').val('');
                $('#thumbnail').val('');
                $('#background_image_data').val('');
                $('#template_id').val('');

                // Reset editors
                tinymce.get('tinymce-visual-editor').setContent('');
                codeMirrorEditor.setValue('');

                // Reset background image
                const editor = tinymce.get('tinymce-visual-editor');
                if (editor) {
                    const editorBody = editor.getBody();
                    $(editorBody).css({
                        'background-image': 'none'
                    });
                }

                // Reset background image data
                backgroundImageData = '';

                // Clear any existing template selection
                $('#existing_templates').val('');
                // Scroll to the top of the page
                $('html, body').animate({
                    scrollTop: 0
                }, 500);
            }

// Add event handler for "Create New Template" button
            $('.btn-create-new-template').on('click', function() {
                resetTemplateForm();
            });
            // Function to load template data via AJAX
            function loadTemplateData(templateId) {
                $.ajax({
                    url: '/students/load-certificate-template/' + templateId, // Replace with your correct route
                    type: 'GET',
                    success: function (response) {
                        // Populate form fields
                        $('#template_name').val(response.name);
                        $(`input[name="certificate_type"][value="${response.type}"]`).prop('checked', true);
                        $('#page_layout').val(response.page_layout);
                        $('#height').val(response.height);
                        $('#width').val(response.width);
                        $('#user_image_shape').val(response.user_image_shape);
                        $('#image_size').val(response.image_size);
                        $('#certificate_title').val(response.certificate_title);
                        $('#certificate_heading').val(response.certificate_heading);
                        $('#thumbnail').val(response.background_image);

                        // Update the editors (TinyMCE and CodeMirror, for example)
                        tinymce.get('tinymce-visual-editor').setContent(response.certificate_text);
                        codeMirrorEditor.setValue(response.certificate_text);

                        // Handle background image if exists
                        if (response.background_image_path) {
                            const bgUrl = response.background_image_data || `/${response.background_image_path}`;
                            backgroundImageData = bgUrl;
                            $('#background_image_data').val(bgUrl);

                            // Apply background image with a slight delay to ensure editor is ready
                            setTimeout(applyBackgroundToEditor, 100);
                        }
                    },
                    error: function (error) {
                        console.error('Error loading template:', error);
                        alert('Error loading template. Please try again.');
                    }
                });
            }
// Handle background image display in TinyMCE
            $('#background_image').on('change', function() {
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        // Create temporary image to check dimensions
                        const img = new Image();
                        img.onload = function() {
                            const imgWidth = this.width;
                            const imgHeight = this.height;
                            const certificateWidth = parseInt($('#width').val());
                            const certificateHeight = parseInt($('#height').val());

                            // Check if image dimensions match certificate dimensions
                            if (imgWidth / imgHeight !== certificateWidth / certificateHeight) {
                                // Show warning about aspect ratio mismatch
                                alert('Note: The image aspect ratio doesn\'t match the certificate dimensions. The image will be resized to fit.');
                            }

                            // Update TinyMCE editor with background image
                            const editor = tinymce.get('tinymce-visual-editor');
                            const editorBody = editor.getBody();

                            // Set background image on the editor with proper sizing
                            $(editorBody).css({
                                'background-image': `url(${e.target.result})`,
                                'background-size': 'cover',
                                'background-position': 'center',
                                'background-repeat': 'no-repeat',
                                'width': '100%',
                                'height': '100%'
                            });

                            // Store the background image data for form submission
                            $('#background_image_data').val(e.target.result);

                            // Update filename display
                            const fileName = $('#background_image').val().split('\\').pop();
                            $('#thumbnail').val(fileName || 'Thumbnail');
                        };
                        img.src = e.target.result;
                    };
                    reader.readAsDataURL(file);
                }
            });

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

// Add this to your JavaScript to ensure content is properly formatted for PDF output
            function prepareCertificateContent() {
                const editor = tinymce.get('tinymce-visual-editor');
                if (!editor) return;

                // Get editor content
                let content = editor.getContent();

                // Add responsive container to ensure PDF rendering matches preview
                if (!content.includes('certificate-main-content')) {
                    content = `<div class="certificate-main-content" style="position: relative; width: 100%; height: 100%;">${content}</div>`;
                    editor.setContent(content);
                }

                // Update CodeMirror as well if it's active
                if ($('#html-tab').hasClass('active')) {
                    codeMirrorEditor.setValue(content);
                }

                // Store in hidden field
                $('#certificate_text').val(content);
            }


// Form submission handler with JSON error handling
            $('form').on('submit', function(e) {
                // Determine which button was clicked
                const actionButton = $(document.activeElement);
                const actionValue = actionButton.val();

                // Prepare form data
                prepareCertificateContent();

                // Preserve background image data if available
                if (backgroundImageData) {
                    $('#background_image_data').val(backgroundImageData);
                }

                // Get content based on active tab
                if ($('#visual-tab').hasClass('active')) {
                    $('#certificate_text').val(tinymce.get('tinymce-visual-editor').getContent());
                } else {
                    $('#certificate_text').val(codeMirrorEditor.getValue());
                }

                // Ensure custom CSS is added
                if (!$('#custom_css').length) {
                    const customCSS = generateCustomCSS();
                    $('<input>').attr({
                        type: 'hidden',
                        id: 'custom_css',
                        name: 'custom_css',
                        value: customCSS
                    }).appendTo('form');
                }

                // If save action, use AJAX
                if (actionValue === 'save') {
                    e.preventDefault(); // Prevent default form submission

                    // Prepare form data
                    const formData = new FormData(this);

                    // Store template data as JSON
                    try {
                        const templateData = {
                            template_name: $('#template_name').val(),
                            certificate_type: $('input[name="certificate_type"]:checked').val(),
                            page_layout: $('#page_layout').val(),
                            height: $('#height').val(),
                            width: $('#width').val(),
                            user_image_shape: $('#user_image_shape').val(),
                            image_size: $('#image_size').val(),
                            certificate_title: $('#certificate_title').val(),
                            certificate_heading: $('#certificate_heading').val(),
                            certificate_text: $('#certificate_text').val(),
                            background_image: $('#thumbnail').val()
                        };
                        formData.append('template_data_json', JSON.stringify(templateData));
                    } catch (e) {
                        console.log("JSON encoding error occurred");
                        formData.append('template_data_json', '{}');
                    }

                    // Show loading toast
                    toastr.info('Saving template...', 'Processing', {
                        closeButton: false,
                        timeOut: 0,
                        extendedTimeOut: 0,
                        preventDuplicates: true
                    });

                    // Ajax submission
                    $.ajax({
                        url: '{{ route("save.certificate.template") }}',
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            // Close loading toast
                            toastr.clear();
                            loadTemplates();
                            // Show success toast
                            toastr.success('Certificate template saved successfully', 'Success', {
                                closeButton: true,
                                timeOut: 5000
                            });
                            location.reload();
                            // Optional: update UI or perform additional actions
                            if (response.template_id) {
                                $('#template_id').val(response.template_id);
                            }
                        },
                        error: function(xhr) {
                            // Close loading toast
                            toastr.clear();

                            // Show error toast
                            toastr.error(
                                xhr.responseJSON?.message || 'Failed to save template',
                                'Error',
                                {
                                    closeButton: true,
                                    timeOut: 5000
                                }
                            );
                        }
                    });
                }
                // For generate action, allow normal form submission
            });
            // Helper function to generate any custom CSS needed for the certificate
            function generateCustomCSS() {
                // This would collect any custom CSS needed for the certificate
                // You could expand this to include special formatting or styling
                return `
        /* Ensure proper background handling */
        body {
            background-position: center !important;
            background-repeat: no-repeat !important;
            background-size: cover !important;
        }

        /* Ensure content is properly positioned */
        .certificate-content {
            box-sizing: border-box !important;
        }
    `;
            }
            // Standard paper sizes in mm (width x height)
            const paperSizes = {
                'A4_portrait': { width: 210, height: 297 },
                'A4_landscape': { width: 297, height: 210 },
                'letter_portrait': { width: 216, height: 279 },
                'letter_landscape': { width: 279, height: 216 },
                'A3_portrait': { width: 297, height: 420 },
                'A3_landscape': { width: 420, height: 297 }
            };

            // Function to update dimensions based on page layout
            function updateDimensions() {
                const layoutValue = $('#page_layout').val();
                if (paperSizes[layoutValue]) {
                    $('#width').val(paperSizes[layoutValue].width);
                    $('#height').val(paperSizes[layoutValue].height);
                }
            }
            // Add dimension input change handlers to reapply background
            $('#width, #height').on('change', function() {
                setTimeout(applyBackgroundToEditor, 100);
            });
            // Add this function to initialize background when loading templates
            function initializeEditorBackground() {
                // Apply initial background
                setTimeout(applyBackgroundToEditor, 300);

                // Add resize handler to maintain aspect ratio when window resizes
                $(window).on('resize', function() {
                    applyBackgroundToEditor();
                });
            }
            // Update editor when page layout changes
            $('#page_layout').on('change', function() {
                // First update dimensions
                updateDimensions();

                // Then reapply background with new dimensions
                setTimeout(function() {
                    applyBackgroundToEditor();
                }, 100);
            });
            //volume conteint
            // Volume control functionality
            $('#content-volume-slider').on('input', function() {
                const scaleValue = $(this).val() / 100;
                const editor = tinymce.get('tinymce-visual-editor');
                if (!editor) return;

                const editorBody = editor.getBody();
                const editorContent = $(editorBody).find('*:not(html):not(body)');

                // Apply scaling to all content elements while preserving background
                editorContent.css({
                    'transform': `scale(${scaleValue})`,
                    'transform-origin': 'center top',
                    'transition': 'transform 0.2s ease'
                });

                // Adjust padding to accommodate scale changes
                if (scaleValue > 1) {
                    $(editorBody).css('padding-bottom', `${(scaleValue - 1) * 300}px`);
                } else {
                    $(editorBody).css('padding-bottom', '0');
                }
            });

            // Ensure content scaling persists during editing
            tinymce.get('tinymce-visual-editor').on('NodeChange', function() {
                // Preserve scaling when content changes
                const scaleValue = $('#content-volume-slider').val() / 100;
                const editor = tinymce.get('tinymce-visual-editor');
                if (!editor || scaleValue === 1) return;

                const editorBody = editor.getBody();
                const editorContent = $(editorBody).find('*:not(html):not(body)');

                editorContent.css({
                    'transform': `scale(${scaleValue})`,
                    'transform-origin': 'center top'
                });
            });

            // Add preview button handler to show proper sizing
            $('#preview-certificate-visual, #preview-certificate-html').on('click', function() {
                const content = $('#visual-tab').hasClass('active')
                    ? tinymce.get('tinymce-visual-editor').getContent()
                    : codeMirrorEditor.getValue();

            });


            // Update preview buttons to use enhanced preview
            $('#preview-certificate-visual, #preview-certificate-html').on('click', function() {
                prepareCertificateContent();
                const content = $('#visual-tab').hasClass('active')
                    ? tinymce.get('tinymce-visual-editor').getContent()
                    : codeMirrorEditor.getValue();

            });
            // Start table list show
            // Load templates via AJAX
            function loadTemplates() {
                $.ajax({
                    url: '/students/get-certificate-templates',
                    type: 'GET',
                    dataType: 'json',
                    beforeSend: function() {
                        // Show loading state
                        $('#templates-list').html('<tr><td colspan="6" class="text-center"><i class="mdi mdi-loading mdi-spin"></i> Loading templates...</td></tr>');
                    },
                    success: function(response) {
                        if (response.success) {
                            updateTemplateTable(response.templates);
                        } else {
                            showError('Failed to load templates');
                        }
                    },
                    error: function() {
                        showError('Error connecting to server');
                    }
                });
            }
            // Update template table with data
            function updateTemplateTable(templates) {
                const tableBody = $('#templates-list');
                tableBody.empty();

                if (templates.length === 0) {
                    // Show empty state
                    $('#templates-table').hide();
                    $('#no-templates-message').show();
                    return;
                }

                // Show table, hide empty
                $('#templates-table').show();
                $('#no-templates-message').hide();
                // Initialize datatable
                $('#templates-table').DataTable({
                    "order": [[0, "asc"]],
                    "language": {
                        "search": "{{ __('Search') }}:",
                        "lengthMenu": "{{ __('Show _MENU_ entries') }}",
                        "info": "{{ __('Showing _START_ to _END_ of _TOTAL_ entries') }}",
                        "infoEmpty": "{{ __('Showing 0 to 0 of 0 entries') }}",
                        "infoFiltered": "{{ __('(filtered from _MAX_ total entries)') }}",
                        "zeroRecords": "{{ __('No matching records found') }}",
                        "paginate": {
                            "first": "{{ __('First') }}",
                            "last": "{{ __('Last') }}",
                            "next": "{{ __('Next') }}",
                            "previous": "{{ __('Previous') }}"
                        }
                    }
                });

                // Filter by type
                $('#filter_type').change(function() {
                    var type = $(this).val();
                    $('#templates-table').DataTable().columns(1).search(type).draw();
                });

                // Preview modal
                $('.preview-template').click(function() {
                    var backgroundUrl = $(this).data('background');
                    $('#previewImage').attr('src', backgroundUrl);
                });

                // Delete confirmation
                $('.delete-template').click(function(e) {
                    e.preventDefault();
                    var templateId = $(this).data('template-id');
                    var templateName = $(this).data('template-name');

                    $('#delete_template_id').val(templateId);
                    $('#template-name').text(templateName);
                    $('#deleteModal').modal('show');
                });

                // Load template in editor
                $('.load-template').click(function(e) {
                    e.preventDefault();
                    var templateId = $(this).data('template-id');
                    window.location.href = "{{ url('students/honor-roll-certificate') }}?template_id=" + templateId;
                });
            }
            // Confirm delete action
            $('#confirm-delete-btn').on('click', function() {
                var templateId = $('#delete_template_id').val();

                $.ajax({
                    url: '/students/delete-certificate-template/' + templateId,
                    type: 'DELETE',
                    dataType: 'json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            // Close delete modal
                            $('#deleteModal').modal('hide');

                            // Reload the page to refresh the template list
                            location.reload();
                            // Show success toast
                            toastr.success('Template deleted successfully', 'Success');

                            // Reload templates or remove the row
                            $('#templates-table').DataTable()
                                .row($(`[data-template-id="${templateId}"]`).closest('tr'))
                                .remove()
                                .draw();
                        } else {
                            // Show error toast
                            toastr.error(response.message || 'Failed to delete template', 'Error');
                        }
                    },
                    error: function(xhr) {
                        // Show error toast
                        toastr.error(
                            xhr.responseJSON?.message || 'Error deleting template',
                            'Error'
                        );
                    }
                });
            });
            //btn defaul head and template
            // Header template to insert
            const headerTemplate = `
<div class="certificate-content-container" style="position: relative; width: 100%; height: 100%; z-index: 2;">
  <div class="certificate-main-content" style="position: relative; width: 100%; height: 100%;">
    <div style="width: 900px; margin: 10px auto; padding: 20px; position: relative;">
      <div style="text-align: right; margin-top: 25px; font-size: 12px;">
        <div style="margin-bottom: 15px; line-height: 1; font-size: 9px; position: relative;">
          <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); z-index: 10;">
                    Your logo here
            </div>
            <div style="float: left; width: 45%; text-align: center;">
              REPUBLIQUE DU CAMEROUN
              <br style="transform: scale(1); transform-origin: center top; transition: transform 0.2s;">
              <em style="transform: scale(1); transform-origin: center top; transition: transform 0.2s;">
                Paix - Travail - Patrie
              </em>
              <br style="transform: scale(1); transform-origin: center top; transition: transform 0.2s;">
              MINISTERE DES ENSEIGNEMENTS SECONDAIRES
              <br style="transform: scale(1); transform-origin: center top; transition: transform 0.2s;">
              DELEGATION REGIONALE DE L'EST
              <br style="transform: scale(1); transform-origin: center top; transition: transform 0.2s;">
              DELEGATION DEPARTEMENTALE DU LOMI ET DJEREM
              <br style="transform: scale(1); transform-origin: center top; transition: transform 0.2s;">
              LYCEE TECHNIQUE DE BERTOUA-NKOLBIKON
              <br style="transform: scale(1); transform-origin: center top; transition: transform 0.2s;">
              BP 289 BERTOUA, Tel: 22 24 55 68
            </div>
            <div style="float: right; width: 45%; text-align: center;">
              REPUBLIC OF CAMEROON
              <br style="transform: scale(1); transform-origin: center top; transition: transform 0.2s;">
              <em style="transform: scale(1); transform-origin: center top; transition: transform 0.2s;">
                Peace - Work - Fatherland
              </em>
              <br style="transform: scale(1); transform-origin: center top; transition: transform 0.2s;">
              MINISTRY OF SECONDARY EDUCATION
              <br style="transform: scale(1); transform-origin: center top; transition: transform 0.2s;">
              EAST REGIONAL DELEGATION
              <br style="transform: scale(1); transform-origin: center top; transition: transform 0.2s;">
              DIVISIONAL DELEGATION OF LOMI AND DJEREM
              <br style="transform: scale(1); transform-origin: center top; transition: transform 0.2s;">
              TECHNICAL HIGH SCHOOL OF BERTOUA-NKOLBIKON
              <br style="transform: scale(1); transform-origin: center top; transition: transform 0.2s;">
              P.O BOX 289 BERTOUA, Tel: 22 24 55 68
            </div>
            <div style="content: &quot;&quot;; display: table; clear: both; text-align: center;">
              &nbsp;
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
`;

            const bodyTemplate = `
<div class="certificate-main-body" style="width: 900px; margin: 10px auto; padding: 20px; position: relative;">
  <div class="honor-roll-section" style="text-align: center; margin-top: 20px;">
    <h2>TABLEAU D'HONNEUR</h2>
    <h3>HONOR ROLL</h3>
  </div>
</div>
`;

// Event handlers for Visual Editor
            $('#add-header-btn').on('click', function() {
                tinymce.get('tinymce-visual-editor').execCommand('mceInsertContent', false, headerTemplate);
            });

            $('#add-body-btn').on('click', function() {
                tinymce.get('tinymce-visual-editor').execCommand('mceInsertContent', false, bodyTemplate);
            });

// Event handlers for HTML Editor
            $('#add-header-btn-html').on('click', function() {
                const cursor = codeMirrorEditor.getCursor();
                codeMirrorEditor.replaceRange(headerTemplate, cursor);
            });

            $('#add-body-btn-html').on('click', function() {
                const cursor = codeMirrorEditor.getCursor();
                codeMirrorEditor.replaceRange(bodyTemplate, cursor);
            });
        });

    </script>
@endsection