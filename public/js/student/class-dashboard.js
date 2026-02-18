// Student Class Dashboard JavaScript

// Teacher Messages Modal Functions
function openTeacherMessagesModal() {
    document.getElementById('teacherMessagesModal').classList.remove('hidden');
    document.getElementById('teacherMessagesModal').classList.add('flex');
    document.body.style.overflow = 'hidden';
}

function closeTeacherMessagesModal() {
    document.getElementById('teacherMessagesModal').classList.add('hidden');
    document.getElementById('teacherMessagesModal').classList.remove('flex');
    document.body.style.overflow = '';
}

// Abandon Quest Modal Functions
function openAbandonQuestModal() {
    document.getElementById('abandonQuestModal').classList.remove('hidden');
    document.getElementById('abandonQuestModal').classList.add('flex');
    document.body.style.overflow = 'hidden';
}

function closeAbandonQuestModal() {
    document.getElementById('abandonQuestModal').classList.add('hidden');
    document.getElementById('abandonQuestModal').classList.remove('flex');
    document.body.style.overflow = '';
}

// Initialize event listeners when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    // Close modals when clicking outside
    const teacherMessagesModal = document.getElementById('teacherMessagesModal');
    if (teacherMessagesModal) {
        teacherMessagesModal.addEventListener('click', function(e) {
            if (e.target === this) {
                closeTeacherMessagesModal();
            }
        });
    }

    const abandonQuestModal = document.getElementById('abandonQuestModal');
    if (abandonQuestModal) {
        abandonQuestModal.addEventListener('click', function(e) {
            if (e.target === this) {
                closeAbandonQuestModal();
            }
        });
    }

    // Close modals with Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeTeacherMessagesModal();
            closeAbandonQuestModal();
        }
    });

    // Check if class still exists
    const classroomId = window.classroomId;
    if (classroomId) {
        let checkInterval;
        
        async function checkClassStatus() {
            try {
                const response = await fetch(`/api/class/${classroomId}/status`, {
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    }
                });
                
                if (!response.ok) {
                    // Class doesn't exist anymore - redirect immediately
                    clearInterval(checkInterval);
                    window.location.href = '/student/dashboard';
                    return;
                }
                
                const data = await response.json();
                if (!data.exists) {
                    // Class was deleted - redirect immediately
                    clearInterval(checkInterval);
                    window.location.href = '/student/dashboard';
                }
            } catch (error) {
                console.log('Error checking class status:', error);
                // If there's an error, assume class might be deleted and redirect
                clearInterval(checkInterval);
                window.location.href = '/student/dashboard';
            }
        }
        
        // Check immediately when page loads
        checkClassStatus();
        
        // Then check every 10 seconds for faster detection
        checkInterval = setInterval(checkClassStatus, 10000);
        
        // Cleanup when page unloads
        window.addEventListener('beforeunload', function() {
            if (checkInterval) {
                clearInterval(checkInterval);
            }
        });
    }
});
