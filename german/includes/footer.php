    </div>
  </main>
</div>

<!-- Global confirm-delete modal (reused by every page) -->
<div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="post" id="confirmDeleteForm">
        <div class="modal-header">
          <h5 class="modal-title"><i class="bi bi-exclamation-triangle-fill text-danger me-2"></i>Confirm deletion</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <p id="confirmDeleteText">Are you sure you want to delete this item? This cannot be undone.</p>
          <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
          <input type="hidden" name="id" id="confirmDeleteId" value="">
          <input type="hidden" name="university_id" id="confirmDeleteUniId" value="">
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-danger"><i class="bi bi-trash3 me-1"></i>Delete</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Toast container -->
<div class="toast-container position-fixed bottom-0 end-0 p-3" id="toastContainer"></div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= h(base_path()) ?>/assets/js/app.js"></script>
</body>
</html>
