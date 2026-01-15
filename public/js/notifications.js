// Système de notifications
class NotificationSystem {
    constructor() {
        this.notifications = [];
        this.unreadCount = 0;
        this.isOpen = false;
        this.init();
    }

    init() {
        this.createNotificationUI();
        this.fetchNotifications();
        // Rafraîchir toutes les 2 minutes
        setInterval(() => this.fetchNotifications(), 120000);
    }

    createNotificationUI() {
        // Créer le badge et le bouton dans la navigation
        const sidebar = document.querySelector('.sidebar');
        if (!sidebar) return;

        const notifContainer = document.createElement('div');
        notifContainer.className = 'notification-container';
        notifContainer.innerHTML = `
            <button class="notification-btn" id="notificationBtn">
                <span class="notification-icon">🔔</span>
                <span class="notification-badge" id="notificationBadge" style="display: none;">0</span>
            </button>
        `;

        // Insérer avant user-info
        const userInfo = sidebar.querySelector('.user-info');
        if (userInfo) {
            sidebar.insertBefore(notifContainer, userInfo);
        }

        // Créer le dropdown
        const dropdown = document.createElement('div');
        dropdown.className = 'notification-dropdown';
        dropdown.id = 'notificationDropdown';
        dropdown.innerHTML = `
            <div class="notification-header">
                <h3>🔔 Notifications</h3>
                <button class="mark-all-read" id="markAllRead">Tout marquer comme lu</button>
            </div>
            <div class="notification-list" id="notificationList">
                <div class="notification-loading">
                    <div class="loading-spinner-small"></div>
                    <p>Chargement...</p>
                </div>
            </div>
        `;

        document.body.appendChild(dropdown);

        // Event listeners
        document.getElementById('notificationBtn').addEventListener('click', () => this.toggleDropdown());
        document.getElementById('markAllRead').addEventListener('click', () => this.markAllAsRead());

        // Fermer si clic ailleurs
        document.addEventListener('click', (e) => {
            if (!e.target.closest('.notification-container') && !e.target.closest('.notification-dropdown')) {
                this.closeDropdown();
            }
        });
    }

    async fetchNotifications() {
        try {
            const response = await fetch('/views/api/notifications_data.php');
            const data = await response.json();

            this.notifications = data.notifications || [];
            this.unreadCount = data.unread_count || 0;

            this.updateBadge();
            if (this.isOpen) {
                this.renderNotifications();
            }
        } catch (error) {
            console.error('Erreur chargement notifications:', error);
        }
    }

    updateBadge() {
        const badge = document.getElementById('notificationBadge');
        if (!badge) return;

        if (this.unreadCount > 0) {
            badge.textContent = this.unreadCount > 99 ? '99+' : this.unreadCount;
            badge.style.display = 'flex';
        } else {
            badge.style.display = 'none';
        }
    }

    toggleDropdown() {
        this.isOpen = !this.isOpen;
        const dropdown = document.getElementById('notificationDropdown');

        if (this.isOpen) {
            dropdown.classList.add('active');
            this.renderNotifications();
        } else {
            dropdown.classList.remove('active');
        }
    }

    closeDropdown() {
        this.isOpen = false;
        const dropdown = document.getElementById('notificationDropdown');
        dropdown.classList.remove('active');
    }

    renderNotifications() {
        const list = document.getElementById('notificationList');

        if (this.notifications.length === 0) {
            list.innerHTML = `
                <div class="notification-empty">
                    <div class="empty-icon">✅</div>
                    <p>Aucune notification</p>
                    <small>Tout est à jour !</small>
                </div>
            `;
            return;
        }

        list.innerHTML = this.notifications.map(notif => `
            <a href="${notif.link}" class="notification-item notification-${notif.urgency}">
                <div class="notification-icon-wrapper">
                    <span class="notification-emoji">${notif.icon}</span>
                </div>
                <div class="notification-content">
                    <div class="notification-title">${notif.title}</div>
                    <div class="notification-message">${notif.message}</div>
                    <div class="notification-time">${this.formatDate(notif.date)}</div>
                </div>
            </a>
        `).join('');
    }

    markAllAsRead() {
        // Animation de suppression
        const items = document.querySelectorAll('.notification-item');
        items.forEach((item, index) => {
            setTimeout(() => {
                item.style.animation = 'slideOutRight 0.3s ease-out forwards';
            }, index * 50);
        });

        setTimeout(() => {
            this.notifications = [];
            this.unreadCount = 0;
            this.updateBadge();
            this.renderNotifications();
        }, items.length * 50 + 300);
    }

    formatDate(dateString) {
        const date = new Date(dateString);
        const now = new Date();
        const diffMs = now - date;
        const diffMins = Math.floor(diffMs / 60000);
        const diffHours = Math.floor(diffMs / 3600000);
        const diffDays = Math.floor(diffMs / 86400000);

        if (diffMins < 1) return 'À l\'instant';
        if (diffMins < 60) return `Il y a ${diffMins} min`;
        if (diffHours < 24) return `Il y a ${diffHours}h`;
        if (diffDays < 7) return `Il y a ${diffDays}j`;

        return date.toLocaleDateString('fr-FR', {
            day: '2-digit',
            month: 'short'
        });
    }
}

// Initialiser le système de notifications quand le DOM est prêt
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        window.notificationSystem = new NotificationSystem();
    });
} else {
    window.notificationSystem = new NotificationSystem();
}
