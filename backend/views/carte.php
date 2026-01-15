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
    <link rel="stylesheet" href="/public/css/notifications.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        [v-cloak] { display: none; }

        #map {
            height: calc(100vh - 200px);
            min-height: 500px;
            border-radius: 16px;
            box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1);
            border: 1px solid var(--gray-100);
        }

        .map-container {
            background: white;
            padding: 25px;
            border-radius: 16px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }

        .map-controls {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .filter-map-btn {
            padding: 10px 20px;
            background: white;
            color: var(--gray-700);
            border: 2px solid var(--gray-200);
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s;
            font-weight: 600;
            font-size: 14px;
        }

        .filter-map-btn:hover {
            border-color: var(--primary);
            color: var(--primary);
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        .filter-map-btn.active {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            border-color: transparent;
            box-shadow: var(--shadow-md);
        }

        .legend {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: var(--shadow);
            margin-top: 20px;
            border: 1px solid var(--gray-100);
        }

        .legend h3 {
            margin-bottom: 15px;
            font-size: 16px;
            font-weight: 700;
            color: var(--gray-900);
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 10px;
        }

        .legend-icon {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            font-weight: bold;
            color: white;
        }

        .icon-en_preparation { background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%); }
        .icon-en_cours { background: linear-gradient(135deg, #60a5fa 0%, #3b82f6 100%); }
        .icon-termine { background: linear-gradient(135deg, #34d399 0%, #10b981 100%); }

        .stats-map {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 20px;
            margin-bottom: 25px;
        }

        .stat-map-card {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: var(--shadow);
            text-align: center;
            border: 1px solid var(--gray-100);
            transition: var(--transition);
        }

        .stat-map-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-lg);
        }

        .stat-map-card .number {
            font-size: 32px;
            font-weight: 800;
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 5px;
        }

        .stat-map-card .label {
            font-size: 13px;
            color: var(--gray-600);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .loading-map {
            text-align: center;
            padding: 60px 20px;
        }

        .loading-spinner-map {
            border: 4px solid var(--gray-200);
            border-top: 4px solid var(--primary);
            border-radius: 50%;
            width: 50px;
            height: 50px;
            animation: spin 1s linear infinite;
            margin: 0 auto 20px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Custom Leaflet Popup */
        .leaflet-popup-content-wrapper {
            border-radius: 12px !important;
            box-shadow: var(--shadow-lg) !important;
        }

        .leaflet-popup-content {
            margin: 16px !important;
            font-family: inherit !important;
        }

        .popup-title {
            font-size: 16px;
            font-weight: 700;
            color: var(--gray-900);
            margin-bottom: 10px;
        }

        .popup-info {
            font-size: 13px;
            color: var(--gray-600);
            margin-bottom: 6px;
        }

        .popup-badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            margin-top: 8px;
        }

        .popup-link {
            display: inline-block;
            margin-top: 10px;
            padding: 8px 16px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            transition: var(--transition);
        }

        .popup-link:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
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
                    <h1>🗺️ Carte des Événements</h1>
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
                    <p style="color: var(--gray-600); font-weight: 600;">{{ loadingMessage }}</p>
                </div>

                <!-- Carte -->
                <div v-if="!loading" class="map-container">
                    <div class="map-controls">
                        <button
                            v-for="filter in filters"
                            :key="filter.value"
                            @click="currentFilter = filter.value"
                            class="filter-map-btn"
                            :class="{ active: currentFilter === filter.value }">
                            {{ filter.label }} ({{ getFilterCount(filter.value) }})
                        </button>
                    </div>
                    <div id="map"></div>
                </div>

                <!-- Légende -->
                <div v-if="!loading" class="legend">
                    <h3>📍 Légende</h3>
                    <div class="legend-item">
                        <div class="legend-icon icon-en_preparation">📋</div>
                        <span><strong>En préparation</strong> - Événements en cours de planification</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-icon icon-en_cours">🎯</div>
                        <span><strong>En cours</strong> - Événements actuellement en déroulement</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-icon icon-termine">✅</div>
                        <span><strong>Terminés</strong> - Événements complétés</span>
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
                loadingMessage: 'Chargement de la carte...',
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
                    'lille': [50.6292, 3.0573],
                    'rennes': [48.1173, -1.6778],
                    'reims': [49.2583, 4.0317],
                    'le havre': [49.4944, 0.1079],
                    'saint-étienne': [45.4397, 4.3872],
                    'toulon': [43.1242, 5.9280],
                    'grenoble': [45.1885, 5.7245],
                    'dijon': [47.3220, 5.0415],
                    'angers': [47.4784, -0.5632],
                    'nîmes': [43.8367, 4.3601],
                    'villeurbanne': [45.7660, 4.8795],
                    'clermont-ferrand': [45.7772, 3.0870],
                    'le mans': [48.0077, 0.1984],
                    'aix-en-provence': [43.5297, 5.4474],
                    'brest': [48.3904, -4.4861],
                    'tours': [47.3941, 0.6848],
                    'amiens': [49.8942, 2.2957],
                    'limoges': [45.8336, 1.2611],
                    'annecy': [45.8992, 6.1294],
                    'perpignan': [42.6886, 2.8948],
                    'besançon': [47.2380, 6.0243]
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
                    return this.events;
                }
                return this.events.filter(e => e.statut === this.currentFilter);
            },
            visibleEvents() {
                return this.filteredEvents.length;
            }
        },
        methods: {
            async fetchEvents() {
                this.loading = true;
                try {
                    const response = await fetch('/views/api/events_map_data.php');
                    const data = await response.json();
                    this.events = data.events || [];
                    await this.$nextTick();
                    this.initMap();
                } catch (error) {
                    console.error('Erreur:', error);
                    this.events = [];
                } finally {
                    this.loading = false;
                }
            },
            initMap() {
                // Centre de la France par défaut
                this.map = L.map('map').setView([46.603354, 1.888334], 6);

                // Tile layer avec style moderne
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© OpenStreetMap contributors',
                    maxZoom: 19
                }).addTo(this.map);

                this.updateMarkers();
            },
            async updateMarkers() {
                // Supprimer les anciens marqueurs
                this.markers.forEach(marker => marker.remove());
                this.markers = [];

                const bounds = [];
                const eventsToGeocode = this.filteredEvents.filter(e => e.lieu);

                for (let i = 0; i < eventsToGeocode.length; i++) {
                    const event = eventsToGeocode[i];

                    // Mise à jour du message de progression
                    this.loadingMessage = `Localisation des événements... (${i + 1}/${eventsToGeocode.length})`;

                    try {
                        // Géocodage avec Nominatim (OpenStreetMap)
                        const coords = await this.geocode(event.lieu);
                        if (coords) {
                            const marker = this.createMarker(event, coords);
                            this.markers.push(marker);
                            bounds.push(coords);
                        }

                        // Délai de 1 seconde entre chaque requête API (pas pour le cache local)
                        if (i < eventsToGeocode.length - 1 && !this.geocodeCache[event.lieu]) {
                            await new Promise(resolve => setTimeout(resolve, 1000));
                        }
                    } catch (error) {
                        console.error(`Erreur géocodage pour ${event.lieu}:`, error);
                    }
                }

                // Réinitialiser le message
                this.loadingMessage = 'Chargement de la carte...';

                // Ajuster la vue pour afficher tous les marqueurs
                if (bounds.length > 0) {
                    this.map.fitBounds(bounds, { padding: [50, 50] });
                }
            },
            async geocode(address) {
                // Vérifier le cache d'abord
                if (this.geocodeCache[address]) {
                    return this.geocodeCache[address];
                }

                // Recherche rapide dans les villes pré-définies
                const addressLower = address.toLowerCase().trim();
                for (const [city, coords] of Object.entries(this.cityCoordinates)) {
                    if (addressLower.includes(city)) {
                        console.log(`Ville trouvée en cache: ${city}`);
                        this.geocodeCache[address] = coords;
                        return coords;
                    }
                }

                // Si pas trouvé, utiliser l'API Nominatim
                try {
                    console.log(`Géocodage API pour: ${address}`);
                    const response = await fetch(
                        `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(address)}, France&limit=1`,
                        {
                            headers: {
                                'User-Agent': 'GestionEvents/1.0'
                            }
                        }
                    );

                    if (!response.ok) {
                        console.error(`Erreur HTTP: ${response.status}`);
                        return null;
                    }

                    const data = await response.json();
                    if (data && data.length > 0) {
                        const coords = [parseFloat(data[0].lat), parseFloat(data[0].lon)];
                        this.geocodeCache[address] = coords;
                        console.log(`Coordonnées trouvées: ${coords}`);
                        return coords;
                    }

                    console.warn(`Aucune coordonnée trouvée pour: ${address}`);
                    this.geocodeCache[address] = null;
                    return null;
                } catch (error) {
                    console.error('Erreur géocodage:', error);
                    return null;
                }
            },
            createMarker(event, coords) {
                const iconColors = {
                    'en_preparation': '#f59e0b',
                    'en_cours': '#3b82f6',
                    'termine': '#10b981'
                };

                const iconEmojis = {
                    'en_preparation': '📋',
                    'en_cours': '🎯',
                    'termine': '✅'
                };

                const icon = L.divIcon({
                    className: 'custom-marker',
                    html: `<div style="
                        background: ${iconColors[event.statut] || '#667eea'};
                        width: 32px;
                        height: 32px;
                        border-radius: 50%;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        font-size: 16px;
                        box-shadow: 0 4px 6px rgba(0,0,0,0.2);
                        border: 3px solid white;
                    ">${iconEmojis[event.statut] || '📍'}</div>`,
                    iconSize: [32, 32],
                    iconAnchor: [16, 16]
                });

                const marker = L.marker(coords, { icon }).addTo(this.map);

                const popupContent = `
                    <div class="popup-title">${this.escapeHtml(event.nom)}</div>
                    <div class="popup-info">📍 ${this.escapeHtml(event.lieu)}</div>
                    <div class="popup-info">📅 ${this.formatDate(event.date_debut)}</div>
                    ${event.type_event ? `<div class="popup-info">🎭 ${this.escapeHtml(event.type_event)}</div>` : ''}
                    ${event.responsable_nom ? `<div class="popup-info">👤 ${this.escapeHtml(event.responsable_nom)}</div>` : ''}
                    <span class="popup-badge badge-${event.statut}">${this.formatStatut(event.statut)}</span>
                    <br>
                    <a href="events/voir.php?id=${event.id}" class="popup-link">Voir les détails →</a>
                `;

                marker.bindPopup(popupContent, {
                    maxWidth: 300,
                    className: 'custom-popup'
                });

                return marker;
            },
            getFilterCount(filterValue) {
                if (filterValue === 'all') return this.events.length;
                return this.events.filter(e => e.statut === filterValue).length;
            },
            formatDate(date) {
                if (!date) return '-';
                return new Date(date).toLocaleDateString('fr-FR', {
                    day: '2-digit',
                    month: 'long',
                    year: 'numeric'
                });
            },
            formatStatut(statut) {
                return statut.replace('_', ' ');
            },
            escapeHtml(text) {
                const div = document.createElement('div');
                div.textContent = text;
                return div.innerHTML;
            }
        },
        watch: {
            currentFilter() {
                if (this.map) {
                    this.updateMarkers();
                }
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
