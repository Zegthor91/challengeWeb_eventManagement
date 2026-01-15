<?php
require_once '../config/database.php';
require_once '../config/session.php';
require_once '../config/helpers.php';

requireLogin();

$user = getCurrentUser();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Vue.js - Gestion Événements</title>
    <link rel="stylesheet" href="/public/css/style.css">
    <link rel="stylesheet" href="/public/css/notifications.css">
    <style>
        [v-cloak] { display: none; }
        
        .vue-dashboard {
            padding: 20px;
        }
        
        .stats-grid-vue {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card-vue {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transition: all 0.3s;
            cursor: pointer;
            position: relative;
            overflow: hidden;
        }
        
        .stat-card-vue:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 15px rgba(0,0,0,0.2);
        }
        
        .stat-card-vue::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: var(--card-color);
        }
        
        .stat-icon {
            font-size: 48px;
            margin-bottom: 10px;
        }
        
        .stat-label {
            font-size: 14px;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 5px;
        }
        
        .stat-value {
            font-size: 36px;
            font-weight: bold;
            color: #333;
            margin-bottom: 5px;
        }
        
        .stat-change {
            font-size: 12px;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .stat-change.positive {
            color: #2ecc71;
        }
        
        .stat-change.negative {
            color: #e74c3c;
        }
        
        .section-vue {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        
        .section-vue h2 {
            margin-bottom: 20px;
            color: #333;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .event-item, .task-item, .alert-item {
            padding: 15px;
            border-left: 4px solid #667eea;
            background: #f8f9fa;
            margin-bottom: 10px;
            border-radius: 5px;
            transition: all 0.2s;
        }
        
        .event-item:hover, .task-item:hover, .alert-item:hover {
            background: #e9ecef;
            transform: translateX(5px);
        }
        
        .item-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 5px;
        }
        
        .item-title {
            font-weight: bold;
            font-size: 16px;
        }
        
        .item-date {
            color: #666;
            font-size: 13px;
        }
        
        .badge-vue {
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .badge-urgent {
            background: #fee;
            color: #e74c3c;
        }
        
        .badge-warning {
            background: #ffeaa7;
            color: #fdcb6e;
        }
        
        .badge-success {
            background: #d4edda;
            color: #28a745;
        }
        
        .loading {
            text-align: center;
            padding: 40px;
            color: #999;
        }
        
        .loading-spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #667eea;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 0 auto 15px;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .empty-state {
            text-align: center;
            padding: 40px;
            color: #999;
        }
        
        .empty-icon {
            font-size: 64px;
            margin-bottom: 15px;
            opacity: 0.3;
        }
        
        .refresh-btn {
            background: #667eea;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s;
        }
        
        .refresh-btn:hover {
            background: #5568d3;
            transform: scale(1.05);
        }
        
        .refresh-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
    </style>
</head>
<body>
    <div class="container">
        <nav class="sidebar">
            <h2>Gestion Events</h2>
            <ul>
                <li><a href="dashboard.php" class="active">Dashboard</a></li>
                <li><a href="events/liste.php">Événements</a></li>
                <li><a href="carte.php">Carte</a></li>
                <li><a href="budget/liste.php">Budget</a></li>
                <li><a href="personnel/liste.php">Personnel</a></li>
                <li><a href="prestataires/liste.php">Prestataires</a></li>
                <li><a href="tasks/liste.php">Tâches</a></li>
            </ul>
            <div class="user-info">
                <p><strong><?php echo htmlspecialchars($user['nom']); ?></strong></p>
                <p><?php echo htmlspecialchars($user['role']); ?></p>
                <a href="logout.php">Déconnexion</a>
            </div>
        </nav>
        
        <main class="main-content">
            <div id="app" v-cloak>
                <div class="page-header">
                    <h1>📊 Dashboard Vue.js</h1>
                    <button @click="refreshData" :disabled="loading" class="refresh-btn">
                        <span v-if="!loading">🔄 Actualiser</span>
                        <span v-else>⏳ Chargement...</span>
                    </button>
                </div>
                
                <!-- Loading -->
                <div v-if="loading && !hasData" class="loading">
                    <div class="loading-spinner"></div>
                    <p>Chargement des données...</p>
                </div>
                
                <!-- Stats Cards -->
                <div v-if="hasData" class="stats-grid-vue">
                    <div class="stat-card-vue" style="--card-color: #667eea;" @click="navigateTo('events/liste.php')">
                        <div class="stat-icon">📅</div>
                        <div class="stat-label">Événements</div>
                        <div class="stat-value">{{ stats.total_events }}</div>
                        <div class="stat-change positive">
                            <span>↗</span>
                            <span>{{ stats.events_en_cours }} en cours</span>
                        </div>
                    </div>
                    
                    <div class="stat-card-vue" style="--card-color: #2ecc71;" @click="navigateTo('tasks/liste.php')">
                        <div class="stat-icon">✅</div>
                        <div class="stat-label">Tâches</div>
                        <div class="stat-value">{{ stats.total_tasks }}</div>
                        <div class="stat-change" :class="stats.tasks_a_faire > 5 ? 'negative' : 'positive'">
                            <span>{{ stats.tasks_a_faire > 5 ? '⚠' : '✓' }}</span>
                            <span>{{ stats.tasks_a_faire }} à faire</span>
                        </div>
                    </div>
                    
                    <div class="stat-card-vue" style="--card-color: #f39c12;">
                        <div class="stat-icon">💰</div>
                        <div class="stat-label">Budget Total</div>
                        <div class="stat-value">{{ formatMoney(stats.budget_total) }}</div>
                        <div class="stat-change positive">
                            <span>💳</span>
                            <span>{{ stats.budget_count }} budgets</span>
                        </div>
                    </div>
                    
                    <div class="stat-card-vue" style="--card-color: #e74c3c;">
                        <div class="stat-icon">👥</div>
                        <div class="stat-label">Personnel</div>
                        <div class="stat-value">{{ stats.total_personnel }}</div>
                        <div class="stat-change positive">
                            <span>✓</span>
                            <span>Actifs</span>
                        </div>
                    </div>
                </div>
                
                <!-- Prochains Événements -->
                <div v-if="hasData" class="section-vue">
                    <h2>📅 Prochains Événements</h2>
                    <div v-if="upcomingEvents.length > 0">
                        <div v-for="event in upcomingEvents" :key="event.id" class="event-item">
                            <div class="item-header">
                                <span class="item-title">{{ event.nom }}</span>
                                <span class="badge-vue" :class="getBadgeClass(event.statut)">{{ event.statut }}</span>
                            </div>
                            <div class="item-date">
                                📍 {{ event.lieu }} • 📆 {{ formatDate(event.date_debut) }}
                            </div>
                        </div>
                    </div>
                    <div v-else class="empty-state">
                        <div class="empty-icon">📅</div>
                        <p>Aucun événement à venir</p>
                    </div>
                </div>
                
                <!-- Tâches Urgentes -->
                <div v-if="hasData" class="section-vue">
                    <h2>⚠️ Tâches Urgentes</h2>
                    <div v-if="urgentTasks.length > 0">
                        <div v-for="task in urgentTasks" :key="task.id" class="task-item">
                            <div class="item-header">
                                <span class="item-title">{{ task.titre }}</span>
                                <span class="badge-vue badge-urgent">{{ task.priorite }}</span>
                            </div>
                            <div class="item-date">
                                🎯 {{ task.event_nom || 'Sans événement' }} • ⏰ {{ formatDate(task.date_limite) }}
                            </div>
                        </div>
                    </div>
                    <div v-else class="empty-state">
                        <div class="empty-icon">✅</div>
                        <p>Aucune tâche urgente !</p>
                    </div>
                </div>
                
                <!-- Alertes Budget -->
                <div v-if="hasData && budgetAlerts.length > 0" class="section-vue">
                    <h2>💰 Alertes Budget</h2>
                    <div v-for="alert in budgetAlerts" :key="alert.event_id" class="alert-item">
                        <div class="item-header">
                            <span class="item-title">{{ alert.event_nom }}</span>
                            <span class="badge-vue badge-warning">{{ alert.percentage }}% utilisé</span>
                        </div>
                        <div class="item-date">
                            Budget: {{ formatMoney(alert.budget_total) }} • Dépensé: {{ formatMoney(alert.total_reel) }}
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
    
    <!-- Vue.js 3 CDN -->
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
    
    <script>
    const { createApp } = Vue;
    
    createApp({
        data() {
            return {
                loading: true,
                stats: {
                    total_events: 0,
                    events_en_cours: 0,
                    total_tasks: 0,
                    tasks_a_faire: 0,
                    budget_total: 0,
                    budget_count: 0,
                    total_personnel: 0
                },
                upcomingEvents: [],
                urgentTasks: [],
                budgetAlerts: []
            }
        },
        computed: {
            hasData() {
                return this.stats.total_events !== null;
            }
        },
        methods: {
            async fetchData() {
                this.loading = true;
                try {
                    const response = await fetch('/views/api/dashboard_data.php');
                    const data = await response.json();
                    
                    this.stats = data.stats;
                    this.upcomingEvents = data.upcomingEvents;
                    this.urgentTasks = data.urgentTasks;
                    this.budgetAlerts = data.budgetAlerts;
                } catch (error) {
                    console.error('Erreur:', error);
                } finally {
                    this.loading = false;
                }
            },
            refreshData() {
                this.fetchData();
            },
            formatMoney(amount) {
                return new Intl.NumberFormat('fr-FR', {
                    style: 'currency',
                    currency: 'EUR'
                }).format(amount || 0);
            },
            formatDate(date) {
                if (!date) return '-';
                return new Date(date).toLocaleDateString('fr-FR', {
                    day: '2-digit',
                    month: 'long',
                    year: 'numeric'
                });
            },
            getBadgeClass(statut) {
                const mapping = {
                    'en_preparation': 'badge-warning',
                    'en_cours': 'badge-success',
                    'termine': 'badge-success'
                };
                return mapping[statut] || 'badge-warning';
            },
            navigateTo(url) {
                window.location.href = url;
            }
        },
        mounted() {
            this.fetchData();
        }
    }).mount('#app');
    </script>

    <!-- Système de notifications -->
    <script src="/public/js/notifications.js"></script>
</body>
</html>