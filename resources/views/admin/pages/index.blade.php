@extends('admin.layout')

@section('content')

<div class="container-fluid py-4">

    {{-- Header --}}

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h4 class="mb-1">
                Website Pages
            </h4>

            <p class="text-muted mb-0">
                Manage website policies, terms and informational content.
            </p>

        </div>

    </div>


    {{-- Success Message --}}

    @if(session('success'))

    <div class="alert alert-success alert-dismissible fade show">

        <i class="bi bi-check-circle me-2"></i>

        {{ session('success') }}

        <button
            type="button"
            class="btn-close"
            data-bs-dismiss="alert">
        </button>

    </div>

    @endif


    {{-- Error Message --}}

    @if(session('error'))

    <div class="alert alert-danger alert-dismissible fade show">

        <i class="bi bi-exclamation-triangle me-2"></i>

        {{ session('error') }}

        <button
            type="button"
            class="btn-close"
            data-bs-dismiss="alert">
        </button>

    </div>

    @endif


    {{-- Validation Errors --}}

    @if($errors->any())

    <div class="alert alert-danger">

        <strong>
            Please correct the following:
        </strong>

        <ul class="mb-0 mt-2">

            @foreach($errors->all() as $error)

            <li>
                {{ $error }}
            </li>

            @endforeach

        </ul>

    </div>

    @endif


    @if($page)

    <form
        method="POST"
        action="{{ route('admin.pages.update') }}">

        @csrf

        @method('PUT')


        <div class="card border-0 shadow-sm">

            <div class="card-body p-0">

                {{-- Tabs --}}

                <div class="pages-tabs">

                    <button
                        type="button"
                        class="page-tab active"
                        data-target="refund-policy">

                        <i class="bi bi-arrow-counterclockwise"></i>

                        Refund Policy

                    </button>


                    <button
                        type="button"
                        class="page-tab"
                        data-target="privacy-policy">

                        <i class="bi bi-shield-lock"></i>

                        Privacy Policy

                    </button>


                    <button
                        type="button"
                        class="page-tab"
                        data-target="terms">

                        <i class="bi bi-file-earmark-text"></i>

                        Terms & Conditions

                    </button>


                    <button
                        type="button"
                        class="page-tab"
                        data-target="about-us">

                        <i class="bi bi-info-circle"></i>

                        About Us

                    </button>

                </div>


                {{-- Content --}}

                <div class="p-4">

                    {{-- Refund Policy --}}

                    <div
                        class="page-panel active"
                        id="refund-policy">

                        <div class="mb-3">

                            <h5 class="mb-1">
                                Refund Policy
                            </h5>

                            <p class="text-muted small">
                                Content displayed on the website's Refund Policy page.
                            </p>

                        </div>


                        <textarea
                            id="refund_policy"
                            name="refund_policy"
                            class="page-editor"
                            required>{{ old('refund_policy', $page->refund_policy) }}</textarea>

                    </div>


                    {{-- Privacy Policy --}}

                    <div
                        class="page-panel"
                        id="privacy-policy">

                        <div class="mb-3">

                            <h5 class="mb-1">
                                Privacy Policy
                            </h5>

                            <p class="text-muted small">
                                Content displayed on the website's Privacy Policy page.
                            </p>

                        </div>


                        <textarea
                            id="privacy_policy"
                            name="privacy_policy"
                            class="page-editor"
                            required>{{ old('privacy_policy', $page->privacy_policy) }}</textarea>

                    </div>


                    {{-- Terms --}}

                    <div
                        class="page-panel"
                        id="terms">

                        <div class="mb-3">

                            <h5 class="mb-1">
                                Terms & Conditions
                            </h5>

                            <p class="text-muted small">
                                Content displayed on the website's Terms & Conditions page.
                            </p>

                        </div>


                        <textarea
                            id="terms_and_conditions"
                            name="terms_and_conditions"
                            class="page-editor"
                            required>{{ old('terms_and_conditions', $page->terms_and_conditions) }}</textarea>

                    </div>


                    {{-- About Us --}}

                    <div
                        class="page-panel"
                        id="about-us">

                        <div class="mb-3">

                            <h5 class="mb-1">
                                About Us
                            </h5>

                            <p class="text-muted small">
                                Content displayed on the website's About Us page.
                            </p>

                        </div>


                        <textarea
                            id="about_us"
                            name="about_us"
                            class="page-editor"
                            required>{{ old('about_us', $page->about_us) }}</textarea>

                    </div>

                </div>


                {{-- Footer --}}

                <div class="pages-footer">

                    <div class="text-muted small">

                        @if($page->updated_at)

                        Last updated:
                        {{ \Carbon\Carbon::parse($page->updated_at)->format('d M Y, h:i A') }}

                        @endif

                    </div>


                    <button
                        type="submit"
                        class="btn btn-primary px-4">

                        <i class="bi bi-check-circle me-1"></i>

                        Save Changes

                    </button>

                </div>

            </div>

        </div>

    </form>

    @else

    <div class="card border-0 shadow-sm">

        <div class="card-body text-center py-5">

            <i class="bi bi-file-earmark-x fs-1 text-muted"></i>

            <h5 class="mt-3">
                Pages configuration not found
            </h5>

            <p class="text-muted mb-0">
                No pages configuration record exists in the database.
            </p>

        </div>

    </div>

    @endif

