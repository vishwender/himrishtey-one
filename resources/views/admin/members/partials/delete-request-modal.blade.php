<div
    class="modal fade"
    id="deleteRequestModal"
    tabindex="-1"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content border-0 shadow">

            <form
                method="POST"
                id="deleteRequestForm">

                @csrf

                <div class="modal-header">

                    <div>

                        <h5 class="modal-title">
                            Raise Profile Delete Request
                        </h5>

                        <div class="small text-muted mt-1">
                            This request will be sent for admin approval.
                        </div>

                    </div>

                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"></button>

                </div>


                <div class="modal-body">

                    <div class="alert alert-warning">

                        <i class="bi bi-exclamation-triangle me-2"></i>

                        You are requesting deletion of

                        <strong id="deleteRequestMemberName"></strong>

                        <span
                            id="deleteRequestProfileId"
                            class="ms-1"></span>.

                    </div>


                    <div>

                        <label class="form-label fw-semibold">
                            Reason for deletion
                            <span class="text-danger">*</span>
                        </label>

                        <textarea
                            name="reason"
                            class="form-control"
                            rows="4"
                            maxlength="1000"
                            required
                            placeholder="Explain why this profile should be deleted..."></textarea>

                        <div class="form-text">
                            The approving administrator will see this reason.
                        </div>

                    </div>

                </div>


                <div class="modal-footer">

                    <button
                        type="button"
                        class="btn btn-light border"
                        data-bs-dismiss="modal">
                        Cancel
                    </button>

                    <button
                        type="submit"
                        class="btn btn-danger">
                        <i class="bi bi-send me-1"></i>
                        Raise Request
                    </button>

                </div>

            </form>

        </div>

    </div>
</div>