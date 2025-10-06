<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Edit Ad Box
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route('admin.ad-boxes.update', $adBox) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Title</label>
                            <input type="text" name="title" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('title') border-red-500 @enderror" value="{{ old('title', $adBox->title) }}">
                            @error('title')
                            <p class="text-red-500 text-xs italic">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">URL</label>
                            <input type="url" name="url" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('url') border-red-500 @enderror" value="{{ old('url', $adBox->url) }}" placeholder="https://example.com">
                            @error('url')
                            <p class="text-red-500 text-xs italic">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">HTML Content</label>
                            <textarea name="html_content" rows="6" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline font-mono text-sm @error('html_content') border-red-500 @enderror">{{ old('html_content', $adBox->html_content) }}</textarea>
                            <p class="text-gray-600 text-xs mt-1">Enter the HTML/text content for your ad</p>
                            @error('html_content')
                            <p class="text-red-500 text-xs italic">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="flex items-center">
                                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $adBox->is_active) ? 'checked' : '' }} class="rounded">
                                <span class="ml-2 text-gray-700">Active</span>
                            </label>
                        </div>

                        <div class="mb-4">
                            <label class="flex items-center">
                                <input type="checkbox" name="is_national" value="1" id="is_national" onchange="toggleLocationFields()" {{ old('is_national', $adBox->is_national) ? 'checked' : '' }} class="rounded">
                                <span class="ml-2 text-gray-700">National Ad (shows everywhere)</span>
                            </label>
                        </div>

                        <div id="location-fields" class="{{ old('is_national', $adBox->is_national) ? 'hidden' : '' }}">
                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-bold mb-2">Location Name</label>
                                <input type="text" name="location_name" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" value="{{ old('location_name', $adBox->location_name) }}" placeholder="e.g., New York, NY">
                            </div>

                            <div class="grid grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label class="block text-gray-700 text-sm font-bold mb-2">Latitude</label>
                                    <input type="number" step="0.00000001" name="latitude" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" value="{{ old('latitude', $adBox->latitude) }}" placeholder="40.7128">
                                </div>
                                <div>
                                    <label class="block text-gray-700 text-sm font-bold mb-2">Longitude</label>
                                    <input type="number" step="0.00000001" name="longitude" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" value="{{ old('longitude', $adBox->longitude) }}" placeholder="-74.0060">
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-bold mb-2">Radius (miles)</label>
                                <input type="number" name="radius_miles" min="1" max="500" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" value="{{ old('radius_miles', $adBox->radius_miles) }}">
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Rotation Order</label>
                            <input type="number" name="rotation_order" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" value="{{ old('rotation_order', $adBox->rotation_order) }}">
                            <p class="text-gray-600 text-xs mt-1">Lower numbers show first</p>
                        </div>

                        <div class="mb-4 bg-gray-50 p-4 rounded">
                            <p class="text-sm text-gray-700"><strong>Click Count:</strong> {{ $adBox->click_count ?? 0 }}</p>
                        </div>

                        <div class="flex items-center justify-between">
                            <button type="submit" class="bg-grass hover:bg-grass-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                Update
                            </button>
                            <a href="{{ route('admin.ad-boxes.index') }}" class="text-gray-600 hover:text-gray-800">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function toggleLocationFields() {
            const checkbox = document.getElementById('is_national');
            const fields = document.getElementById('location-fields');
            fields.classList.toggle('hidden', checkbox.checked);
        }
    </script>
</x-app-layout>
