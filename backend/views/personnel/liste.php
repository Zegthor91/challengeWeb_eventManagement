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
    <title>Personnel - Gestion</title>
    <link rel="stylesheet" href="/public/css/style.css">
    <link rel="stylesheet" href="/public/css/notifications.css">
    <style>
        [v-cloak] { display: none; }

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
        }

        .clear-btn:hover {
            background: #c0392b;
        }

        .result-info {
            margin-top: 15px;
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

        .btn-modifier { background: #2ecc71; }
        .btn-modifier:hover { background: #27ae60; transform: scale(1.05); }

        .btn-supprimer { background: #e74c3c; }
        .btn-supprimer:hover { background: #c0392b; transform: scale(1.05); }

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
                <li><a href="../events/liste.php">Événements</a></li>
                <li><a href="../carte.php">Carte</a></li>
                <li><a href="../budget/liste.php">Budget</a></li>
                <li><a href="liste.php" class="active">Personnel</a></li>
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
                    <h1>👥 Gestion du Personnel</h1>
                    <a href="ajouter.php" class="btn-primary">+ Ajouter un membre</a>
                </div>

                <!-- Contrôles -->
                <div class="controls">
                    <div class="search-bar">
                        <div class="search-input-wrapper">
                            <span class="search-icon">🔍</span>
                            <input
                                v-model="searchQuery"
                                type="text"
                                class="search-input"
                                placeholder="Rechercher par nom, prénom, email, téléphone ou poste...">
                            <button v-if="searchQuery" @click="searchQuery = ''" class="clear-btn">✕</button>
                        </div>
                    </div>
                    <div class="result-info">
                        {{ filteredPersonnel.length }} membre(s) trouvé(s)
                    </div>
                </div>

                <!-- Loading -->
                <div v-if="loading" class="loading">
                    <div class="loading-spinner"></div>
                    <p>Chargement...</p>
                </div>

                <!-- Tableau -->
                <div v-if="!loading && filteredPersonnel.length > 0" class="table-vue">
                    <table>
                        <thead>
                            <tr>
                                <th @click="sortBy('nom')" :class="{ sorted: sortField === 'nom' }">
                                    Nom <span class="sort-icon">{{ getSortIcon('nom') }}</span>
                                </th>
                                <th @click="sortBy('prenom')" :class="{ sorted: sortField === 'prenom' }">
                                    Prénom <span class="sort-icon">{{ getSortIcon('prenom') }}</span>
                                </th>
                                <th>Email</th>
                                <th>Téléphone</th>
                                <th @click="sortBy('poste')" :class="{ sorted: sortField === 'poste' }">
                                    Poste <span class="sort-icon">{{ getSortIcon('poste') }}</span>
                                </th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="p in filteredPersonnel" :key="p.id">
                                <td><strong>{{ p.nom }}</strong></td>
                                <td>{{ p.prenom }}</td>
                                <td>{{ p.email || '-' }}</td>
                                <td>{{ p.telephone || '-' }}</td>
                                <td>{{ p.poste || '-' }}</td>
                                <td>
                                    <div class="actions-vue">
                                        <a :href="'modifier.php?id=' + p.id" class="btn-action btn-modifier">✏️ Modifier</a>
                                        <a :href="'supprimer.php?id=' + p.id"
                                           @click="confirmDelete($event, p.nom)"
                                           class="btn-action btn-supprimer">🗑️ Supprimer</a>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Empty state -->
                <div v-if="!loading && filteredPersonnel.length === 0" class="empty-state">
                    <div class="empty-icon">👥</div>
                    <h3>Aucun membre trouvé</h3>
                    <p v-if="searchQuery">Essayez avec d'autres mots-clés</p>
                    <p v-else>Ajoutez votre premier membre!</p>
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
                personnel: [],
                loading: true,
                searchQuery: '',
                sortField: 'nom',
                sortOrder: 'asc'
            }
        },
        computed: {
            filteredPersonnel() {
                let result = this.personnel;

                // Recherche
                if (this.searchQuery.trim()) {
                    const query = this.searchQuery.toLowerCase();
                    result = result.filter(p =>
                        (p.nom && p.nom.toLowerCase().includes(query)) ||
                        (p.prenom && p.prenom.toLowerCase().includes(query)) ||
                        (p.email && p.email.toLowerCase().includes(query)) ||
                        (p.telephone && p.telephone.toLowerCase().includes(query)) ||
                        (p.poste && p.poste.toLowerCase().includes(query))
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
            }
        },
        methods: {
            async fetchPersonnel() {
                this.loading = true;
                try {
                    const response = await fetch('/views/api/personnel_data.php');
                    const data = await response.json();
                    this.personnel = data.personnel || [];
                } catch (error) {
                    console.error('Erreur:', error);
                    this.personnel = [];
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
            confirmDelete(event, nom) {
                if (!confirm(`Voulez-vous vraiment supprimer ${nom} ?`)) {
                    event.preventDefault();
                }
            }
        },
        mounted() {
            this.fetchPersonnel();
        }
    }).mount('#app');
    </script>
    <!-- Système de notifications -->
    <script src="/public/js/notifications.js"></script>
</body>
</html>
