<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Stats Overview -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <x-filament::card>
                <div class="text-center">
                    <div class="text-3xl font-bold text-gray-900">{{ number_format($stats['total_scans']) }}</div>
                    <div class="text-sm text-gray-600">Total Scans</div>
                </div>
            </x-filament::card>

            <x-filament::card>
                <div class="text-center">
                    <div class="text-3xl font-bold text-blue-600">{{ number_format($stats['scans_with_location']) }}</div>
                    <div class="text-sm text-gray-600">With Location</div>
                </div>
            </x-filament::card>

            <x-filament::card>
                <div class="text-center">
                    <div class="text-3xl font-bold text-green-600">{{ $stats['unique_locations'] }}</div>
                    <div class="text-sm text-gray-600">Unique Locations</div>
                </div>
            </x-filament::card>

            <x-filament::card>
                <div class="text-center">
                    <div class="text-3xl font-bold text-purple-600">{{ $stats['location_capture_rate'] }}%</div>
                    <div class="text-sm text-gray-600">Capture Rate</div>
                </div>
            </x-filament::card>
        </div>

        <!-- Top Locations -->
        <x-filament::card>
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Top 10 Locations by Scan Volume</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rank</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Property</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Scans</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($stats['top_locations'] as $index => $location)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $index + 1 }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $location['location'] }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $location['property'] }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-blue-600">{{ $location['scans'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-filament::card>

        <!-- Google Maps Heatmap -->
        <x-filament::card>
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Scan Location Heatmap</h3>
                <div class="flex gap-2">
                    <button onclick="toggleHeatmap()" class="px-3 py-1 text-sm bg-blue-600 text-white rounded hover:bg-blue-700">
                        Toggle Heatmap
                    </button>
                    <button onclick="toggleMarkers()" class="px-3 py-1 text-sm bg-green-600 text-white rounded hover:bg-green-700">
                        Toggle Markers
                    </button>
                </div>
            </div>

            @if(empty($googleMapsApiKey) || $googleMapsApiKey === 'your_google_maps_api_key_here')
                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4">
                    <p class="text-sm text-yellow-700">
                        <strong>Google Maps API Key Required:</strong> Please add your API key to <code>.env</code> as <code>GOOGLE_MAPS_API_KEY</code>.
                        Get your key from: <a href="https://console.cloud.google.com/google/maps-apis" target="_blank" class="underline">Google Cloud Console</a>
                    </p>
                </div>
                <div id="map" style="height: 600px; background: #e5e7eb;" class="rounded mt-4 flex items-center justify-center">
                    <p class="text-gray-500">Map preview unavailable - API key required</p>
                </div>
            @else
                <div id="map" style="height: 600px;" class="rounded"></div>
            @endif
        </x-filament::card>

        <!-- Legend -->
        <x-filament::card>
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Legend</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="flex items-center gap-2">
                    <div class="w-4 h-4 bg-blue-500 rounded-full"></div>
                    <span class="text-sm text-gray-700">QR Code Scans</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-4 h-4 bg-green-500 rounded-full"></div>
                    <span class="text-sm text-gray-700">Correct Submissions</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-4 h-4 bg-yellow-500 rounded-full"></div>
                    <span class="text-sm text-gray-700">Winners</span>
                </div>
            </div>
        </x-filament::card>
    </div>

    @if(!empty($googleMapsApiKey) && $googleMapsApiKey !== 'your_google_maps_api_key_here')
        <script src="https://maps.googleapis.com/maps/api/js?key={{ $googleMapsApiKey }}&libraries=visualization&callback=initMap" async defer></script>
        <script>
            let map, heatmap;
            let markers = [];
            let showHeatmap = true;
            let showMarkers = false;

            const scanLocations = @json($scanLocations);
            const submissionLocations = @json($submissionLocations);

            function initMap() {
                // Default center (USA center, will adjust to data bounds)
                const center = { lat: 39.8283, lng: -98.5795 };

                map = new google.maps.Map(document.getElementById('map'), {
                    zoom: 4,
                    center: center,
                    mapTypeId: 'roadmap',
                });

                // Create heatmap data
                const heatmapData = scanLocations.map(location => ({
                    location: new google.maps.LatLng(location.lat, location.lng),
                    weight: 1
                }));

                // Add submission heatmap data
                submissionLocations.forEach(submission => {
                    heatmapData.push({
                        location: new google.maps.LatLng(submission.lat, submission.lng),
                        weight: submission.winner ? 3 : submission.correct ? 2 : 1
                    });
                });

                // Create heatmap layer
                heatmap = new google.maps.visualization.HeatmapLayer({
                    data: heatmapData,
                    radius: 20,
                    opacity: 0.6
                });

                heatmap.setMap(map);

                // Fit bounds to show all markers
                if (heatmapData.length > 0) {
                    const bounds = new google.maps.LatLngBounds();
                    heatmapData.forEach(point => {
                        bounds.extend(point.location);
                    });
                    map.fitBounds(bounds);
                }

                // Create markers (hidden by default)
                createMarkers();
            }

            function createMarkers() {
                // Clear existing markers
                markers.forEach(marker => marker.setMap(null));
                markers = [];

                // Add scan markers (blue)
                scanLocations.forEach(scan => {
                    const marker = new google.maps.Marker({
                        position: { lat: scan.lat, lng: scan.lng },
                        map: showMarkers ? map : null,
                        title: `${scan.location} - ${scan.property}`,
                        icon: {
                            path: google.maps.SymbolPath.CIRCLE,
                            scale: 6,
                            fillColor: '#3B82F6',
                            fillOpacity: 0.8,
                            strokeColor: '#1E40AF',
                            strokeWeight: 1
                        }
                    });

                    const infoWindow = new google.maps.InfoWindow({
                        content: `
                            <div style="padding: 8px;">
                                <strong>${scan.location}</strong><br>
                                <em>${scan.property}</em><br>
                                <small>Scanned: ${scan.time}</small>
                            </div>
                        `
                    });

                    marker.addListener('click', () => {
                        infoWindow.open(map, marker);
                    });

                    markers.push(marker);
                });

                // Add submission markers
                submissionLocations.forEach(submission => {
                    const color = submission.winner ? '#EAB308' : submission.correct ? '#10B981' : '#EF4444';
                    const strokeColor = submission.winner ? '#CA8A04' : submission.correct ? '#059669' : '#B91C1C';

                    const marker = new google.maps.Marker({
                        position: { lat: submission.lat, lng: submission.lng },
                        map: showMarkers ? map : null,
                        title: submission.winner ? 'Winner!' : submission.correct ? 'Correct Answer' : 'Incorrect Answer',
                        icon: {
                            path: google.maps.SymbolPath.CIRCLE,
                            scale: 6,
                            fillColor: color,
                            fillOpacity: 0.8,
                            strokeColor: strokeColor,
                            strokeWeight: 1
                        }
                    });

                    const infoWindow = new google.maps.InfoWindow({
                        content: `
                            <div style="padding: 8px;">
                                <strong>${submission.winner ? '🏆 Winner!' : submission.correct ? '✓ Correct' : '✗ Incorrect'}</strong><br>
                                <small>Submitted: ${submission.time}</small>
                            </div>
                        `
                    });

                    marker.addListener('click', () => {
                        infoWindow.open(map, marker);
                    });

                    markers.push(marker);
                });
            }

            function toggleHeatmap() {
                showHeatmap = !showHeatmap;
                heatmap.setMap(showHeatmap ? map : null);
            }

            function toggleMarkers() {
                showMarkers = !showMarkers;
                markers.forEach(marker => {
                    marker.setMap(showMarkers ? map : null);
                });
            }

            // Initialize map when script loads
            window.initMap = initMap;
        </script>
    @endif
</x-filament-panels::page>
