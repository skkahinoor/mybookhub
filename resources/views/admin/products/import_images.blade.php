@extends('admin.layout.layout')

@section('content')
<div class="container-fluid">

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-4">Import Product Images - Media Gallery</h4>

                    @if(session('success_message'))
                        <div class="alert alert-success">{{ session('success_message') }}</div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('admin.import.images.upload') }}" enctype="multipart/form-data" id="upload-form">
                        @csrf

                        <div class="upload-container">
                            <div class="dropzone" id="dropzone">
                                <div class="dropzone-text">
                                    <i class="mdi mdi-cloud-upload display-3 text-muted"></i>
                                    <h5>Drag & Drop images here or click to browse</h5>
                                    <p class="text-muted">Supported formats: JPG, JPEG, PNG, WEBP</p>
                                </div>
                                <input type="file" name="images[]" id="file-input" class="file-input" multiple accept="image/*" required>
                            </div>

                            <div class="gallery-preview mt-4" id="gallery-preview" style="display: none;">
                                <h5 class="mb-3">Selected Images (<span id="selected-count">0</span>)</h5>
                                <div class="row" id="gallery-grid">
                                    <!-- Preview items will be added here -->
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary mr-2">
                                <i class="mdi mdi-cloud-upload"></i> Upload Images
                            </button>
                            <button type="button" class="btn btn-light" id="clear-all-btn" style="display: none;">
                                Clear All
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>

<style>
    .upload-container {
        border: 1px solid #e3e3e3;
        border-radius: 8px;
        padding: 20px;
        background: #f9f9f9;
    }
    .dropzone {
        border: 2px dashed #b1b1b1;
        border-radius: 6px;
        padding: 40px 20px;
        text-align: center;
        background: #fff;
        cursor: pointer;
        transition: all 0.3s ease;
        position: relative;
    }
    .dropzone:hover, .dropzone.dragover {
        border-color: #4B49AC;
        background-color: #f0f0fa;
    }
    .file-input {
        display: none;
    }
    .gallery-item {
        position: relative;
        margin-bottom: 20px;
    }
    /* ... existing CSS ... */
    .dropzone-text {
        pointer-events: none;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const dropzone = document.getElementById('dropzone');
        const fileInput = document.getElementById('file-input');
        const galleryPreview = document.getElementById('gallery-preview');
        const galleryGrid = document.getElementById('gallery-grid');
        const selectedCount = document.getElementById('selected-count');
        const clearAllBtn = document.getElementById('clear-all-btn');
        
        // DataTransfer object to manage files
        const dataTransfer = new DataTransfer();
        let dragCounter = 0;

        // Open file selector when clicking dropzone
        dropzone.addEventListener('click', function() {
            fileInput.click();
        });

        // Handle Drag & Drop events
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropzone.addEventListener(eventName, preventDefaults, false);
        });

        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        dropzone.addEventListener('dragenter', function(e) {
            dragCounter++;
            dropzone.classList.add('dragover');
        });

        dropzone.addEventListener('dragleave', function(e) {
            dragCounter--;
            if (dragCounter <= 0) {
                dropzone.classList.remove('dragover');
                dragCounter = 0;
            }
        });

        dropzone.addEventListener('drop', handleDrop, false);

        function handleDrop(e) {
            dragCounter = 0;
            dropzone.classList.remove('dragover');
            
            const dt = e.dataTransfer;
            const files = dt.files;
            handleFiles(files);
        }

        // Handle File Input Change
        fileInput.addEventListener('change', function() {
            // Add new files to our dataTransfer
            const newFiles = Array.from(fileInput.files);
            handleFiles(newFiles);
            
            // Note: We don't need to reset fileInput value because we overwrite it in updateMainInput
            // But if user clicks Cancel, change doesn't fire.
        });
        
        function handleFiles(files) {
            if (files.length > 0) {
                Array.from(files).forEach(file => {
                    // Check for duplicates based on name, size, lastModified
                    let exists = Array.from(dataTransfer.files).some(f => 
                        f.name === file.name && 
                        f.size === file.size && 
                        f.lastModified === file.lastModified
                    );
                    
                    if (!exists && file.type.startsWith('image/')) {
                        dataTransfer.items.add(file);
                        previewFile(file);
                    }
                });

                updateMainInput();
            }
        }

        function previewFile(file) {
            galleryPreview.style.display = 'block';
            clearAllBtn.style.display = 'inline-block';

            const reader = new FileReader();
            reader.readAsDataURL(file);
            reader.onloadend = function() {
                const col = document.createElement('div');
                col.className = 'col-6 col-sm-4 col-md-3 col-lg-2 gallery-item';
                
                col.innerHTML = `
                    <div class="card">
                        <div class="remove-btn" title="Remove">&times;</div>
                        <img src="${reader.result}" class="gallery-img" alt="${file.name}">
                        <div class="file-info text-center" title="${file.name}">${file.name}</div>
                    </div>
                `;
                
                // Add remove event
                col.querySelector('.remove-btn').addEventListener('click', function(e) {
                    e.stopPropagation(); // prevent other clicks
                    removeFile(file, col);
                });

                galleryGrid.appendChild(col);
                updateCount();
            }
        }

        function removeFile(fileToRemove, colElement) {
            const newDataTransfer = new DataTransfer();
            
            // Copy all files EXCEPT the one we are removing
            Array.from(dataTransfer.files).forEach(file => {
                // Unique identification: name + size + lastModified
                if (file !== fileToRemove && 
                   (file.name !== fileToRemove.name || file.size !== fileToRemove.size || file.lastModified != fileToRemove.lastModified)) {
                    newDataTransfer.items.add(file);
                }
            });
            
            // Update the global dataTransfer
            dataTransfer.items.clear();
            Array.from(newDataTransfer.files).forEach(file => dataTransfer.items.add(file));
            
            // Update UI
            colElement.remove();
            updateMainInput();
            
            if (dataTransfer.files.length === 0) {
                galleryPreview.style.display = 'none';
                clearAllBtn.style.display = 'none';
            }
        }

        function updateMainInput() {
            fileInput.files = dataTransfer.files;
            updateCount();
        }

        function updateCount() {
            selectedCount.textContent = dataTransfer.files.length;
        }

        clearAllBtn.addEventListener('click', function() {
            dataTransfer.items.clear();
            fileInput.files = dataTransfer.files;
            galleryGrid.innerHTML = '';
            galleryPreview.style.display = 'none';
            clearAllBtn.style.display = 'none';
            updateCount();
        });
    });
</script>
@endsection

