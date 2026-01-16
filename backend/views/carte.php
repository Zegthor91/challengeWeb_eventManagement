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
    <title>Carte des Événements - Gestion</title>
    <link rel="stylesheet" href="/public/css/style.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        [v-cloak] { display: none; }

        #map {
            height: 500px;
            width: 100%;
            border-radius: 10px;
            border: 1px solid #ddd;
        }

        .map-container {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }

        .map-controls {
            display: flex;
            gap: 10px;
            margin-bottom: 15px;
            flex-wrap: wrap;
        }

        .filter-map-btn {
            padding: 8px 16px;
            background: white;
            color: #333;
            border: 1px solid #ddd;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }

        .filter-map-btn:hover {
            background: #f5f5f5;
        }

        .filter-map-btn.active {
            background: #2c3e50;
            color: white;
            border-color: #2c3e50;
        }

        .legend {
            background: white;
            padding: 15px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            margin-top: 20px;
        }

        .legend h3 {
            margin-bottom: 10px;
            font-size: 14px;
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 8px;
            font-size: 13px;
        }

        .legend-icon {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
        }

        .icon-en_preparation { background: #ffeaa7; }
        .icon-en_cours { background: #74b9ff; }
        .icon-termine { background: #55efc4; }

        .stats-map {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            margin-bottom: 20px;
        }

        .stat-map-card {
            background: white;
            padding: 15px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            text-align: center;
        }

        .stat-map-card .number {
            font-size: 28px;
            font-weight: bold;
            color: #667eea;
        }

        .stat-map-card .label {
            font-size: 12px;
            color: #666;
            text-transform: uppercase;
        }

        .loading-map {
            text-align: center;
            padding: 40px;
        }

        .loading-spinner-map {
            border: 3px solid #eee;
            border-top: 3px solid #667eea;
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

        .leaflet-popup-content-wrapper {
            border-radius: 8px;
        }

        .popup-title {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 8px;
        }

        .popup-info {
            font-size: 12px;
            color: #666;
            margin-bottom: 4px;
        }

        .popup-link {
            display: inline-block;
            margin-top: 8px;
            padding: 6px 12px;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 12px;
        }

        .popup-link:hover {
            background: #5568d3;
        }
    </style>
</head>
<body>
    <div class="container">
        <nav class="sidebar">
            <h2>Gestion Events</h2>
            <ul>
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="events/liste.php">Événements</a></li>
                <li><a href="carte.php" class="active">Carte</a></li>
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
                    <h1>Carte des Événements</h1>
                </div>

                <!-- Stats -->
                <div class="stats-map">
                    <div class="stat-map-card">
                        <div class="number">{{ totalEvents }}</div>
                        <div class="label">Événements</div>
                    </div>
                    <div class="stat-map-card">
                        <div class="number">{{ eventsWithLocation }}</div>
                        <div class="label">Localisés</div>
                    </div>
                    <div class="stat-map-card">
                        <div class="number">{{ visibleEvents }}</div>
                        <div class="label">Affichés</div>
                    </div>
                </div>

                <!-- Loading -->
                <div v-if="loading" class="loading-map">
                    <div class="loading-spinner-map"></div>
                    <p>{{ loadingMessage }}</p>
                </div>

                <!-- Carte -->
                <div v-show="!loading" class="map-container">
                    <div class="map-controls">
                        <button
                            v-for="filter in filters"
                            :key="filter.value"
                            @click="setFilter(filter.value)"
                            class="filter-map-btn"
                            :class="{ active: currentFilter === filter.value }">
                            {{ filter.label }} ({{ getFilterCount(filter.value) }})
                        </button>
                    </div>
                    <div id="map"></div>
                </div>

                <!-- Légende -->
                <div v-show="!loading" class="legend">
                    <h3>Légende</h3>
                    <div class="legend-item">
                        <div class="legend-icon icon-en_preparation">📋</div>
                        <span>En préparation</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-icon icon-en_cours">🎯</div>
                        <span>En cours</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-icon icon-termine">✅</div>
                        <span>Terminés</span>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
    <script>
    const { createApp } = Vue;

    createApp({
        data() {
            return {
                events: [],
                loading: true,
                map: null,
                markers: [],
                currentFilter: 'all',
                filters: [
                    { value: 'all', label: 'Tous' },
                    { value: 'en_preparation', label: 'En préparation' },
                    { value: 'en_cours', label: 'En cours' },
                    { value: 'termine', label: 'Terminés' }
                ],
                geocodeCache: {},
                loadingMessage: 'Chargement...',
                mapInitialized: false,
                cityCoordinates: {
                    'paris': [48.8566, 2.3522],
                    'marseille': [43.2965, 5.3698],
                    'lyon': [45.7640, 4.8357],
                    'toulouse': [43.6047, 1.4442],
                    'nice': [43.7102, 7.2620],
                    'nantes': [47.2184, -1.5536],
                    'strasbourg': [48.5734, 7.7521],
                    'montpellier': [43.6108, 3.8767],
                    'bordeaux': [44.8378, -0.5792],
                    'lille': [50.6292, 3.0573]
                }
            }
        },
        computed: {
            totalEvents() {
                return this.events.length;
            },
            eventsWithLocation() {
                return this.events.filter(e => e.lieu && e.lieu.trim() !== '').length;
            },
            filteredEvents() {
                if (this.currentFilter === 'all') {
                    return this.events.filter(e => e.lieu && e.lieu.trim() !== '');
                }
                return this.events.filter(e => e.statut === this.currentFilter && e.lieu && e.lieu.trim() !== '');
            },
            visibleEvents() {
                return this.filteredEvents.length;
            }
        },
        methods: {
            async fetchEvents() {
                this.loadingMessage = 'Chargement des événements...';
                try {
                    const response = await fetch('/views/api/events_map_data.php');
                    if (!response.ok) throw new Error('Erreur réseau');
                    const data = await response.json();
                    this.events = data.events || [];
                    console.log('Événements chargés:', this.events.length);
                } catch (error) {
                    console.error('Erreur:', error);
                    this.events = [];
                }

                // Initialiser la carte après chargement des données
                this.loading = false;
                await this.$nextTick();
                this.initMap();
            },
            initMap() {
                if (this.mapInitialized) return;

                const mapElement = document.getElementById('map');
                if (!mapElement) {
                    console.error('Element #map non trouvé');
                    return;
                }

                console.log('Initialisation de la carte...');
                this.map = L.map('map').setView([46.603354, 1.888334], 6);

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© OpenStreetMap'
                }).addTo(this.map);

                this.mapInitialized = true;
                console.log('Carte initialisée');

                // Forcer le redimensionnement
                setTimeout(() => {
                    this.map.invalidateSize();
                    this.updateMarkers();
                }, 100);
            },
            setFilter(value) {
                this.currentFilter = value;
                this.updateMarkers();
            },
            async updateMarkers() {
                if (!this.map) return;

                // Supprimer les anciens marqueurs
                this.markers.forEach(marker => marker.remove());
                this.markers = [];

                const eventsToShow = this.filteredEvents;
                console.log('Événements à afficher:', eventsToShow.length);

                for (const event of eventsToShow) {
                    const coords = this.getCoordinates(event.lieu);
                    if (coords) {
                        const marker = this.createMarker(event, coords);
                        this.markers.push(marker);
                    }
                }

                // Ajuster la vue
                if (this.markers.length > 0) {
                    const group = L.featureGroup(this.markers);
                    this.map.fitBounds(group.getBounds().pad(0.1));
                }
            },
            getCoordinates(lieu) {
                if (!lieu) return null;
                const lieuLower = lieu.toLowerCase();
                for (const [city, coords] of Object.entries(this.cityCoordinates)) {
                    if (lieuLower.includes(city)) {
                        return coords;
                    }
                }
                // Position par défaut (Paris) si ville non trouvée
                return [48.8566, 2.3522];
            },
            createMarker(event, coords) {
                const colors = {
                    'en_preparation': '#f59e0b',
                    'en_cours': '#3b82f6',
                    'termine': '#10b981'
                };

                const emojis = {
                    'en_preparation': '📋',
                    'en_cours': '🎯',
                    'termine': '✅'
                };

                const icon = L.divIcon({
                    className: 'custom-marker',
                    html: `<div style="
                        background: ${colors[event.statut] || '#667eea'};
                        width: 30px;
                        height: 30px;
                        border-radius: 50%;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        font-size: 14px;
                        border: 2px solid white;
                        box-shadow: 0 2px 5px rgba(0,0,0,0.3);
                    ">${emojis[event.statut] || '📍'}</div>`,
                    iconSize: [30, 30],
                    iconAnchor: [15, 15]
                });

                const marker = L.marker(coords, { icon }).addTo(this.map);

                marker.bindPopup(`
                    <div class="popup-title">${event.nom}</div>
                    <div class="popup-info">📍 ${event.lieu}</div>
                    <div class="popup-info">📅 ${this.formatDate(event.date_debut)}</div>
                    <a href="events/voir.php?id=${event.id}" class="popup-link">Voir détails</a>
                `);

                return marker;
            },
            getFilterCount(filterValue) {
                if (filterValue === 'all') return this.events.length;
                return this.events.filter(e => e.statut === filterValue).length;
            },
            formatDate(date) {
                if (!date) return '-';
                return new Date(date).toLocaleDateString('fr-FR');
            }
        },
        mounted() {
            this.fetchEvents();
        }
    }).mount('#app');
    </script>
</body>
</html>