</div>


<style>
    .pages-tabs {
        display: flex;
        align-items: stretch;
        border-bottom: 1px solid #e5e7eb;
        overflow-x: auto;
    }

    .page-tab {
        border: 0;
        background: transparent;
        padding: 16px 22px;
        color: #6b7280;
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
        white-space: nowrap;
        border-bottom: 3px solid transparent;
        transition: all .2s ease;
    }

    .page-tab:hover {
        color: #111827;
        background: #f9fafb;
    }

    .page-tab.active {
        color: #0d6efd;
        border-bottom-color: #0d6efd;
        background: #f8faff;
    }

    .page-tab i {
        margin-right: 7px;
    }


    .page-panel {
        display: none;
    }

    .page-panel.active {
        display: block;
    }

    .pages-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 15px;
        padding: 18px 24px;
        border-top: 1px solid #e5e7eb;
    }


    @media(max-width: 768px) {

        .pages-tabs {
            flex-wrap: nowrap;
        }

        .pages-footer {
            flex-direction: column;
            align-items: stretch;
        }

    }
</style>

<script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/classic/ckeditor.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {

        /*
        |--------------------------------------------------------------------------
        | Page Tabs
        |--------------------------------------------------------------------------
        */

        const tabs = document.querySelectorAll('.page-tab');
        const panels = document.querySelectorAll('.page-panel');

        tabs.forEach(function(tab) {

            tab.addEventListener('click', function() {

                const target = this.dataset.target;

                tabs.forEach(function(item) {
                    item.classList.remove('active');
                });

                panels.forEach(function(panel) {
                    panel.classList.remove('active');
                });

                this.classList.add('active');

                const targetPanel = document.getElementById(target);

                if (targetPanel) {
                    targetPanel.classList.add('active');
                }

            });

        });


        /*
        |--------------------------------------------------------------------------
        | CKEditor
        |--------------------------------------------------------------------------
        */

        const editorFields = [
            'refund_policy',
            'privacy_policy',
            'terms_and_conditions',
            'about_us'
        ];


        editorFields.forEach(function(fieldId) {

            const element = document.getElementById(fieldId);

            if (!element) {
                return;
            }


            ClassicEditor
                .create(element, {

                    toolbar: [
                        'undo',
                        'redo',
                        '|',
                        'heading',
                        '|',
                        'bold',
                        'italic',
                        'underline',
                        'strikethrough',
                        '|',
                        'fontSize',
                        'fontFamily',
                        'fontColor',
                        'fontBackgroundColor',
                        '|',
                        'alignment',
                        '|',
                        'bulletedList',
                        'numberedList',
                        '|',
                        'outdent',
                        'indent',
                        '|',
                        'link',
                        'insertTable',
                        '|',
                        'blockQuote',
                        'horizontalLine',
                        '|',
                        'removeFormat'
                    ],

                    heading: {
                        options: [{
                                model: 'paragraph',
                                title: 'Paragraph',
                                class: 'ck-heading_paragraph'
                            },
                            {
                                model: 'heading1',
                                view: 'h1',
                                title: 'Heading 1',
                                class: 'ck-heading_heading1'
                            },
                            {
                                model: 'heading2',
                                view: 'h2',
                                title: 'Heading 2',
                                class: 'ck-heading_heading2'
                            },
                            {
                                model: 'heading3',
                                view: 'h3',
                                title: 'Heading 3',
                                class: 'ck-heading_heading3'
                            }
                        ]
                    }

                })

                .then(function(editor) {

                    console.log(
                        'CKEditor initialized:',
                        fieldId
                    );

                })

                .catch(function(error) {

                    console.error(
                        'CKEditor initialization failed:',
                        fieldId,
                        error
                    );

                });

        });

    });
</script>

@endsection