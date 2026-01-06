<?php
// Main Container
?>
<div class="container mx-auto px-4 py-8">

    <!-- Page Header -->
    <div class="mb-10 text-center">
        <h1 class="text-4xl md:text-5xl font-bold mb-4 brand-font">
            Find Your <span class="brand-gradient">Perfect Home</span>
        </h1>
        <p class="text-gray-600 text-lg max-w-3xl mx-auto">
            Browse verified rental listings with transparent information. Every property includes honest disclosures about common issues and practical solutions.
        </p>
    </div>

    <!-- Search and Filter Section -->
    <div class="bg-white rounded-2xl shadow-lg p-6 mb-8">
        <!-- Quick Search Bar -->
        <div class="mb-6">
            <div class="relative">
                <input
                    type="text"
                    id="searchInput"
                    placeholder="Search by location, property type, or keyword..."
                    class="w-full p-4 pl-12 pr-24 text-lg border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#2FA4E7] focus:border-transparent transition-all duration-300"
                >
                <i class="fas fa-search absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400 text-lg"></i>
                <button
                    id="searchBtn"
                    class="absolute right-2 top-1/2 transform -translate-y-1/2 bg-gradient-to-r from-[#2FA4E7] to-[#3CB371] text-white px-6 py-2 rounded-lg font-semibold hover:shadow-lg transition-all duration-300 hover:scale-105"
                >
                    Search
                </button>
            </div>
        </div>

        <!-- Advanced Filter Toggle -->
        <div class="mb-4">
            <button
                id="filterToggle"
                class="flex items-center text-[#2FA4E7] font-semibold hover:text-[#3CB371] transition-colors duration-300"
            >
                <i class="fas fa-sliders-h mr-2"></i>
                Advanced Filters
                <i id="filterToggleIcon" class="fas fa-chevron-down ml-2 transition-transform duration-300"></i>
            </button>
        </div>

        <!-- Advanced Filters (Collapsible) -->
        <div id="advancedFilters" class="filter-slide max-h-0">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 pt-4 border-t border-gray-100">
                <!-- Price Range Filter -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Price Range (KES)</label>
                    <div class="space-y-2">
                        <div class="flex justify-between text-sm text-gray-600">
                            <span id="priceMinDisplay">0</span>
                            <span id="priceMaxDisplay">200,000</span>
                        </div>
                        <div class="relative pt-4">
                            <input
                                type="range"
                                id="priceRange"
                                min="0"
                                max="200000"
                                step="5000"
                                value="50000"
                                class="w-full"
                            >
                        </div>
                    </div>
                </div>

                <!-- Property Type Filter -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Property Type</label>
                    <div id="propertyTypeContainer" class="flex flex-wrap gap-2">
                        <!-- Property types will be loaded dynamically -->
                    </div>
                </div>

                <!-- Bedrooms Filter -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Bedrooms</label>
                    <div class="flex flex-wrap gap-2">
                        <button class="tag filter-tag px-3 py-1 bg-gray-100 text-gray-700 rounded-full border border-gray-200 text-sm font-medium hover:bg-gray-200" data-filter="1">
                            1
                        </button>
                        <button class="tag filter-tag px-3 py-1 bg-gray-100 text-gray-700 rounded-full border border-gray-200 text-sm font-medium hover:bg-gray-200" data-filter="2">
                            2
                        </button>
                        <button class="tag filter-tag px-3 py-1 bg-gray-100 text-gray-700 rounded-full border border-gray-200 text-sm font-medium hover:bg-gray-200" data-filter="3">
                            3
                        </button>
                        <button class="tag filter-tag px-3 py-1 bg-gray-100 text-gray-700 rounded-full border border-gray-200 text-sm font-medium hover:bg-gray-200" data-filter="4+">
                            4+
                        </button>
                    </div>
                </div>

                <!-- Location Filter -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Location</label>
                    <select id="locationFilter" class="w-full p-3 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#2FA4E7] focus:border-transparent transition-all duration-300">
                        <option value="">All Locations</option>
                        <option value="Westlands">Westlands</option>
                        <option value="Kilimani">Kilimani</option>
                        <option value="Kileleshwa">Kileleshwa</option>
                        <option value="Lavington">Lavington</option>
                        <option value="Karen">Karen</option>
                        <option value="Runda">Runda</option>
                    </select>
                </div>
            </div>

            <!-- Action Buttons for Filters -->
            <div class="flex justify-end space-x-4 mt-6 pt-6 border-t border-gray-100">
                <button
                    id="clearFilters"
                    class="px-5 py-2 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors duration-300"
                >
                    Clear All
                </button>
                <button
                    id="applyFilters"
                    class="px-5 py-2 bg-gradient-to-r from-[#2FA4E7] to-[#3CB371] text-white font-semibold rounded-lg hover:shadow-lg transition-all duration-300 hover:scale-105"
                >
                    Apply Filters
                </button>
            </div>
        </div>
    </div>

    <!-- Results Summary -->
    <div class="flex flex-col md:flex-row justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">
                <span id="resultsCount">12</span> Properties Found
            </h2>
            <p class="text-gray-600">Sorted by: <span class="font-semibold text-[#2FA4E7]">Verified First</span></p>
        </div>

        <!-- Sort Options -->
        <div class="mt-4 md:mt-0">
            <select id="sortOptions" class="p-3 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#2FA4E7] focus:border-transparent transition-all duration-300">
                <option value="verified">Verified First</option>
                <option value="price-low">Price: Low to High</option>
                <option value="price-high">Price: High to Low</option>
                <option value="newest">Newest First</option>
                <option value="popular">Most Popular</option>
            </select>
        </div>
    </div>

    <!-- Property Listings Grid -->
    <div id="housesGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <!-- Houses will be loaded dynamically -->
    </div>

    <!-- Load More Button -->
    <div class="text-center mt-12">
        <button
            id="loadMore"
            class="px-8 py-4 bg-white border-2 border-[#2FA4E7] text-[#2FA4E7] font-semibold rounded-xl hover:bg-[#2FA4E7] hover:text-white transition-all duration-300 hover:scale-105 hover:shadow-lg"
        >
            Load More Properties
        </button>
    </div>

    <!-- Map View Toggle -->
    <div class="fixed bottom-6 right-6 z-20">
        <button
            id="mapToggle"
            class="bg-gradient-to-r from-[#2FA4E7] to-[#3CB371] text-white p-4 rounded-full shadow-2xl hover:shadow-3xl transition-all duration-300 hover:scale-110"
            title="View on Map"
        >
            <i class="fas fa-map-marked-alt text-xl"></i>
        </button>
    </div>
