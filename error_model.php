<?php if (!empty($error)): ?>
    <div id="errorModal" class="modal error-modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2><?= $error ?></h2>
            <button onclick="closeModal()">OK</button>
        </div>
    </div>

    <script>
        var modal = document.getElementById("errorModal");
        modal.style.display = "block"; 

        function closeModal() {
            modal.style.display = "none"; 
        }

        var closeBtn = document.getElementsByClassName("close")[0];
        closeBtn.onclick = function() {
            closeModal();
        }
    </script>
<?php endif; ?>
