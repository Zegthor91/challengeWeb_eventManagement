<?php
require_once '../../config/database.php';
require_once '../../config/session.php';
require_once '../../config/helpers.php';

requireLogin();
$user = getCurrentUser();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Tâches - Gestion</title>
    <link rel="stylesheet" href="/public/css/style.css">
    <link rel="stylesheet" href="/public/css/notifications.css">
    <style>
        [v-cloak] { display: none; }
        .controls { background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 20px; }
        .search-bar { display: flex; gap: 15px; margin-bottom: 20px; align-items: center; }
        .search-input-wrapper { flex: 1; position: relative; }
        .search-input { width: 100%; padding: 12px 40px; border: 2px solid #ddd; border-radius: 8px; font-size: 15px; transition: all 0.3s; }
        .search-input:focus { outline: none; border-color: #667eea; box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1); }
        .search-icon { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #999; font-size: 18px; }
        .clear-btn { position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: #e74c3c; color: white; border: none; border-radius: 50%; width: 24px; height: 24px; cursor: pointer; font-size: 12px; }
        .clear-btn:hover { background: #c0392b; }
        .filters { display: flex; gap: 10px; margin-bottom: 15px; flex-wrap: wrap; }
        .filter-chip { padding: 8px 16px; border: 2px solid #ddd; background: white; border-radius: 20px; cursor: pointer; transition: all 0.2s; font-size: 13px; font-weight: 500; }
        .filter-chip:hover { border-color: #667eea; background: #f8f9ff; }
        .filter-chip.active { background: #667eea; color: white; border-color: #667eea; }
        .result-info { color: #666; font-size: 14px; }
        .table-vue { background: white; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); overflow: hidden; }
        .table-vue table { width: 100%; border-collapse: collapse; }
        .table-vue th { background: #f8f9fa; padding: 15px; text-align: left; font-weight: 600; color: #555; cursor: pointer; user-select: none; transition: background 0.2s; }
        .table-vue th:hover { background: #e9ecef; }
        .table-vue th .sort-icon { margin-left: 5px; opacity: 0.3; }
        .table-vue th.sorted .sort-icon { opacity: 1; }
        .table-vue td { padding: 15px; border-bottom: 1px solid #eee; }
        .table-vue tbody tr { transition: all 0.2s; }
        .table-vue tbody tr:hover { background: #f8f9ff; }
        .badge-vue { padding: 5px 12px; border-radius: 15px; font-size: 11px; font-weight: bold; text-transform: uppercase; }
        .badge-a_faire { background: #ffeaa7; color: #d63031; }
        .badge-en_cours { background: #74b9ff; color: #0984e3; }
        .badge-terminee { background: #55efc4; color: #00b894; }
        .priorite { font-weight: bold; }
        .priorite-basse { color: #95a5a6; }
        .priorite-moyenne { color: #f39c12; }
        .priorite-haute { color: #e74c3c; }
        .actions-vue { display: flex; gap: 5px; }
        .btn-action { padding: 6px 12px; border: none; border-radius: 5px; cursor: pointer; font-size: 11px; transition: all 0.2s; text-decoration: none; display: inline-block; color: white; }
        .btn-modifier { background: #2ecc71; }
        .btn-modifier:hover { background: #27ae60; transform: scale(1.05); }
        .btn-supprimer { background: #e74c3c; }
        .btn-supprimer:hover { background: #c0392b; transform: scale(1.05); }
        .empty-state { text-align: center; padding: 60px 20px; color: #999; }
        .empty-icon { font-size: 64px; margin-bottom: 15px; opacity: 0.3; }
        .loading { text-align: center; padding: 40px; }
        .loading-spinner { border: 4px solid #f3f3f3; border-top: 4px solid #667eea; border-radius: 50%; width: 40px; height: 40px; animation: spin 1s linear infinite; margin: 0 auto 15px; }
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
    </style>
</head>
<body>
    <div class="container">
        <nav class="sidebar">
            <h2>Gestion Events</h2>
            <ul>
                <li><a href="../dashboard.php">Dashboard</a></li>
                <li><a href="../events/liste.php">Événements</a></li>
                <li><a href="../carte.php">Carte</a></li>
                <li><a href="../budget/liste.php">Budget</a></li>
                <li><a href="../personnel/liste.php">Personnel</a></li>
                <li><a href="../prestataires/liste.php">Prestataires</a></li>
                <li><a href="liste.php" class="active">Tâches</a></li>
            </ul>
            <div class="user-info">
                <p><strong><?php echo htmlspecialchars($user['nom']); ?></strong></p>
                <p><?php echo htmlspecialchars($user['role']); ?></p>
                <a href="../logout.php">Déconnexion</a>
            </div>
        </nav>
        <main class="main-content">
            <div id="app" v-cloak>
                <div class="page-header">
                    <h1>✅ Gestion des Tâches</h1>
                    <a href="ajouter.php" class="btn-primary">+ Nouvelle tâche</a>
                </div>
                <div class="controls">
                    <div class="search-bar">
                        <div class="search-input-wrapper">
                            <span class="search-icon">🔍</span>
                            <input v-model="searchQuery" type="text" class="search-input" placeholder="Rechercher par titre, événement ou assigné...">
                            <button v-if="searchQuery" @click="searchQuery = ''" class="clear-btn">✕</button>
                        </div>
                    </div>
                    <div class="filters">
                        <div v-for="filter in statusFilters" :key="filter.value" @click="currentStatusFilter = filter.value" class="filter-chip" :class="{ active: currentStatusFilter === filter.value }">
                            {{ filter.label }} ({{ getStatusCount(filter.value) }})
                        </div>
                    </div>
                    <div class="filters">
                        <div v-for="filter in priorityFilters" :key="filter.value" @click="currentPriorityFilter = filter.value" class="filter-chip" :class="{ active: currentPriorityFilter === filter.value }">
                            {{ filter.label }} ({{ getPriorityCount(filter.value) }})
                        </div>
                    </div>
                    <div class="result-info">{{ filteredTasks.length }} tâche(s) trouvée(s)</div>
                </div>
                <div v-if="loading" class="loading">
                    <div class="loading-spinner"></div>
                    <p>Chargement...</p>
                </div>
                <div v-if="!loading && filteredTasks.length > 0" class="table-vue">
                    <table>
                        <thead>
                            <tr>
                                <th @click="sortBy('titre')" :class="{ sorted: sortField === 'titre' }">Tâche <span class="sort-icon">{{ getSortIcon('titre') }}</span></th>
                                <th>Événement</th>
                                <th @click="sortBy('priorite')" :class="{ sorted: sortField === 'priorite' }">Priorité <span class="sort-icon">{{ getSortIcon('priorite') }}</span></th>
                                <th @click="sortBy('date_limite')" :class="{ sorted: sortField === 'date_limite' }">Date limite <span class="sort-icon">{{ getSortIcon('date_limite') }}</span></th>
                                <th>Assigné à</th>
                                <th @click="sortBy('statut')" :class="{ sorted: sortField === 'statut' }">Statut <span class="sort-icon">{{ getSortIcon('statut') }}</span></th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="task in filteredTasks" :key="task.id">
                                <td><strong>{{ task.titre }}</strong></td>
                                <td>{{ task.event_nom || 'N/A' }}</td>
                                <td><span class="priorite" :class="'priorite-' + task.priorite">{{ formatPriorite(task.priorite) }}</span></td>
                                <td>{{ formatDate(task.date_limite) }}</td>
                                <td>{{ task.assigne_nom || 'Non assigné' }}</td>
                                <td><span class="badge-vue" :class="'badge-' + task.statut">{{ formatStatut(task.statut) }}</span></td>
                                <td>
                                    <div class="actions-vue">
                                        <a :href="'modifier.php?id=' + task.id" class="btn-action btn-modifier">✏️</a>
                                        <a :href="'supprimer.php?id=' + task.id" @click="confirmDelete($event, task.titre)" class="btn-action btn-supprimer">🗑️</a>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div v-if="!loading && filteredTasks.length === 0" class="empty-state">
                    <div class="empty-icon">✅</div>
                    <h3>Aucune tâche trouvée</h3>
                    <p v-if="searchQuery">Essayez avec d'autres mots-clés</p>
                    <p v-else-if="currentStatusFilter !== 'all' || currentPriorityFilter !== 'all'">Aucune tâche avec ces filtres</p>
                    <p v-else>Créez votre première tâche!</p>
                </div>
            </div>
        </main>
    </div>
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
    <script>
    const { createApp } = Vue;
    createApp({
        data() {
            return {
                tasks: [],
                loading: true,
                searchQuery: '',
                currentStatusFilter: 'all',
                currentPriorityFilter: 'all',
                statusFilters: [
                    { value: 'all', label: 'Toutes' },
                    { value: 'a_faire', label: 'À faire' },
                    { value: 'en_cours', label: 'En cours' },
                    { value: 'termine', label: 'Terminées' }
                ],
                priorityFilters: [
                    { value: 'all', label: 'Toutes priorités' },
                    { value: 'haute', label: 'Haute' },
                    { value: 'moyenne', label: 'Moyenne' },
                    { value: 'basse', label: 'Basse' }
                ],
                sortField: 'date_limite',
                sortOrder: 'desc'
            }
        },
        computed: {
            filteredTasks() {
                let result = this.tasks;
                if (this.currentStatusFilter !== 'all') {
                    result = result.filter(t => t.statut === this.currentStatusFilter);
                }
                if (this.currentPriorityFilter !== 'all') {
                    result = result.filter(t => t.priorite === this.currentPriorityFilter);
                }
                if (this.searchQuery.trim()) {
                    const query = this.searchQuery.toLowerCase();
                    result = result.filter(t =>
                        (t.titre && t.titre.toLowerCase().includes(query)) ||
                        (t.event_nom && t.event_nom.toLowerCase().includes(query)) ||
                        (t.assigne_nom && t.assigne_nom.toLowerCase().includes(query))
                    );
                }
                result.sort((a, b) => {
                    let aVal = a[this.sortField] || '';
                    let bVal = b[this.sortField] || '';
                    if (this.sortOrder === 'asc') {
                        return aVal > bVal ? 1 : -1;
                    } else {
                        return aVal < bVal ? 1 : -1;
                    }
                });
                return result;
            }
        },
        methods: {
            async fetchTasks() {
                this.loading = true;
                try {
                    const response = await fetch('/views/api/tasks_data.php');
                    const data = await response.json();
                    this.tasks = data.tasks || [];
                } catch (error) {
                    console.error('Erreur:', error);
                    this.tasks = [];
                } finally {
                    this.loading = false;
                }
            },
            sortBy(field) {
                if (this.sortField === field) {
                    this.sortOrder = this.sortOrder === 'asc' ? 'desc' : 'asc';
                } else {
                    this.sortField = field;
                    this.sortOrder = 'asc';
                }
            },
            getSortIcon(field) {
                if (this.sortField !== field) return '↕';
                return this.sortOrder === 'asc' ? '↑' : '↓';
            },
            formatDate(date) {
                if (!date) return '-';
                return new Date(date).toLocaleDateString('fr-FR', {
                    day: '2-digit',
                    month: 'short',
                    year: 'numeric'
                });
            },
            formatStatut(statut) {
                return statut.replace('_', ' ');
            },
            formatPriorite(priorite) {
                return priorite ? priorite.charAt(0).toUpperCase() + priorite.slice(1) : 'Moyenne';
            },
            getStatusCount(status) {
                if (status === 'all') return this.tasks.length;
                return this.tasks.filter(t => t.statut === status).length;
            },
            getPriorityCount(priority) {
                if (priority === 'all') return this.tasks.length;
                return this.tasks.filter(t => t.priorite === priority).length;
            },
            confirmDelete(event, titre) {
                if (!confirm(`Supprimer la tâche "${titre}" ?`)) {
                    event.preventDefault();
                }
            }
        },
        mounted() {
            this.fetchTasks();
        }
    }).mount('#app');
    </script>
    <!-- Système de notifications -->
    <script src="/public/js/notifications.js"></script>
</body>
</html>
