        // darkmode
        const darkModeToggle = document.getElementById('darkModeToggle');
        const body = document.body;
        
        //var darkmode guardada
        const darkMode = localStorage.getItem('darkMode');
        
        if (darkMode === 'enabled') {
            body.classList.add('dark-mode');
        }
        
        // activar darkmode
        darkModeToggle.addEventListener('click', () => {
            body.classList.toggle('dark-mode');
            
            // variable darkmode 
            if (body.classList.contains('dark-mode')) {
                localStorage.setItem('darkMode', 'enabled');
            } else {
                localStorage.setItem('darkMode', 'disabled');
            }
        });
        
        // Auto-submit filters when changed (for better UX)
        document.querySelectorAll('select[name="mundial"], select[name="categoria"], select[name="orden"]').forEach(select => {
            select.addEventListener('change', function() {
                this.form.submit();
            });
        });
        
        // Toggle comments section
        function toggleComments(postId) {
            const commentsSection = document.getElementById('comments-' + postId);
            if (commentsSection.style.display === 'none') {
                commentsSection.style.display = 'block';
            } else {
                commentsSection.style.display = 'none';
            }
        }
        
        // Image modal functions
        function openImageModal(imageSrc) {
            const modal = document.getElementById('imageModal');
            const modalImg = document.getElementById('modalImage');
            modal.style.display = 'flex';
            modalImg.src = imageSrc;
            document.body.style.overflow = 'hidden';
        }
        
        function closeImageModal() {
            const modal = document.getElementById('imageModal');
            modal.style.display = 'none';
            document.body.style.overflow = 'auto';
        }
        
        // Close modal when clicking outside the image
        document.getElementById('imageModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeImageModal();
            }
        });
        
        // Close modal with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeImageModal();
            }
        });