@extends('admin.layout.layout')

@section('content')
<div class="container-fluid">

    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">

                    <h4 class="card-title mb-4">
                        Import Product Images - Media Gallery
                    </h4>

                    @if(session('success_message'))
                        <div class="alert alert-success">
                            {{ session('success_message') }}
                        </div>
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

                    <form method="POST"
                          action="{{ route('admin.import.images.upload') }}"
                          enctype="multipart/form-data"
                          id="upload-form">
                        @csrf

                        <!-- Dropzone -->
                        <div class="upload-container">

                            <div class="dropzone position-relative">

                                <div class="dropzone-text text-center">
                                    <i class="mdi mdi-cloud-upload display-3 text-muted"></i>
                                    <h5>Drag & Drop images here or click to browse</h5>
                                    <p class="text-muted">
                                        Supported: JPG, JPEG, PNG, WEBP
                                    </p>
                                </div>

                                <!-- IMPORTANT FIX -->
                                <input type="file"
                                       name="images[]"
                                       id="file-input"
                                       class="file-input"
                                       multiple
                                       accept="image/*"
                                       required>
                            </div>

                            <!-- Preview -->
                            <div class="gallery-preview mt-4"
                                 id="gallery-preview"
                                 style="display:none;">
                                <h5>
                                    Selected Images
                                    (<span id="selected-count">0</span>)
                                </h5>
                                <div class="row g-3"
                                     id="gallery-grid"></div>
                            </div>

                            <!-- Progress -->
                            <div class="progress mt-4"
                                 id="progress-wrapper"
                                 style="height:25px; display:none;">
                                <div class="progress-bar progress-bar-striped progress-bar-animated"
                                     id="progress-bar"
                                     style="width:0%">
                                    0%
                                </div>
                            </div>

                        </div>

                        <!-- Buttons -->
                        <div class="mt-4">
                            <button type="submit"
                                    class="btn btn-primary"
                                    id="upload-btn">
                                Upload Images
                            </button>

                            <button type="button"
                                    class="btn btn-danger"
                                    id="clear-btn"
                                    style="display:none;">
                                Clear All
                            </button>
                        </div>

                    </form>

                </div>
            </div>
        </div>
    </div>

</div>

<!-- ================= STYLE ================= -->
<style>

.upload-container {
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 20px;
    background: #f8f9fa;
}

.dropzone {
    border: 2px dashed #bbb;
    border-radius: 8px;
    padding: 60px 20px;
    text-align: center;
    background: #fff;
    cursor: pointer;
    transition: 0.3s;
    position: relative;
}

.dropzone:hover {
    border-color: #4B49AC;
    background: #f0f0fa;
}

/* MAIN FIX */
.file-input {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    opacity: 0;
    cursor: pointer;
}

.gallery-item {
    position: relative;
}

.gallery-img {
    width: 100%;
    height: 150px;
    object-fit: cover;
    border-radius: 8px;
}

.remove-btn {
    position: absolute;
    top: 6px;
    right: 10px;
    background: red;
    color: #fff;
    width: 22px;
    height: 22px;
    border-radius: 50%;
    text-align: center;
    line-height: 20px;
    cursor: pointer;
    font-weight: bold;
}

.file-info {
    font-size: 12px;
    text-align: center;
    margin-top: 5px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

/* Responsive */
@media (max-width: 576px) {
    .gallery-img {
        height: 120px;
    }
}

</style>

<!-- ================= SCRIPT ================= -->
<script>
document.addEventListener("DOMContentLoaded", function () {

    const fileInput = document.getElementById("file-input");
    const galleryGrid = document.getElementById("gallery-grid");
    const galleryPreview = document.getElementById("gallery-preview");
    const selectedCount = document.getElementById("selected-count");
    const clearBtn = document.getElementById("clear-btn");

    // IMPORTANT
    let dataTransfer = new DataTransfer();

    // When selecting files
    fileInput.addEventListener("change", function () {

        Array.from(fileInput.files).forEach(file => {
            dataTransfer.items.add(file);
            previewFile(file);
        });

        updateInputFiles();
    });

    // Preview image
    function previewFile(file) {

        galleryPreview.style.display = "block";
        clearBtn.style.display = "inline-block";

        const reader = new FileReader();

        reader.onload = function (e) {

            const col = document.createElement("div");
            col.className = "col-6 col-sm-4 col-md-3 col-lg-2 gallery-item";

            col.innerHTML = `
                <div class="position-relative">
                    <div class="remove-btn">&times;</div>
                    <img src="${e.target.result}" class="gallery-img">
                    <div class="file-info">${file.name}</div>
                </div>
            `;

            // REMOVE BUTTON EVENT
            col.querySelector(".remove-btn").addEventListener("click", function () {
                removeFile(file, col);
            });

            galleryGrid.appendChild(col);
            updateCount();
        };

        reader.readAsDataURL(file);
    }

    // Remove file properly
    function removeFile(fileToRemove, element) {

        const newTransfer = new DataTransfer();

        Array.from(dataTransfer.files).forEach(file => {
            if (
                file.name !== fileToRemove.name ||
                file.size !== fileToRemove.size ||
                file.lastModified !== fileToRemove.lastModified
            ) {
                newTransfer.items.add(file);
            }
        });

        dataTransfer = newTransfer;

        updateInputFiles();
        element.remove();

        if (dataTransfer.files.length === 0) {
            galleryPreview.style.display = "none";
            clearBtn.style.display = "none";
        }
    }

    function updateInputFiles() {
        fileInput.files = dataTransfer.files;
        updateCount();
    }

    function updateCount() {
        selectedCount.innerText = dataTransfer.files.length;
    }

    // Clear All
    clearBtn.addEventListener("click", function () {
        dataTransfer = new DataTransfer();
        fileInput.files = dataTransfer.files;
        galleryGrid.innerHTML = "";
        galleryPreview.style.display = "none";
        clearBtn.style.display = "none";
        updateCount();
    });

});
</script>


@endsection
