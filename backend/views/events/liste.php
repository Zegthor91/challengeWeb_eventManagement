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
    <title>Événements - Gestion</title>
    <link rel="stylesheet" href="/public/css/style.css">
    <link rel="stylesheet" href="/public/css/notifications.css">
    <style>
        [v-cloak] { display: none; }
        
        .vue-container {
            padding: 20px;
        }
        
        .controls {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        
        .search-bar {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
            align-items: center;
        }
        
        .search-input-wrapper {
            flex: 1;
            position: relative;
        }
        
        .search-input {
            width: 100%;
            padding: 12px 40px 12px 40px;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 15px;
            transition: all 0.3s;
        }
        
        .search-input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        .search-icon {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #999;
            font-size: 18px;
        }
        
        .clear-btn {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            background: #e74c3c;
            color: white;
            border: none;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            cursor: pointer;
            font-size: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .clear-btn:hover {
            background: #c0392b;
        }
        
        .filters {
            display: flex;
            gap: 10px;
            margin-bottom: 15px;
        }
        
        .filter-chip {
            padding: 8px 16px;
            border: 2px solid #ddd;
            background: white;
            border-radius: 20px;
            cursor: pointer;
            transition: all 0.2s;
            font-size: 13px;
            font-weight: 500;
        }
        
        .filter-chip:hover {
            border-color: #667eea;
            background: #f8f9ff;
        }
        
        .filter-chip.active {
            background: #667eea;
            color: white;
            border-color: #667eea;
        }
        
        .result-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: #666;
            font-size: 14px;
        }
        
        .table-vue {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        .table-vue table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .table-vue th {
            background: #f8f9fa;
            padding: 15px;
            text-align: left;
            font-weight: 600;
            color: #555;
            cursor: pointer;
            user-select: none;
            transition: background 0.2s;
        }
        
        .table-vue th:hover {
            background: #e9ecef;
        }
        
        .table-vue th .sort-icon {
            margin-left: 5px;
            opacity: 0.3;
        }
        
        .table-vue th.sorted .sort-icon {
            opacity: 1;
        }
        
        .table-vue td {
            padding: 15px;
            border-bottom: 1px solid #eee;
        }
        
        .table-vue tbody tr {
            transition: all 0.2s;
        }
        
        .table-vue tbody tr:hover {
            background: #f8f9ff;
        }
        
        .badge-vue {
            padding: 5px 12px;
            border-radius: 15px;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .badge-en_preparation {
            background: #ffeaa7;
            color: #d63031;
        }
        
        .badge-en_cours {
            background: #74b9ff;
            color: #0984e3;
        }
        
        .badge-termine {
            background: #55efc4;
            color: #00b894;
        }
        
        .actions-vue {
            display: flex;
            gap: 5px;
        }
        
        .btn-action {
            padding: 6px 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 11px;
            transition: all 0.2s;
            text-decoration: none;
            display: inline-block;
            color: white;
        }
        
        .btn-voir { background: #3498db; }
        .btn-voir:hover { background: #2980b9; transform: scale(1.05); }
        
        .btn-modifier { background: #2ecc71; }
        .btn-modifier:hover { background: #27ae60; transform: scale(1.05); }
        
        .btn-dupliquer { background: #9b59b6; }
        .btn-dupliquer:hover { background: #8e44ad; transform: scale(1.05); }
        
        .btn-supprimer { background: #e74c3c; }
        .btn-supprimer:hover { background: #c0392b; transform: scale(1.05); }
        
        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            padding: 20px;
            background: white;
            border-radius: 10px;
            margin-top: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .page-btn {
            padding: 8px 16px;
            border: 2px solid #ddd;
            background: white;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.2s;
            font-weight: 500;
        }
        
        .page-btn:hover:not(:disabled) {
            border-color: #667eea;
            background: #667eea;
            color: white;
        }
        
        .page-btn:disabled {
            opacity: 0.3;
            cursor: not-allowed;
        }
        
        .page-info {
            padding: 8px 16px;
            font-weight: 500;
            color: #666;
        }
        
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #999;
        }
        
        .empty-icon {
            font-size: 64px;
            margin-bottom: 15px;
            opacity: 0.3;
        }
        
        .loading {
            text-align: center;
            padding: 40px;
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
    </style>
</head>
<body>
    <div class="container">
        <nav class="sidebar">
            <h2>Gestion Events</h2>
            <ul>
                <li><a href="../dashboard.php">Dashboard</a></li>
                <li><a href="liste.php" class="active">Événements</a></li>
                <li><a href="../carte.php">Carte</a></li>
                <li><a href="../budget/liste.php">Budget</a></li>
                <li><a href="../personnel/liste.php">Personnel</a></li>
                <li><a href="../prestataires/liste.php">Prestataires</a></li>
                <li><a href="../tasks/liste.php">Tâches</a></li>
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
                    <h1>📅 Événements</h1>
                    <a href="ajouter.php" class="btn-primary">+ Nouvel événement</a>
                </div>
                
                <!-- Contrôles -->
                <div class="controls">
                    <!-- Barre de recherche -->
                    <div class="search-bar">
                        <div class="search-input-wrapper">
                            <span class="search-icon">🔍</span>
                            <input 
                                v-model="searchQuery" 
                                type="text" 
                                class="search-input" 
                                placeholder="Rechercher par nom, type, lieu ou responsable...">
                            <button v-if="searchQuery" @click="searchQuery = ''" class="clear-btn">✕</button>
                        </div>
                    </div>
                    
                    <!-- Filtres -->
                    <div class="filters">
                        <div 
                            v-for="filter in filters" 
                            :key="filter.value"
                            @click="currentFilter = filter.value"
                            class="filter-chip"
                            :class="{ active: currentFilter === filter.value }">
                            {{ filter.label }} ({{ getFilterCount(filter.value) }})
                        </div>
                    </div>
                    
                    <!-- Info résultats -->
                    <div class="result-info">
                        <span>{{ filteredEvents.length }} événement(s) trouvé(s)</span>
                        <span>{{ paginatedEvents.length }} affiché(s)</span>
                    </div>
                </div>
                
                <!-- Loading -->
                <div v-if="loading" class="loading">
                    <div class="loading-spinner"></div>
                    <p>Chargement...</p>
                </div>
                
                <!-- Tableau -->
                <div v-if="!loading && paginatedEvents.length > 0" class="table-vue">
                    <table>
                        <thead>
                            <tr>
                                <th @click="sortBy('nom')" :class="{ sorted: sortField === 'nom' }">
                                    Nom <span class="sort-icon">{{ getSortIcon('nom') }}</span>
                                </th>
                                <th @click="sortBy('type_event')" :class="{ sorted: sortField === 'type_event' }">
                                    Type <span class="sort-icon">{{ getSortIcon('type_event') }}</span>
                                </th>
                                <th @click="sortBy('date_debut')" :class="{ sorted: sortField === 'date_debut' }">
                                    Date <span class="sort-icon">{{ getSortIcon('date_debut') }}</span>
                                </th>
                                <th>Lieu</th>
                                <th>Responsable</th>
                                <th @click="sortBy('statut')" :class="{ sorted: sortField === 'statut' }">
                                    Statut <span class="sort-icon">{{ getSortIcon('statut') }}</span>
                                </th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="event in paginatedEvents" :key="event.id">
                                <td><strong>{{ event.nom }}</strong></td>
                                <td>{{ event.type_event }}</td>
                                <td>{{ formatDate(event.date_debut) }}</td>
                                <td>{{ event.lieu }}</td>
                                <td>{{ event.responsable_nom || 'Non assigné' }}</td>
                                <td>
                                    <span class="badge-vue" :class="'badge-' + event.statut">
                                        {{ formatStatut(event.statut) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="actions-vue">
                                        <a :href="'voir.php?id=' + event.id" class="btn-action btn-voir">👁️</a>
                                        <a :href="'modifier.php?id=' + event.id" class="btn-action btn-modifier">✏️</a>
                                        <a :href="'dupliquer.php?id=' + event.id" 
                                           @click="confirmDuplicate($event, event.id)" 
                                           class="btn-action btn-dupliquer">📋</a>
                                        <a :href="'supprimer.php?id=' + event.id" 
                                           @click="confirmDelete($event, event.nom)" 
                                           class="btn-action btn-supprimer">🗑️</a>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <!-- Empty state -->
                <div v-if="!loading && filteredEvents.length === 0" class="empty-state">
                    <div class="empty-icon">🔍</div>
                    <h3>Aucun événement trouvé</h3>
                    <p v-if="searchQuery">Essayez avec d'autres mots-clés</p>
                    <p v-else-if="currentFilter !== 'all'">Aucun événement avec ce statut</p>
                    <p v-else>Créez votre premier événement !</p>
                </div>
                
                <!-- Pagination -->
                <div v-if="!loading && filteredEvents.length > perPage" class="pagination">
                    <button @click="previousPage" :disabled="currentPage === 1" class="page-btn">← Précédent</button>
                    <span class="page-info">Page {{ currentPage }} / {{ totalPages }}</span>
                    <button @click="nextPage" :disabled="currentPage === totalPages" class="page-btn">Suivant →</button>
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
                events: [],
                loading: true,
                searchQuery: '',
                currentFilter: 'all',
                filters: [
                    { value: 'all', label: 'Tous' },
                    { value: 'en_preparation', label: 'En préparation' },
                    { value: 'en_cours', label: 'En cours' },
                    { value: 'termine', label: 'Terminés' }
                ],
                sortField: 'date_debut',
                sortOrder: 'desc',
                currentPage: 1,
                perPage: 10
            }
        },
        computed: {
            filteredEvents() {
                let result = this.events;
                
                // Filtre par statut
                if (this.currentFilter !== 'all') {
                    result = result.filter(e => e.statut === this.currentFilter);
                }
                
                // Recherche
                if (this.searchQuery.trim()) {
                    const query = this.searchQuery.toLowerCase();
                    result = result.filter(e => 
                        (e.nom && e.nom.toLowerCase().includes(query)) ||
                        (e.type_event && e.type_event.toLowerCase().includes(query)) ||
                        (e.lieu && e.lieu.toLowerCase().includes(query)) ||
                        (e.responsable_nom && e.responsable_nom.toLowerCase().includes(query))
                    );
                }
                
                // Tri
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
            },
            paginatedEvents() {
                const start = (this.currentPage - 1) * this.perPage;
                const end = start + this.perPage;
                return this.filteredEvents.slice(start, end);
            },
            totalPages() {
                return Math.ceil(this.filteredEvents.length / this.perPage);
            }
        },
        methods: {
            async fetchEvents() {
                this.loading = true;
                try {
                    const response = await fetch('/views/api/events_data.php');
                    const data = await response.json();
                    this.events = data.events || [];
                } catch (error) {
                    console.error('Erreur:', error);
                    this.events = [];
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
            getFilterCount(filterValue) {
                if (filterValue === 'all') return this.events.length;
                return this.events.filter(e => e.statut === filterValue).length;
            },
            previousPage() {
                if (this.currentPage > 1) this.currentPage--;
            },
            nextPage() {
                if (this.currentPage < this.totalPages) this.currentPage++;
            },
            confirmDelete(event, nom) {
                if (!confirm(`Voulez-vous vraiment supprimer "${nom}" ?`)) {
                    event.preventDefault();
                }
            },
            confirmDuplicate(event, id) {
                if (!confirm('Dupliquer cet événement avec son budget et ses tâches ?')) {
                    event.preventDefault();
                }
            }
        },
        watch: {
            searchQuery() {
                this.currentPage = 1;
            },
            currentFilter() {
                this.currentPage = 1;
            }
        },
        mounted() {
            this.fetchEvents();
        }
    }).mount('#app');
    </script>
    <!-- Système de notifications -->
    <script src="/public/js/notifications.js"></script>
</body>
</html>