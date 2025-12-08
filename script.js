// script.js
document.addEventListener('DOMContentLoaded', () => {

    // --- CORE FUNCTIONS ---

    /** Loads content dynamically into the main content area using AJAX. */
    function loadContent(endpoint) {
        document.getElementById('main-content-area').innerHTML = '<h2>Loading...</h2>';
        fetch(endpoint)
            .then(response => response.text())
            .then(html => {
                document.getElementById('main-content-area').innerHTML = html;
            })
            .catch(error => {
                console.error('Error loading content:', error);
                document.getElementById('main-content-area').innerHTML = `<h2 class="error">Error loading content.</h2>`;
            });
    }

    /** Loads the sidebar and main app layout after successful login. */
    function loadMainLayout(role) {
        document.getElementById('login-container').classList.add('hidden');
        document.getElementById('main-app').classList.remove('hidden');

        const sidebar = document.getElementById('sidebar-menu');
        sidebar.innerHTML = `
            <a href="#" data-target="dashboard"><i class="fas fa-home"></i> Home</a>
            <a href="#" data-target="new_contact"><i class="fas fa-user-plus"></i> New Contact</a>
            ${role === 'Admin' ? '<a href="#" data-target="view_users"><i class="fas fa-users"></i> Users</a>' : ''}
            <button id="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</button>
        `;
        
        loadContent('content/dashboard.php?filter=all');
    }

    // --- EVENT HANDLERS ---

    // 1. Login Form Submission
    const loginForm = document.getElementById('login-form');
    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const feedbackDiv = document.getElementById('login-feedback');
            feedbackDiv.innerHTML = '';

            fetch('api/login.php', { method: 'POST', body: formData })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    feedbackDiv.className = 'feedback-message success';
                    feedbackDiv.textContent = 'Login successful, redirecting...';
                    loadMainLayout(data.role);
                } else {
                    feedbackDiv.className = 'feedback-message error';
                    feedbackDiv.textContent = data.message;
                }
            }).catch(error => { console.error('Error during login:', error); });
        });
    }

    // 2. Global Click Handler (Navigation, Filters, View Contact, Contact Actions)
    document.addEventListener('click', function(e) {
        // Navigation (Sidebar/Buttons)
        const navTarget = e.target.closest('a[data-target], button[data-target]');
        if (navTarget) {
            e.preventDefault();
            const page = navTarget.dataset.target;
            if (page === 'dashboard') loadContent('content/dashboard.php?filter=all');
            else if (page === 'new_contact') loadContent('content/new_contact.php');
            else if (page === 'view_users') loadContent('content/view_users.php');
        }
        
        // Dashboard Filters
        if (e.target && e.target.classList.contains('filter-btn')) {
            e.preventDefault();
            const filterType = e.target.dataset.filter;
            document.querySelectorAll('.filter-btn').forEach(btn => btn.classList.remove('active'));
            e.target.classList.add('active');
            loadContent(`content/dashboard.php?filter=${filterType}`);
        }

        // View Contact Link
        if (e.target && e.target.classList.contains('view-contact-link')) {
            e.preventDefault();
            const contactId = e.target.dataset.id;
            loadContent(`content/contact_details.php?id=${contactId}`);
        }

        // Action Buttons (Assign to Me, Switch Type)
        if (e.target && e.target.classList.contains('contact-action-btn')) {
            e.preventDefault();
            const contactId = e.target.dataset.id;
            const action = e.target.dataset.action;
            let endpoint = '';
            let postData = { contact_id: contactId };
            
            if (action === 'assign') {
                endpoint = 'api/update_contact_assignment.php';
            } else if (action === 'switch') {
                endpoint = 'api/switch_contact_type.php';
                postData.current_type = e.target.dataset.currentType;
            }

            if(endpoint) {
                fetch(endpoint, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(postData)
                })
                .then(response => response.json())
                .then(data => {
                    const feedback = document.getElementById('contact-details-feedback');
                    if (data.success) {
                        feedback.className = 'feedback-message success';
                        feedback.textContent = data.message;
                        // Reload the current contact details page to reflect changes
                        loadContent(`content/contact_details.php?id=${contactId}`);
                    } else {
                        feedback.className = 'feedback-message error';
                        feedback.textContent = data.message;
                    }
                }).catch(error => console.error('Action error:', error));
            }
        }
    });
    
    // 3. Global Form Submissions (New User, New Contact, Add Note)
    document.addEventListener('submit', function(e) {
        const formId = e.target.id;
        let endpoint = '';

        if (formId === 'new-user-form') endpoint = 'api/add_user.php';
        else if (formId === 'new-contact-form') endpoint = 'api/add_contact.php';
        else if (formId === 'add-note-form') endpoint = 'api/add_note.php';
        
        if (endpoint) {
            e.preventDefault();
            const formData = new FormData(e.target);
            const feedbackDiv = document.getElementById(formId.replace('-form', '-feedback'));
            
            fetch(endpoint, { method: 'POST', body: formData })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    feedbackDiv.className = 'feedback-message success';
                    feedbackDiv.textContent = data.message;
                    e.target.reset();
                    
                    if (formId === 'add-note-form') {
                        // After adding a note, reload the details view to refresh notes list
                        const contactId = formData.get('contact_id');
                        loadContent(`content/contact_details.php?id=${contactId}`);
                    }
                } else {
                    feedbackDiv.className = 'feedback-message error';
                    feedbackDiv.textContent = data.message;
                }
            }).catch(error => { console.error('Form submission error:', error); });
        }
    });

    // 4. Logout Button
    document.addEventListener('click', function(e) {
        if (e.target.id === 'logout-btn') {
            fetch('api/logout.php')
                .then(() => {
                    document.getElementById('main-app').classList.add('hidden');
                    document.getElementById('login-container').classList.remove('hidden');
                    document.getElementById('login-feedback').textContent = 'You have been logged out.';
                });
        }
    });

});