</div>

<!-- Property Details Modal -->
<div id="propertyModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/70 backdrop-blur-sm"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl w-full max-w-6xl max-h-[90vh] overflow-y-auto modal-enter">
            <!-- Modal Content -->
            <div class="flex flex-col md:flex-row h-full">
                <!-- Left Column - Property Images & Details -->
                <div class="md:w-2/3 overflow-y-auto">
                    <!-- Modal Header -->
                    <div class="sticky top-0 z-10 bg-white border-b border-gray-100 p-6 flex justify-between items-center">
                        <h2 class="text-2xl font-bold text-gray-800" id="modalPropertyTitle">Spacious 3-Bedroom Apartment</h2>
                        <button id="closeModal" class="text-gray-500 hover:text-gray-800 text-2xl">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>

                    <!-- Property Images Gallery -->
                    <div class="p-6">
                        <div class="grid grid-cols-2 gap-4 mb-6">
                            <div class="col-span-2 h-80 rounded-2xl overflow-hidden">
                                <img
                                    id="modalMainImage"
                                    src="https://images.unsplash.com/photo-1560518883-ce09059eeffa?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1073&q=80"
                                    alt="Property"
                                    class="w-full h-full object-cover"
                                >
                            </div>
                            <div class="h-40 rounded-xl overflow-hidden">
                                <img src="https://images.unsplash.com/photo-1586023492125-27b2c045efd7?ixlib=rb-4.0.3&auto=format&fit=crop&w=958&q=80" alt="Living Room" class="w-full h-full object-cover">
                            </div>
                            <div class="h-40 rounded-xl overflow-hidden">
                                <img src="https://images.unsplash.com/photo-1616594039964-ae9021a400a0?ixlib=rb-4.0.3&auto=format&fit=crop&w=1170&q=80" alt="Kitchen" class="w-full h-full object-cover">
                            </div>
                        </div>

                        <!-- Property Details -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                            <!-- Left Column -->
                            <div>
                                <h3 class="text-xl font-bold text-gray-800 mb-4">Property Details</h3>
                                <div class="space-y-4">
                                    <div class="flex justify-between py-3 border-b border-gray-100">
                                        <span class="text-gray-600">Property Type</span>
                                        <span class="font-semibold" id="modalPropertyType">Apartment</span>
                                    </div>
                                    <div class="flex justify-between py-3 border-b border-gray-100">
                                        <span class="text-gray-600">Bedrooms</span>
                                        <span class="font-semibold" id="modalBedrooms">3</span>
                                    </div>
                                    <div class="flex justify-between py-3 border-b border-gray-100">
                                        <span class="text-gray-600">Bathrooms</span>
                                        <span class="font-semibold" id="modalBathrooms">2</span>
                                    </div>
                                    <div class="flex justify-between py-3 border-b border-gray-100">
                                        <span class="text-gray-600">Square Feet</span>
                                        <span class="font-semibold" id="modalSquareFeet">1,200 sqft</span>
                                    </div>
                                    <div class="flex justify-between py-3 border-b border-gray-100">
                                        <span class="text-gray-600">Parking</span>
                                        <span class="font-semibold" id="modalParking">1 Dedicated</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Right Column -->
                            <div>
                                <h3 class="text-xl font-bold text-gray-800 mb-4">Location & Pricing</h3>
                                <div class="space-y-4">
                                    <div class="flex justify-between py-3 border-b border-gray-100">
                                        <span class="text-gray-600">Location</span>
                                        <span class="font-semibold" id="modalLocation">Westlands, Nairobi</span>
                                    </div>
                                    <div class="flex justify-between py-3 border-b border-gray-100">
                                        <span class="text-gray-600">Monthly Rent</span>
                                        <span class="font-bold text-lg text-[#2FA4E7]" id="modalRent">KES 85,000</span>
                                    </div>
                                    <div class="flex justify-between py-3 border-b border-gray-100">
                                        <span class="text-gray-600">Security Deposit</span>
                                        <span class="font-semibold" id="modalDeposit">KES 85,000</span>
                                    </div>
                                    <div class="flex justify-between py-3 border-b border-gray-100">
                                        <span class="text-gray-600">Available From</span>
                                        <span class="font-semibold" id="modalAvailable">Immediately</span>
                                    </div>
                                    <div class="flex justify-between py-3 border-b border-gray-100">
                                        <span class="text-gray-600">Verified Status</span>
                                        <span class="px-3 py-1 bg-gradient-to-r from-[#2FA4E7] to-[#3CB371] text-white rounded-full text-xs font-semibold">Verified</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Honest Disclosure Section -->
                        <div class="mb-8">
                            <h3 class="text-xl font-bold text-gray-800 mb-4">Honest Disclosure</h3>
                            <div class="bg-blue-50 border border-blue-100 rounded-2xl p-6">
                                <div class="flex items-start mb-4">
                                    <i class="fas fa-info-circle text-[#2FA4E7] text-xl mr-3 mt-1"></i>
                                    <div>
                                        <h4 class="font-bold text-gray-800 mb-2">Common Issues & Solutions</h4>
                                        <ul class="space-y-2 text-gray-700">
                                            <li class="flex items-start">
                                                <i class="fas fa-check-circle text-[#3CB371] mr-2 mt-1"></i>
                                                <span><strong>Water Pressure:</strong> Slightly low during peak hours. Solution: Building has water storage tanks.</span>
                                            </li>
                                            <li class="flex items-start">
                                                <i class="fas fa-check-circle text-[#3CB371] mr-2 mt-1"></i>
                                                <span><strong>Noise Level:</strong> Moderate street noise. Solution: Double-glazed windows installed.</span>
                                            </li>
                                            <li class="flex items-start">
                                                <i class="fas fa-check-circle text-[#3CB371] mr-2 mt-1"></i>
                                                <span><strong>Access Road:</strong> Partially paved. Solution: Property accessible by all vehicle types.</span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Amenities -->
                        <div class="mb-8">
                            <h3 class="text-xl font-bold text-gray-800 mb-4">Amenities & Features</h3>
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                                <div class="flex items-center p-3 bg-gray-50 rounded-xl">
                                    <i class="fas fa-wifi text-[#2FA4E7] mr-3"></i>
                                    <span>High-Speed WiFi</span>
                                </div>
                                <div class="flex items-center p-3 bg-gray-50 rounded-xl">
                                    <i class="fas fa-swimming-pool text-[#3CB371] mr-3"></i>
                                    <span>Swimming Pool</span>
                                </div>
                                <div class="flex items-center p-3 bg-gray-50 rounded-xl">
                                    <i class="fas fa-dumbbell text-[#2FA4E7] mr-3"></i>
                                    <span>Gym Access</span>
                                </div>
                                <div class="flex items-center p-3 bg-gray-50 rounded-xl">
                                    <i class="fas fa-shield-alt text-[#3CB371] mr-3"></i>
                                    <span>24/7 Security</span>
                                </div>
                                <div class="flex items-center p-3 bg-gray-50 rounded-xl">
                                    <i class="fas fa-paw text-[#2FA4E7] mr-3"></i>
                                    <span>Pet Friendly</span>
                                </div>
                                <div class="flex items-center p-3 bg-gray-50 rounded-xl">
                                    <i class="fas fa-car text-[#3CB371] mr-3"></i>
                                    <span>Dedicated Parking</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column - Booking & Actions -->
                <div class="md:w-1/3 bg-gradient-to-b from-blue-50 to-green-50 p-6 overflow-y-auto border-l border-gray-100">
                    <!-- Payment Access Card -->
                    <div class="bg-white rounded-2xl shadow-lg p-6 mb-6">
                        <h3 class="text-xl font-bold text-gray-800 mb-4">Access Full Details</h3>
                        <p class="text-gray-600 mb-6">Pay a one-time fee of <span class="font-bold text-[#2FA4E7]">KES 200</span> to unlock:</p>
                        <ul class="space-y-3 mb-6">
                            <li class="flex items-center">
                                <i class="fas fa-check-circle text-[#3CB371] mr-3"></i>
                                <span>Landlord Contact Details</span>
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-check-circle text-[#3CB371] mr-3"></i>
                                <span>Exact Location & Directions</span>
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-check-circle text-[#3CB371] mr-3"></i>
                                <span>Full Honest Disclosure Report</span>
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-check-circle text-[#3CB371] mr-3"></i>
                                <span>Property Viewing Arrangement</span>
                            </li>
                        </ul>
                        <button
                            id="payAccessFee"
                            class="w-full py-4 bg-gradient-to-r from-[#2FA4E7] to-[#3CB371] text-white font-semibold rounded-xl hover:shadow-xl transition-all duration-300 hover:scale-105 mb-4"
                        >
                            Pay KES 200 to Unlock
                        </button>
                        <p class="text-xs text-gray-500 text-center">One-time payment • Full refund if information is inaccurate</p>
                    </div>

                    <!-- Moving Services Card -->
                    <div class="bg-white rounded-2xl shadow-lg p-6 mb-6">
                        <h3 class="text-xl font-bold text-gray-800 mb-4">Need Moving Services?</h3>
                        <p class="text-gray-600 mb-6">Connect with trusted movers through our platform</p>
                        <div class="space-y-4 mb-6">
                            <div class="flex items-center p-4 bg-gray-50 rounded-xl">
                                <div class="w-12 h-12 rounded-full bg-gradient-to-r from-[#2FA4E7] to-blue-100 flex items-center justify-center mr-4">
                                    <i class="fas fa-truck text-white"></i>
                                </div>
                                <div>
                                    <h4 class="font-bold text-gray-800">SafeMove Relocations</h4>
                                    <p class="text-sm text-gray-600">4.8 ★ (120 reviews)</p>
                                </div>
                            </div>
                            <div class="flex items-center p-4 bg-gray-50 rounded-xl">
                                <div class="w-12 h-12 rounded-full bg-gradient-to-r from-[#3CB371] to-green-100 flex items-center justify-center mr-4">
                                    <i class="fas fa-boxes text-white"></i>
                                </div>
                                <div>
                                    <h4 class="font-bold text-gray-800">QuickShift Movers</h4>
                                    <p class="text-sm text-gray-600">4.6 ★ (85 reviews)</p>
                                </div>
                            </div>
                        </div>
                        <button
                            id="requestMovers"
                            class="w-full py-4 border-2 border-[#2FA4E7] text-[#2FA4E7] font-semibold rounded-xl hover:bg-[#2FA4E7] hover:text-white transition-all duration-300"
                        >
                            Request Moving Quote
                        </button>
                    </div>

                    <!-- Contact Form -->
                    <div class="bg-white rounded-2xl shadow-lg p-6">
                        <h3 class="text-xl font-bold text-gray-800 mb-4">Schedule Viewing</h3>
                        <form id="viewingForm" class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                                <input type="text" name="full_name" class="w-full p-3 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#2FA4E7] focus:border-transparent transition-all duration-300" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                                <input type="tel" name="phone" class="w-full p-3 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#2FA4E7] focus:border-transparent transition-all duration-300" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Preferred Date</label>
                                <input type="date" name="preferred_date" class="w-full p-3 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#2FA4E7] focus:border-transparent transition-all duration-300" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Message (Optional)</label>
                                <textarea rows="3" name="message" class="w-full p-3 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#2FA4E7] focus:border-transparent transition-all duration-300" placeholder="Any specific requirements..."></textarea>
                            </div>
                            <button
                                type="submit"
                                class="w-full py-4 bg-gradient-to-r from-[#2FA4E7] to-[#3CB371] text-white font-semibold rounded-xl hover:shadow-xl transition-all duration-300 hover:scale-105"
                            >
                                Request Viewing
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Moving Services Modal -->
<div id="moversModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/70 backdrop-blur-sm"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl w-full max-w-4xl max-h-[90vh] overflow-y-auto modal-enter">
            <!-- Moving Services Content -->
            <div class="p-8">
                <div class="flex justify-between items-center mb-8">
                    <h2 class="text-3xl font-bold text-gray-800">Request Moving Services</h2>
                    <button id="closeMoversModal" class="text-gray-500 hover:text-gray-800 text-2xl">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Form -->
                    <div>
                        <form id="movingRequestForm" class="space-y-6">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Moving From</label>
                                <input type="text" class="w-full p-4 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#2FA4E7] focus:border-transparent transition-all duration-300" placeholder="Current address" required>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Moving To</label>
                                <input type="text" class="w-full p-4 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#2FA4E7] focus:border-transparent transition-all duration-300" placeholder="New property address" required>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Moving Date</label>
                                    <input type="date" class="w-full p-4 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#2FA4E7] focus:border-transparent transition-all duration-300" required>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Number of Rooms</label>
                                    <select class="w-full p-4 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#2FA4E7] focus:border-transparent transition-all duration-300">
                                        <option>1 Room</option>
                                        <option>2 Rooms</option>
                                        <option>3 Rooms</option>
                                        <option>4+ Rooms</option>
                                    </select>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Special Requirements</label>
                                <textarea rows="4" class="w-full p-4 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#2FA4E7] focus:border-transparent transition-all duration-300" placeholder="Fragile items, special handling requirements..."></textarea>
                            </div>

                            <button
                                type="submit"
                                class="w-full py-4 bg-gradient-to-r from-[#2FA4E7] to-[#3CB371] text-white font-semibold rounded-xl hover:shadow-xl transition-all duration-300 hover:scale-105 text-lg"
                            >
                                Get Quotes from Movers
                            </button>
                        </form>
                    </div>

                    <!-- Moving Services Info -->
                    <div class="bg-gradient-to-br from-blue-50 to-green-50 rounded-2xl p-8">
                        <h3 class="text-2xl font-bold text-gray-800 mb-6">Why Use Rheaspark Movers?</h3>
                        <div class="space-y-6">
                            <div class="flex items-start">
                                <div class="w-12 h-12 rounded-xl bg-gradient-to-r from-[#2FA4E7] to-blue-100 flex items-center justify-center mr-4 flex-shrink-0">
                                    <i class="fas fa-shield-alt text-white text-xl"></i>
                                </div>
                                <div>
                                    <h4 class="font-bold text-gray-800 mb-2">Verified & Trusted</h4>
                                    <p class="text-gray-600">All movers are thoroughly vetted and verified by our team.</p>
                                </div>
                            </div>

                            <div class="flex items-start">
                                <div class="w-12 h-12 rounded-xl bg-gradient-to-r from-[#3CB371] to-green-100 flex items-center justify-center mr-4 flex-shrink-0">
                                    <i class="fas fa-money-bill-wave text-white text-xl"></i>
                                </div>
                                <div>
                                    <h4 class="font-bold text-gray-800 mb-2">Competitive Pricing</h4>
                                    <p class="text-gray-600">Get multiple quotes and choose the best option for your budget.</p>
                                </div>
                            </div>

                            <div class="flex items-start">
                                <div class="w-12 h-12 rounded-xl bg-gradient-to-r from-[#2FA4E7] to-blue-100 flex items-center justify-center mr-4 flex-shrink-0">
                                    <i class="fas fa-handshake text-white text-xl"></i>
                                </div>
                                <div>
                                    <h4 class="font-bold text-gray-800 mb-2">10% Commission</h4>
                                    <p class="text-gray-600">We charge movers only 10% per successful job - no hidden fees for you.</p>
                                </div>
                            </div>

                            <div class="flex items-start">
                                <div class="w-12 h-12 rounded-xl bg-gradient-to-r from-[#3CB371] to-green-100 flex items-center justify-center mr-4 flex-shrink-0">
                                    <i class="fas fa-star text-white text-xl"></i>
                                </div>
                                <div>
                                    <h4 class="font-bold text-gray-800 mb-2">Rating System</h4>
                                    <p class="text-gray-600">Rate your mover after the job to help others make informed decisions.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Custom Scrollbar */
    ::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }

    ::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }

    ::-webkit-scrollbar-thumb {
        background: linear-gradient(to bottom, #2FA4E7, #3CB371);
        border-radius: 10px;
    }

    /* Modal Animation */
    .modal-enter {
        opacity: 0;
        transform: scale(0.9);
    }

    .modal-enter-active {
        opacity: 1;
        transform: scale(1);
        transition: opacity 300ms, transform 300ms;
    }

    .modal-exit {
        opacity: 1;
        transform: scale(1);
    }

    .modal-exit-active {
        opacity: 0;
        transform: scale(0.9);
        transition: opacity 300ms, transform 300ms;
    }

    /* Card Hover Effects */
    .property-card {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        transform-origin: center;
    }

    .property-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
    }

    /* Filter Animation */
    .filter-slide {
        transition: max-height 0.5s ease-out;
        overflow: hidden;
    }

    /* Price Range Styling */
    input[type="range"] {
        -webkit-appearance: none;
        height: 6px;
        border-radius: 5px;
        background: linear-gradient(to right, #2FA4E7, #3CB371);
        outline: none;
    }

    input[type="range"]::-webkit-slider-thumb {
        -webkit-appearance: none;
        width: 22px;
        height: 22px;
        border-radius: 50%;
        background: white;
        border: 3px solid #2FA4E7;
        cursor: pointer;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
    }

    /* Tag Animation */
    .tag {
        transition: all 0.2s ease;
    }

    .tag:hover {
        transform: scale(1.05);
    }

    /* Loading Skeleton Animation */
    @keyframes shimmer {
        0% {
            background-position: -200px 0;
        }
        100% {
            background-position: calc(200px + 100%) 0;
        }
    }

    .skeleton {
        background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
        background-size: 200px 100%;
        animation: shimmer 1.5s infinite;
    }

    /* Map Pin Animation */
    @keyframes pulse {
        0% {
            transform: scale(1);
            opacity: 1;
        }
        50% {
            transform: scale(1.1);
            opacity: 0.7;
        }
        100% {
            transform: scale(1);
            opacity: 1;
        }
    }

    .pulse-animation {
        animation: pulse 2s infinite;
    }
</style>

<script>
    // DOM Elements
    const propertyModal = document.getElementById('propertyModal');
    const moversModal = document.getElementById('moversModal');
    const closeModal = document.getElementById('closeModal');
    const closeMoversModal = document.getElementById('closeMoversModal');
    const filterToggle = document.getElementById('filterToggle');
    const filterToggleIcon = document.getElementById('filterToggleIcon');
    const advancedFilters = document.getElementById('advancedFilters');
    const searchBtn = document.getElementById('searchBtn');
    const clearFilters = document.getElementById('clearFilters');
    const applyFilters = document.getElementById('applyFilters');
    const loadMore = document.getElementById('loadMore');
    const mapToggle = document.getElementById('mapToggle');
    const payAccessFee = document.getElementById('payAccessFee');
    const requestMovers = document.getElementById('requestMovers');
    const viewingForm = document.getElementById('viewingForm');
    const movingRequestForm = document.getElementById('movingRequestForm');
    const priceRange = document.getElementById('priceRange');
    const priceMinDisplay = document.getElementById('priceMinDisplay');
    const priceMaxDisplay = document.getElementById('priceMaxDisplay');

    // Global variables
    let housesData = [];
    let currentPropertyId = null; // Store current property ID for modal
    let currentFilters = {
        price_min: 0,
        price_max: 200000,
        property_type: '',
        bedrooms: '',
        location: '',
        sort: 'newest'
    };

    // Load Property Types
    function loadPropertyTypes() {
        fetch('api/property_types.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const container = document.getElementById('propertyTypeContainer');
                container.innerHTML = '';
                data.data.forEach(type => {
                    const button = document.createElement('button');
                    button.className = 'tag filter-tag px-3 py-1 rounded-full border text-sm font-medium';
                    button.style.backgroundColor = type.color + '20';
                    button.style.borderColor = type.color;
                    button.style.color = type.color;
                    button.setAttribute('data-filter', type.slug);
                    button.textContent = type.name;
                    container.appendChild(button);
                });
            }
        });
    }

    // Load Locations
    function loadLocations() {
        fetch('api/locations.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const select = document.getElementById('locationFilter');
                select.innerHTML = '<option value="">All Locations</option>';
                data.data.forEach(location => {
                    const option = document.createElement('option');
                    option.value = location;
                    option.textContent = location;
                    select.appendChild(option);
                });
            }
        });
    }

    // Load Houses
    function loadHouses() {
        const params = new URLSearchParams(currentFilters);
        fetch('api/houses.php?' + params)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                housesData = data.data;
                renderHouses();
                document.getElementById('resultsCount').textContent = data.count;
            }
        });
    }

    // Render Houses
    function renderHouses() {
        const grid = document.getElementById('housesGrid');
        grid.innerHTML = '';
        housesData.forEach(house => {
            const card = createHouseCard(house);
            grid.appendChild(card);
        });
    }

    // Create House Card
    function createHouseCard(house) {
        const card = document.createElement('div');
        card.className = 'property-card bg-white rounded-2xl shadow-md overflow-hidden cursor-pointer';
        card.setAttribute('data-property-id', house.id);

        const features = [];
        if (house.bedrooms) features.push(`<div class="flex items-center"><i class="fas fa-bed text-[#2FA4E7] mr-1"></i><span class="text-sm text-gray-700">${house.bedrooms} Bed</span></div>`);
        if (house.bathrooms) features.push(`<div class="flex items-center"><i class="fas fa-bath text-[#2FA4E7] mr-1"></i><span class="text-sm text-gray-700">${house.bathrooms} Bath</span></div>`);
        if (house.size_sqft) features.push(`<div class="flex items-center"><i class="fas fa-ruler-combined text-[#2FA4E7] mr-1"></i><span class="text-sm text-gray-700">${house.size_sqft} sqft</span></div>`);
        if (house.parking_spaces) features.push(`<div class="flex items-center"><i class="fas fa-car text-[#2FA4E7] mr-1"></i><span class="text-sm text-gray-700">${house.parking_spaces} Parking</span></div>`);

        const tags = [];
        if (house.pet_friendly) tags.push('<span class="px-2 py-1 bg-blue-50 text-[#2FA4E7] text-xs rounded-full">Pet Friendly</span>');
        if (house.wifi) tags.push('<span class="px-2 py-1 bg-green-50 text-[#3CB371] text-xs rounded-full">WiFi</span>');
        if (house.swimming_pool) tags.push('<span class="px-2 py-1 bg-blue-50 text-[#2FA4E7] text-xs rounded-full">Swimming Pool</span>');
        if (house.security_24_7) tags.push('<span class="px-2 py-1 bg-green-50 text-[#3CB371] text-xs rounded-full">Security</span>');

        card.innerHTML = `
            <div class="relative overflow-hidden h-64">
                <img src="${house.image_url_1}" alt="${house.title}" class="w-full h-full object-cover transition-transform duration-700 hover:scale-110">
                ${house.verified ? '<div class="absolute top-4 left-4 bg-gradient-to-r from-[#2FA4E7] to-[#3CB371] text-white px-3 py-1 rounded-full text-xs font-semibold flex items-center"><i class="fas fa-shield-alt mr-1"></i> Verified</div>' : ''}
                <div class="absolute top-4 right-4 bg-white/90 backdrop-blur-sm text-gray-800 px-3 py-2 rounded-xl shadow-sm">
                    <span class="font-bold text-lg">KES ${house.price.toLocaleString()}</span>
                    <span class="text-xs text-gray-600 block">/month</span>
                </div>
            </div>
            <div class="p-6">
                <h3 class="text-xl font-bold text-gray-800 mb-2">${house.title}</h3>
                <div class="flex items-center text-gray-600 mb-4">
                    <i class="fas fa-map-marker-alt text-[#3CB371] mr-2"></i>
                    <span>${house.location}</span>
                </div>
                <div class="flex flex-wrap gap-3 mb-4">
                    ${features.join('')}
                </div>
                <div class="flex flex-wrap gap-2 mb-6">
                    ${tags.join('')}
                </div>
                <div class="flex space-x-3">
                    <button class="view-details-btn flex-1 bg-gradient-to-r from-[#2FA4E7] to-[#3CB371] text-white py-3 rounded-lg font-semibold hover:shadow-lg transition-all duration-300">
                        View Details
                    </button>
                    <button class="favorite-btn w-12 h-12 flex items-center justify-center border border-gray-200 rounded-lg text-gray-500 hover:text-red-500 hover:border-red-200 transition-colors duration-300">
                        <i class="far fa-heart text-xl"></i>
                    </button>
                </div>
            </div>
        `;
        return card;
    }

    // Initialize Price Range Display
    priceMinDisplay.textContent = formatCurrency(0);
    priceMaxDisplay.textContent = formatCurrency(200000);

    // Format Currency
    function formatCurrency(amount) {
        return amount.toLocaleString('en-KE');
    }

    // Update Price Range Display
    priceRange.addEventListener('input', function() {
        const value = parseInt(this.value);
        priceMinDisplay.textContent = formatCurrency(10000);
        priceMaxDisplay.textContent = formatCurrency(value);
        currentFilters.price_max = value;
    });

    // Toggle Advanced Filters
    filterToggle.addEventListener('click', function() {
        if (advancedFilters.style.maxHeight === '0px' || advancedFilters.style.maxHeight === '') {
            advancedFilters.style.maxHeight = advancedFilters.scrollHeight + 'px';
            filterToggleIcon.style.transform = 'rotate(180deg)';
        } else {
            advancedFilters.style.maxHeight = '0px';
            filterToggleIcon.style.transform = 'rotate(0deg)';
        }
    });

    // Filter Tag Selection
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('filter-tag')) {
            const tag = e.target;
            const filterType = tag.closest('#propertyTypeContainer') ? 'property_type' : 'bedrooms';

            // Remove active from siblings
            const container = tag.parentElement;
            container.querySelectorAll('.filter-tag').forEach(t => {
                t.classList.remove('active');
                t.style.background = '';
                t.style.color = '';
                t.style.borderColor = '';
            });

            // Add active to clicked
            tag.classList.add('active');
            tag.style.background = tag.style.borderColor;
            tag.style.color = 'white';

            // Update filter
            currentFilters[filterType] = tag.dataset.filter;
        }
    });

    // Apply Filters
    applyFilters.addEventListener('click', function() {
        // Show loading state
        this.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Applying...';

        // Load houses with filters
        loadHouses();

        // Close filters
        advancedFilters.style.maxHeight = '0px';
        filterToggleIcon.style.transform = 'rotate(0deg)';

        // Reset button
        setTimeout(() => {
            this.innerHTML = 'Apply Filters';
            showNotification('Filters applied successfully!', 'success');
        }, 500);
    });

    // Clear Filters
    clearFilters.addEventListener('click', function() {
        // Reset filter tags
        document.querySelectorAll('.filter-tag').forEach(tag => {
            tag.classList.remove('active');
            tag.style.background = '';
            tag.style.color = '';
            tag.style.borderColor = '';
        });

        // Reset price range
        priceRange.value = 200000;
        currentFilters.price_max = 200000;
        currentFilters.price_min = 0;
        priceMinDisplay.textContent = formatCurrency(0);
        priceMaxDisplay.textContent = formatCurrency(200000);

        // Reset location filter
        document.getElementById('locationFilter').value = '';
        currentFilters.location = '';

        // Reset filters
        currentFilters.property_type = '';
        currentFilters.bedrooms = '';

        // Load all houses
        loadHouses();

        // Show notification
        showNotification('All filters cleared!', 'info');
    });

    // Search Functionality
    searchBtn.addEventListener('click', function() {
        const searchTerm = document.getElementById('searchInput').value;
        if (searchTerm.trim()) {
            // Show loading
            this.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Searching...';

            // Simulate search
            setTimeout(() => {
                this.innerHTML = 'Search';
                showNotification(`Search results for: "${searchTerm}"`, 'info');
            }, 800);
        }
    });

    // Enter key to search
    document.getElementById('searchInput').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            searchBtn.click();
        }
    });

    // Open Property Modal (Event delegation for dynamic elements)
    document.getElementById('housesGrid').addEventListener('click', function(event) {
        const card = event.target.closest('.property-card');
        if (!card) return;

        // Prevent event bubbling for buttons inside cards
        if (event.target.classList.contains('favorite-btn')) {
            return;
        }

        const propertyId = card.dataset.propertyId;
        openPropertyModal(propertyId);
    });

    // Open Property Modal Function
    function openPropertyModal(propertyId) {
        const house = housesData.find(h => h.id == propertyId);
        if (!house) return;

        // Store current property ID
        currentPropertyId = propertyId;

        // Update modal content
        document.getElementById('modalPropertyTitle').textContent = house.title;
        document.getElementById('modalPropertyType').textContent = house.property_type.charAt(0).toUpperCase() + house.property_type.slice(1);
        document.getElementById('modalBedrooms').textContent = house.bedrooms || 'N/A';
        document.getElementById('modalBathrooms').textContent = house.bathrooms || 'N/A';
        document.getElementById('modalSquareFeet').textContent = house.size_sqft ? house.size_sqft + ' sqft' : 'N/A';
        document.getElementById('modalParking').textContent = house.parking_spaces ? house.parking_spaces + ' Spaces' : 'N/A';
        document.getElementById('modalLocation').textContent = house.location;
        document.getElementById('modalRent').textContent = 'KES ' + house.price.toLocaleString();
        document.getElementById('modalDeposit').textContent = house.security_deposit ? 'KES ' + house.security_deposit.toLocaleString() : 'N/A';
        document.getElementById('modalAvailable').textContent = house.available_from || 'Immediately';
        document.getElementById('modalMainImage').src = house.image_url_1;

        // Update thumbnails
        const thumbnails = document.querySelectorAll('.modal-enter img:not(#modalMainImage)');
        if (thumbnails.length >= 2) {
            thumbnails[0].src = house.image_url_2 || house.image_url_1;
            thumbnails[1].src = house.image_url_3 || house.image_url_1;
        }

        // Update amenities
        const amenitiesContainer = document.querySelector('.modal-enter .grid.grid-cols-2.md\\:grid-cols-3');
        const amenities = [];
        if (house.wifi) amenities.push('<div class="flex items-center p-3 bg-gray-50 rounded-xl"><i class="fas fa-wifi text-[#2FA4E7] mr-3"></i><span>High-Speed WiFi</span></div>');
        if (house.swimming_pool) amenities.push('<div class="flex items-center p-3 bg-gray-50 rounded-xl"><i class="fas fa-swimming-pool text-[#3CB371] mr-3"></i><span>Swimming Pool</span></div>');
        if (house.gym) amenities.push('<div class="flex items-center p-3 bg-gray-50 rounded-xl"><i class="fas fa-dumbbell text-[#2FA4E7] mr-3"></i><span>Gym Access</span></div>');
        if (house.security_24_7) amenities.push('<div class="flex items-center p-3 bg-gray-50 rounded-xl"><i class="fas fa-shield-alt text-[#3CB371] mr-3"></i><span>24/7 Security</span></div>');
        if (house.pet_friendly) amenities.push('<div class="flex items-center p-3 bg-gray-50 rounded-xl"><i class="fas fa-paw text-[#2FA4E7] mr-3"></i><span>Pet Friendly</span></div>');
        if (house.dedicated_parking) amenities.push('<div class="flex items-center p-3 bg-gray-50 rounded-xl"><i class="fas fa-car text-[#3CB371] mr-3"></i><span>Dedicated Parking</span></div>');
        amenitiesContainer.innerHTML = amenities.join('');

        // Show modal with animation
        propertyModal.classList.remove('hidden');
        setTimeout(() => {
            propertyModal.querySelector('.modal-enter').classList.add('modal-enter-active');
        }, 10);

        // Prevent body scrolling
        document.body.style.overflow = 'hidden';
    }

    // Close Property Modal
    closeModal.addEventListener('click', function() {
        propertyModal.querySelector('.modal-enter').classList.remove('modal-enter-active');
        setTimeout(() => {
            propertyModal.classList.add('hidden');
            document.body.style.overflow = '';
        }, 300);
    });

    // Close on background click
    propertyModal.addEventListener('click', function(e) {
        if (e.target === this) {
            closeModal.click();
        }
    });

    // Open Movers Modal
    requestMovers.addEventListener('click', function() {
        // Close property modal first if open
        if (!propertyModal.classList.contains('hidden')) {
            closeModal.click();
            setTimeout(() => {
                openMoversModal();
            }, 350);
        } else {
            openMoversModal();
        }
    });

    // Open Movers Modal Function
    function openMoversModal() {
        moversModal.classList.remove('hidden');
        setTimeout(() => {
            moversModal.querySelector('.modal-enter').classList.add('modal-enter-active');
        }, 10);

        // Prevent body scrolling
        document.body.style.overflow = 'hidden';
    }

    // Close Movers Modal
    closeMoversModal.addEventListener('click', function() {
        moversModal.querySelector('.modal-enter').classList.remove('modal-enter-active');
        setTimeout(() => {
            moversModal.classList.add('hidden');
            document.body.style.overflow = '';
        }, 300);
    });

    // Close on background click
    moversModal.addEventListener('click', function(e) {
        if (e.target === this) {
            closeMoversModal.click();
        }
    });

    // Pay Access Fee
    payAccessFee.addEventListener('click', function() {
        this.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Processing...';

        // Simulate payment processing
        setTimeout(() => {
            this.innerHTML = '<i class="fas fa-check mr-2"></i> Payment Successful!';
            this.style.background = '#3CB371';

            // Show success notification
            showNotification('Access granted! Full property details are now available.', 'success');

            // Reset button after delay
            setTimeout(() => {
                this.innerHTML = 'Pay KES 200 to Unlock';
                this.style.background = '';
            }, 3000);
        }, 1500);
    });

    // Viewing Form Submission
    viewingForm.addEventListener('submit', function(e) {
        e.preventDefault();

        // Check if user is logged in
        if (!window.userLoggedIn) {
            showNotification('Please login to submit viewing requests', 'error');
            return;
        }

        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;

        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Sending...';
        submitBtn.disabled = true;

        // Get form data
        const formData = new FormData(this);
        const viewingData = {
            house_id: currentPropertyId,
            full_name: formData.get('full_name').trim(),
            phone: formData.get('phone').trim(),
            preferred_date: formData.get('preferred_date'),
            message: formData.get('message').trim()
        };

        // Validate required fields
        if (!viewingData.house_id || !viewingData.full_name || !viewingData.phone || !viewingData.preferred_date) {
            showNotification('All required fields must be provided', 'error');
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
            return;
        }

        // Submit to server
        fetch('api/viewing_requests.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(viewingData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                submitBtn.innerHTML = '<i class="fas fa-check mr-2"></i> Request Sent!';

                // Show success notification
                showNotification('Viewing request sent successfully! The landlord will contact you shortly.', 'success');

                // Reset form and button
                setTimeout(() => {
                    this.reset();
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                }, 3000);
            } else {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
                showNotification(data.error || 'Failed to send viewing request', 'error');
            }
        })
        .catch(error => {
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
            showNotification('Network error. Please try again.', 'error');
        });
    });

    // Moving Request Form Submission
    movingRequestForm.addEventListener('submit', function(e) {
        e.preventDefault();

        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;

        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Processing...';
        submitBtn.disabled = true;

        // Simulate form submission
        setTimeout(() => {
            submitBtn.innerHTML = '<i class="fas fa-check mr-2"></i> Request Sent!';

            // Show success notification
            showNotification('Moving request sent! You will receive quotes from movers within 24 hours.', 'success');

            // Close modal after delay
            setTimeout(() => {
                closeMoversModal.click();

                // Reset form and button
                this.reset();
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }, 2000);
        }, 1500);
    });

    // Load More Properties
    loadMore.addEventListener('click', function() {
        this.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Loading...';

        // Simulate loading more properties
        setTimeout(() => {
            this.innerHTML = 'Load More Properties';
            showNotification('6 more properties loaded!', 'info');
        }, 1200);
    });

    // Map Toggle
    mapToggle.addEventListener('click', function() {
        showNotification('Map view coming soon!', 'info');
    });

    // Favorite Button Toggle (Event delegation)
    document.getElementById('housesGrid').addEventListener('click', function(event) {
        if (event.target.closest('.favorite-btn')) {
            event.stopPropagation(); // Prevent opening modal

            const btn = event.target.closest('.favorite-btn');
            const icon = btn.querySelector('i');
            const card = btn.closest('.property-card');
            const propertyId = card.dataset.propertyId;

            const isFavorited = icon.classList.contains('fas');
            const action = isFavorited ? 'remove' : 'add';

            // Optimistically update UI
            if (action === 'add') {
                icon.classList.remove('far');
                icon.classList.add('fas');
                btn.classList.add('text-red-500');
                btn.classList.add('border-red-200');
            } else {
                icon.classList.remove('fas');
                icon.classList.add('far');
                btn.classList.remove('text-red-500');
                btn.classList.remove('border-red-200');
            }

            // Save to server
            fetch('api/favorites.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    house_id: propertyId,
                    action: action
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification(data.message, action === 'add' ? 'success' : 'info');
                } else {
                    // Revert UI on error
                    if (action === 'add') {
                        icon.classList.remove('fas');
                        icon.classList.add('far');
                        btn.classList.remove('text-red-500');
                        btn.classList.remove('border-red-200');
                    } else {
                        icon.classList.remove('far');
                        icon.classList.add('fas');
                        btn.classList.add('text-red-500');
                        btn.classList.add('border-red-200');
                    }
                    showNotification(data.error || 'Failed to update favorites', 'error');
                }
            })
            .catch(error => {
                // Revert UI on error
                if (action === 'add') {
                    icon.classList.remove('fas');
                    icon.classList.add('far');
                    btn.classList.remove('text-red-500');
                    btn.classList.remove('border-red-200');
                } else {
                    icon.classList.remove('far');
                    icon.classList.add('fas');
                    btn.classList.add('text-red-500');
                    btn.classList.add('border-red-200');
                }
                showNotification('Network error. Please try again.', 'error');
            });
        }
    });

    // Location Filter Change
    document.getElementById('locationFilter').addEventListener('change', function() {
        currentFilters.location = this.value;
    });

    // Sort Options Change
    document.getElementById('sortOptions').addEventListener('change', function() {
        currentFilters.sort = this.value;
        loadHouses();
        showNotification(`Sorted by: ${this.options[this.selectedIndex].text}`, 'info');
    });

    // Show Notification Function
    function showNotification(message, type) {
        // Remove existing notification
        const existingNotification = document.querySelector('.notification');
        if (existingNotification) {
            existingNotification.remove();
        }

        // Create notification element
        const notification = document.createElement('div');
        notification.className = `notification fixed top-6 right-6 z-50 px-6 py-4 rounded-xl shadow-2xl transform transition-all duration-500 translate-x-full`;

        // Set notification style based on type
        if (type === 'success') {
            notification.style.background = 'linear-gradient(90deg, #3CB371, #4CAF50)';
        } else if (type === 'error') {
            notification.style.background = 'linear-gradient(90deg, #F44336, #E53935)';
        } else {
            notification.style.background = 'linear-gradient(90deg, #2FA4E7, #2196F3)';
        }

        notification.innerHTML = `
            <div class="flex items-center">
                <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'} text-white text-xl mr-3"></i>
                <span class="text-white font-semibold">${message}</span>
            </div>
        `;

        // Add to body
        document.body.appendChild(notification);

        // Animate in
        setTimeout(() => {
            notification.classList.remove('translate-x-full');
        }, 10);

        // Remove after 3 seconds
        setTimeout(() => {
            notification.classList.add('translate-x-full');
            setTimeout(() => {
                notification.remove();
            }, 500);
        }, 3000);
    }

    // Initialize with filters open on larger screens
    window.addEventListener('load', function() {
        loadPropertyTypes();
        loadLocations();
        loadHouses();

        if (window.innerWidth >= 768) {
            advancedFilters.style.maxHeight = advancedFilters.scrollHeight + 'px';
            filterToggleIcon.style.transform = 'rotate(180deg)';
        }

        // Check for URL parameters to open specific property
        const urlParams = new URLSearchParams(window.location.search);
        const propertyId = urlParams.get('property');
        const action = urlParams.get('action');

        if (propertyId) {
            // Fetch specific property and open modal
            fetch(`api/property.php?id=${propertyId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Add to housesData if not already there
                    const existingIndex = housesData.findIndex(h => h.id == propertyId);
                    if (existingIndex === -1) {
                        housesData.push(data.data);
                    }

                    openPropertyModal(propertyId);
                    if (action === 'viewing') {
                        // Scroll to the viewing form
                        setTimeout(() => {
                            document.getElementById('viewingForm').scrollIntoView({ behavior: 'smooth' });
                        }, 500);
                    }
                } else {
                    console.error('Property not found:', propertyId);
                    showNotification('Property not found', 'error');
                }
            })
            .catch(error => {
                console.error('Error fetching property:', error);
                showNotification('Error loading property details', 'error');
            });
        }
    });

    // Set login status
    window.userLoggedIn = <?php echo isset($_SESSION['user_id']) ? 'true' : 'false'; ?>;
</script>