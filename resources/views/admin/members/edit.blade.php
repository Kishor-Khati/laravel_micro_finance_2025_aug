@extends('layouts.admin')

@section('title', 'Edit Member')
@section('subtitle', 'Update member information')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="bg-white rounded-lg shadow-lg p-6">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Edit Member</h1>
            <a href="{{ route('admin.members.show', $member) }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors duration-200">
                <i class="fas fa-arrow-left mr-2"></i>Back to Member
            </a>
        </div>

        <form action="{{ route('admin.members.update', $member) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')
            
            <!-- Member Number Section - FIXED VERSION -->
            <div class="bg-gray-50 p-4 rounded-lg">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Member Number</h3>
                <div class="space-y-4">
                    <!-- Member Number Type Selection -->
                    <div class="flex space-x-4">
                        <label class="flex items-center">
                            <input type="radio" name="member_number_type" value="auto" {{ $member->member_number_auto_generated ? 'checked' : '' }} class="mr-2">
                            <span class="text-sm font-medium text-gray-700">Auto Generated</span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" name="member_number_type" value="manual" {{ !$member->member_number_auto_generated ? 'checked' : '' }} class="mr-2">
                            <span class="text-sm font-medium text-gray-700">Manual Entry</span>
                        </label>
                    </div>
                    
                    <!-- Auto Generate Section -->
                    <div id="auto-generate-section" {{ !$member->member_number_auto_generated ? 'class=hidden' : '' }}>
                        <button type="button" id="generate-member-number" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors duration-200">
                            <i class="fas fa-magic mr-2"></i>Generate New Member Number
                        </button>
                        <input type="hidden" name="member_number_auto_generated" value="{{ $member->member_number_auto_generated ? '1' : '0' }}">
                        
                        <!-- Current Number Display -->
                        <div id="current-number-display" class="mt-4">
                            <label for="current_member_number" class="block mb-2 text-sm font-medium text-gray-900">Current Member Number</label>
                            <div class="flex space-x-2">
                                <input type="text" id="current_member_number" value="{{ $member->member_number }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" readonly>
                                <input type="hidden" name="member_number" id="member_number_auto" value="{{ $member->member_number }}">
                            </div>
                        </div>
                        
                        <!-- Generated Number Display -->
                        <div id="generated-number-display" class="mt-4 hidden">
                            <label for="generated_number" class="block mb-2 text-sm font-medium text-gray-900">Generated Member Number</label>
                            <div class="flex space-x-2">
                                <input type="text" id="generated_number" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" readonly>
                                <input type="hidden" name="member_number" id="member_number_generated" disabled>
                                <button type="button" id="reset-member-number" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors duration-200">
                                    <i class="fas fa-undo mr-2"></i>Reset
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Manual Entry Section -->
                    <div id="manual-entry-section" {{ $member->member_number_auto_generated ? 'class=hidden' : '' }}>
                        <label for="member_number_input" class="block mb-2 text-sm font-medium text-gray-900">Member Number</label>
                        <input type="text" name="member_number" id="member_number_input" value="{{ old('member_number', $member->member_number) }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="Enter member number">
                    </div>
                </div>
            </div>

            <!-- Personal Information -->
            <div class="bg-gray-50 p-4 rounded-lg">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Personal Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Full Name -->
                    <div class="md:col-span-2">
                        <label for="full_name" class="block mb-2 text-sm font-medium text-gray-900">Full Name *</label>
                        <input type="text" name="full_name" id="full_name" value="{{ old('full_name', $member->full_name) }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="Enter full name" required>
                        @error('full_name')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Gender -->
                    <div>
                        <label for="gender" class="block mb-2 text-sm font-medium text-gray-900">Gender *</label>
                        <select name="gender" id="gender" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required>
                            <option value="">Select Gender</option>
                            <option value="male" {{ old('gender', $member->gender) == 'male' ? 'selected' : '' }}>Male</option>
                            <option value="female" {{ old('gender', $member->gender) == 'female' ? 'selected' : '' }}>Female</option>
                            <option value="other" {{ old('gender', $member->gender) == 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                        @error('gender')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Date of Birth -->
                    <div>
                        <label for="date_of_birth" class="block mb-2 text-sm font-medium text-gray-900">Date of Birth</label>
                        <input type="date" name="date_of_birth" id="date_of_birth" value="{{ old('date_of_birth', $member->date_of_birth ? $member->date_of_birth->format('Y-m-d') : '') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                        @error('date_of_birth')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Citizenship Number -->
                    <div>
                        <label for="citizenship_number" class="block mb-2 text-sm font-medium text-gray-900">Citizenship Number</label>
                        <input type="text" name="citizenship_number" id="citizenship_number" value="{{ old('citizenship_number', $member->citizenship_number) }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="Enter citizenship number">
                        @error('citizenship_number')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Profile Image Display Section -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Profile Image</label>
                        <div class="flex items-center space-x-4">
                            <div class="w-20 h-20 rounded-full overflow-hidden bg-gray-200 flex items-center justify-center">
                                @if($member->profile_image && file_exists(public_path('images/member-img/' . $member->profile_image)))
                                    <img src="{{ asset('images/member-img/' . $member->profile_image) }}" 
                                         alt="Profile Image" 
                                         class="w-full h-full object-cover">
                                @else
                                    <!-- Fallback Avatar with Initials -->
                                    <div class="w-full h-full bg-blue-500 flex items-center justify-center text-white font-semibold text-lg">
                                        {{ $member->avatar_initials }}
                                    </div>
                                @endif
                            </div>
                            <div>
                                <input type="file" 
                                       name="profile_image" 
                                       id="profile_image"
                                       accept="image/*"
                                       class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                <p class="text-xs text-gray-500 mt-1">PNG, JPG, GIF up to 2MB</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact Information -->
            <div class="bg-gray-50 p-4 rounded-lg">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Contact Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Primary Phone -->
                    <div>
                        <label for="phone" class="block mb-2 text-sm font-medium text-gray-900">Primary Phone Number *</label>
                        <input type="tel" name="phone" id="phone" value="{{ old('phone', $member->phone) }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="Enter primary phone number" required>
                        @error('phone')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Secondary Phone -->
                    <div>
                        <label for="phone_secondary" class="block mb-2 text-sm font-medium text-gray-900">Secondary Phone Number</label>
                        <input type="tel" name="phone_secondary" id="phone_secondary" value="{{ old('phone_secondary', $member->phone_secondary) }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="Enter secondary phone number">
                        @error('phone_secondary')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block mb-2 text-sm font-medium text-gray-900">Email</label>
                        <input type="email" name="email" id="email" value="{{ old('email', $member->email) }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="Enter email address">
                        @error('email')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Address -->
                    <div>
                        <label for="address" class="block mb-2 text-sm font-medium text-gray-900">Address *</label>
                        <textarea name="address" id="address" rows="3" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="Enter address" required>{{ old('address', $member->address) }}</textarea>
                        @error('address')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Branch and Family -->
            <div class="bg-gray-50 p-4 rounded-lg">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Branch & Family Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Branch -->
                    <div>
                        <label for="branch_id" class="block mb-2 text-sm font-medium text-gray-900">Branch *</label>
                        <select name="branch_id" id="branch_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required>
                            <option value="">Select Branch</option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}" {{ old('branch_id', $member->branch_id) == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                            @endforeach
                        </select>
                        @error('branch_id')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Family Members Multi-Select -->
                    <div>
                        <label for="family_members" class="block mb-2 text-sm font-medium text-gray-900">Family Members</label>
                        <div class="relative" id="family-members-container">
                            <!-- Hidden input to store selected values -->
                            <input type="hidden" name="family_members[]" id="family_members_input" value="">
                            
                            <!-- Multi-select display -->
                            <div class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus-within:ring-blue-500 focus-within:border-blue-500 block w-full min-h-[42px] p-2.5 cursor-pointer" id="family_members_display" onclick="toggleFamilyMembersDropdown()">
                                <div class="flex flex-wrap gap-2" id="selected_members_display">
                                    <!-- Selected members will be displayed here as tags -->
                                    <span class="text-gray-500 text-sm" id="placeholder_text">Select family members...</span>
                                </div>
                            </div>
                            
                            <!-- Dropdown -->
                            <div class="absolute z-50 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg hidden" id="family_members_dropdown">
                                <!-- Search input -->
                                <div class="p-3 border-b border-gray-200">
                                    <input type="text" id="family_search" placeholder="Search by name, member number, phone, or email..." class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                </div>
                                
                                <!-- Options list -->
                                <div class="max-h-60 overflow-y-auto" id="family_options_list">
                                    @foreach($existingMembers as $existingMember)
                                        @if($existingMember->id !== $member->id)
                                            <div class="family-option px-3 py-2 hover:bg-gray-100 cursor-pointer border-b border-gray-100 last:border-b-0" 
                                                 data-value="{{ $existingMember->id }}" 
                                                 data-name="{{ $existingMember->full_name }}" 
                                                 data-number="{{ $existingMember->member_number }}"
                                                 data-phone="{{ $existingMember->phone }}"
                                                 data-phone-secondary="{{ $existingMember->phone_secondary ?? '' }}"
                                                 data-email="{{ $existingMember->email ?? '' }}">
                                                <div class="flex items-center">
                                                    <input type="checkbox" class="mr-3 rounded border-gray-300 text-blue-600 focus:ring-blue-500" 
                                                           id="member_{{ $existingMember->id }}" 
                                                           {{ in_array($existingMember->id, old('family_members', $member->family_members ?? [])) ? 'checked' : '' }}>
                                                    <label for="member_{{ $existingMember->id }}" class="flex-1 cursor-pointer">
                                                        <div class="font-medium text-gray-900">{{ $existingMember->full_name }}</div>
                                                        <div class="text-sm text-gray-500">Member #{{ $existingMember->member_number }}</div>
                                                        @if($existingMember->phone)
                                                            <div class="text-xs text-gray-400">📞 {{ $existingMember->phone }}</div>
                                                        @endif
                                                        @if($existingMember->email)
                                                            <div class="text-xs text-gray-400">✉️ {{ $existingMember->email }}</div>
                                                        @endif
                                                    </label>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                                
                                <!-- Footer with actions -->
                                <div class="p-3 border-t border-gray-200 bg-gray-50 rounded-b-lg">
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm text-gray-600" id="selection_count">0 members selected</span>
                                        <div class="space-x-2">
                                            <button type="button" onclick="clearAllSelections()" class="text-sm text-gray-600 hover:text-gray-800">Clear All</button>
                                            <button type="button" onclick="selectAllMembers()" class="text-sm text-blue-600 hover:text-blue-800">Select All</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <p class="mt-1 text-sm text-gray-500">Select multiple family members from the dropdown</p>
                        @error('family_members')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Remove this entire Family Members Summary section -->
            <!-- Family Members Summary -->
            <!-- <div class="bg-gray-50 p-4 rounded-lg mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Selected Family Members</h3>
                <div id="family_members_summary">
                    <p class="text-gray-500 text-sm">No family members selected</p>
                </div>
            </div> -->

            <!-- KYC Documents -->
            <div class="bg-gray-50 p-4 rounded-lg">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">KYC Documents</h3>
                
                @if($member->kyc_documents && count($member->kyc_documents) > 0)
                    <div class="mb-4">
                        <h4 class="text-sm font-medium text-gray-900 mb-3">Current Documents ({{ count($member->kyc_documents) }}):</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($member->kyc_documents as $index => $filename)
                                <div class="relative bg-white border rounded-lg p-3 shadow-sm hover:shadow-md transition-shadow" data-index="{{ $index }}">
                                    <!-- Document Preview -->
                                    <div class="mb-3">
                                        @if($member->kycDocumentExists($filename))
                                            @if(str_ends_with(strtolower($filename), '.pdf'))
                                                <!-- PDF Preview -->
                                                <div class="w-full h-24 bg-red-50 rounded-lg flex items-center justify-center border border-red-200">
                                                    <div class="text-center">
                                                        <i class="fas fa-file-pdf text-red-500 text-2xl mb-1"></i>
                                                        <p class="text-xs text-red-600 font-medium">PDF</p>
                                                    </div>
                                                </div>
                                            @else
                                                <!-- Image Preview -->
                                                <div class="w-full h-24 bg-gray-100 rounded-lg overflow-hidden">
                                                    <img src="{{ asset('images/kyc-docs/' . $filename) }}" 
                                                         alt="KYC Document {{ $index + 1 }}" 
                                                         class="w-full h-full object-cover">
                                                </div>
                                            @endif
                                        @else
                                            <!-- File Not Found -->
                                            <div class="w-full h-24 bg-gray-100 rounded-lg flex items-center justify-center border border-gray-300">
                                                <div class="text-center">
                                                    <i class="fas fa-exclamation-triangle text-yellow-500 text-xl mb-1"></i>
                                                    <p class="text-xs text-gray-600">File Missing</p>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                    
                                    <!-- Document Info -->
                                    <div class="mb-3">
                                        <p class="text-sm font-medium text-gray-900">Document {{ $index + 1 }}</p>
                                    </div>
                                    
                                    <!-- Action Buttons -->
                                    <div class="flex items-center justify-between">
                                        @if($member->kycDocumentExists($filename))
                                            <a href="{{ asset('images/kyc-docs/' . $filename) }}" 
                                               target="_blank" 
                                               class="inline-flex items-center px-2 py-1 text-xs font-medium text-blue-600 bg-blue-50 rounded hover:bg-blue-100 transition-colors">
                                                <i class="fas fa-eye mr-1"></i>View
                                            </a>
                                        @else
                                            <span class="inline-flex items-center px-2 py-1 text-xs font-medium text-gray-400 bg-gray-100 rounded">
                                                <i class="fas fa-eye-slash mr-1"></i>Unavailable
                                            </span>
                                        @endif
                                        
                                        <button type="button" 
                                                class="delete-kyc-doc inline-flex items-center px-2 py-1 text-xs font-medium text-red-600 bg-red-50 rounded hover:bg-red-100 transition-colors"
                                                data-member-id="{{ $member->id }}"
                                                data-index="{{ $index }}"
                                                data-filename="Document {{ $index + 1 }}">
                                            <i class="fas fa-trash mr-1"></i>Delete
                                        </button>
                                    </div>
                                    
                                    <!-- Loading Overlay -->
                                    <div id="loading-{{ $index }}" class="absolute inset-0 bg-white bg-opacity-75 hidden items-center justify-center rounded-lg">
                                        <div class="flex items-center">
                                            <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-red-600 mr-2"></div>
                                            <span class="text-sm text-gray-600">Deleting...</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
                
                <!-- Upload New Documents -->
                <div>
                    <label for="kyc_documents" class="block mb-2 text-sm font-medium text-gray-900">Upload New Documents</label>
                    <input type="file" 
                           name="kyc_documents[]" 
                           id="kyc_documents" 
                           multiple 
                           accept=".pdf,.jpg,.jpeg,.png" 
                           class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    <p class="mt-1 text-sm text-gray-500">PDF, JPG, JPEG, PNG files (MAX. 2MB each). New files will be added to existing documents.</p>
                    @error('kyc_documents')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <script>
// Optimized KYC Documents functionality
document.addEventListener('DOMContentLoaded', function() {
    // Delete KYC document functionality
    document.addEventListener('click', function(e) {
        if (e.target.closest('.delete-kyc-doc')) {
            e.preventDefault();
            
            const button = e.target.closest('.delete-kyc-doc');
            const memberId = button.dataset.memberId;
            const index = button.dataset.index;
            const filename = button.dataset.filename;
            const documentCard = button.closest('[data-index]');
            const loadingOverlay = document.getElementById(`loading-${index}`);
            
            // Show confirmation dialog
            if (confirm(`Are you sure you want to delete "${filename}"? This action cannot be undone.`)) {
                // Show loading state
                if (loadingOverlay) {
                    loadingOverlay.classList.remove('hidden');
                    loadingOverlay.classList.add('flex');
                }
                
                // Disable button
                button.disabled = true;
                
                // Make delete request
                // Make delete request
                fetch(`/admin/members/${memberId}/kyc-document/${index}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                    },
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Hide loading state
                        if (loadingOverlay) {
                            loadingOverlay.classList.add('hidden');
                            loadingOverlay.classList.remove('flex');
                        }
                        
                        // Remove the document card with animation
                        documentCard.style.transition = 'all 0.3s ease';
                        documentCard.style.opacity = '0';
                        documentCard.style.transform = 'scale(0.95)';
                        
                        setTimeout(() => {
                            documentCard.remove();
                            
                            // Update document count or show message if no documents left
                            const remainingCards = document.querySelectorAll('[data-index]');
                            if (remainingCards.length === 0) {
                                const currentDocsSection = document.querySelector('.mb-4');
                                if (currentDocsSection) {
                                    currentDocsSection.remove();
                                }
                            }
                        }, 300);
                        
                        // Show success message
                        showNotification('Document deleted successfully', 'success');
                    } else {
                        // Hide loading state
                        if (loadingOverlay) {
                            loadingOverlay.classList.add('hidden');
                            loadingOverlay.classList.remove('flex');
                        }
                        
                        // Re-enable button
                        button.disabled = false;
                        
                        showNotification('Error: ' + data.message, 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    
                    // Hide loading state
                    if (loadingOverlay) {
                        loadingOverlay.classList.add('hidden');
                        loadingOverlay.classList.remove('flex');
                    }
                    
                    // Re-enable button
                    button.disabled = false;
                    
                    showNotification('An error occurred while deleting the document.', 'error');
                });
            }
        }
    });
    
    // Notification function
    function showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 px-4 py-2 rounded-lg text-white z-50 transition-all duration-300 ${
            type === 'success' ? 'bg-green-500' : 
            type === 'error' ? 'bg-red-500' : 'bg-blue-500'
        }`;
        notification.textContent = message;
        
        document.body.appendChild(notification);
        
        // Auto remove after 3 seconds
        setTimeout(() => {
            notification.style.opacity = '0';
            notification.style.transform = 'translateX(100%)';
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    }
});
</script>
            <!-- Add this section after the "Personal Information" section and before "Guardian Information" -->
            <div class="bg-gray-50 p-4 rounded-lg mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Family Members</h3>
                <div id="family_members_summary">
                    <p class="text-gray-500 text-sm">No family members selected</p>
                </div>
            </div>
            <!-- Guardian Information -->
            <div class="bg-gray-50 p-4 rounded-lg">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Guardian Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label for="guardian_name" class="block mb-2 text-sm font-medium text-gray-900">Guardian Name</label>
                        <input type="text" name="guardian_name" id="guardian_name" value="{{ old('guardian_name', $member->guardian_name) }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="Enter guardian name">
                    </div>
                    <div>
                        <label for="guardian_phone" class="block mb-2 text-sm font-medium text-gray-900">Guardian Phone</label>
                        <input type="tel" name="guardian_phone" id="guardian_phone" value="{{ old('guardian_phone', $member->guardian_phone) }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="Enter guardian phone">
                    </div>
                    <div>
                        <label for="guardian_relation" class="block mb-2 text-sm font-medium text-gray-900">Relation</label>
                        <input type="text" name="guardian_relation" id="guardian_relation" value="{{ old('guardian_relation', $member->guardian_relation) }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="Enter relation">
                    </div>
                </div>
            </div>

            <!-- Additional Information -->
            <div class="bg-gray-50 p-4 rounded-lg">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Additional Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label for="occupation" class="block mb-2 text-sm font-medium text-gray-900">Occupation</label>
                        <input type="text" name="occupation" id="occupation" value="{{ old('occupation', $member->occupation) }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="Enter occupation">
                    </div>
                    <div>
                        <label for="monthly_income" class="block mb-2 text-sm font-medium text-gray-900">Monthly Income</label>
                        <input type="number" name="monthly_income" id="monthly_income" step="0.01" value="{{ old('monthly_income', $member->monthly_income) }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="Enter monthly income">
                    </div>
                    <div>
                        <label for="membership_date" class="block mb-2 text-sm font-medium text-gray-900">Membership Date *</label>
                        <input type="date" name="membership_date" id="membership_date" value="{{ old('membership_date', $member->membership_date ? $member->membership_date->format('Y-m-d') : '') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                    <!-- Status -->
                    <div>
                        <label for="status" class="block mb-2 text-sm font-medium text-gray-900">Status *</label>
                        <select name="status" id="status" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required>
                            <option value="active" {{ old('status', $member->status) == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status', $member->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            <option value="suspended" {{ old('status', $member->status) == 'suspended' ? 'selected' : '' }}>Suspended</option>
                            <option value="kyc_pending" {{ old('status', $member->status) == 'kyc_pending' ? 'selected' : '' }}>KYC Pending</option>
                        </select>
                        @error('status')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- KYC Status -->
                    <div>
                        <label for="kyc_status" class="block mb-2 text-sm font-medium text-gray-900">KYC Status *</label>
                        <select name="kyc_status" id="kyc_status" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required>
                            <option value="pending" {{ old('kyc_status', $member->kyc_status) == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="verified" {{ old('kyc_status', $member->kyc_status) == 'verified' ? 'selected' : '' }}>Verified</option>
                            <option value="rejected" {{ old('kyc_status', $member->kyc_status) == 'rejected' ? 'selected' : '' }}>Rejected</option>
                        </select>
                        @error('kyc_status')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="flex items-center justify-end space-x-4">
                <a href="{{ route('admin.members.show', $member) }}" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg transition-colors duration-200">
                    Cancel
                </a>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition-colors duration-200">
                    <i class="fas fa-save mr-2"></i>Update Member
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const memberNumberTypeRadios = document.querySelectorAll('input[name="member_number_type"]');
    const autoGenerateSection = document.getElementById('auto-generate-section');
    const manualEntrySection = document.getElementById('manual-entry-section');
    const currentNumberDisplay = document.getElementById('current-number-display');
    const generatedNumberDisplay = document.getElementById('generated-number-display');
    const generateButton = document.getElementById('generate-member-number');
    const resetButton = document.getElementById('reset-member-number');
    const branchSelect = document.getElementById('branch_id');
    const originalMemberNumber = '{{ $member->member_number }}';
    
    // Function to enable/disable inputs based on mode
    function updateInputStates(mode) {
        const manualInput = document.getElementById('member_number_input');
        const autoInput = document.getElementById('member_number_auto');
        const generatedInput = document.getElementById('member_number_generated');
        
        // Disable all inputs first
        if (manualInput) manualInput.disabled = true;
        if (autoInput) autoInput.disabled = true;
        if (generatedInput) generatedInput.disabled = true;
        
        // Enable the appropriate input based on mode
        if (mode === 'manual') {
            if (manualInput) manualInput.disabled = false;
        } else if (mode === 'auto') {
            if (autoInput) autoInput.disabled = false;
        } else if (mode === 'generated') {
            if (generatedInput) generatedInput.disabled = false;
        }
    }
    
    // Initialize based on current state
    const currentMode = document.querySelector('input[name="member_number_type"]:checked').value;
    if (currentMode === 'auto') {
        updateInputStates('auto');
    } else {
        updateInputStates('manual');
    }
    
    // Handle member number type change
    memberNumberTypeRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            if (this.value === 'auto') {
                autoGenerateSection.classList.remove('hidden');
                manualEntrySection.classList.add('hidden');
                generatedNumberDisplay.classList.add('hidden');
                currentNumberDisplay.classList.remove('hidden');
                document.querySelector('input[name="member_number_auto_generated"]').value = '1';
                updateInputStates('auto');
            } else {
                autoGenerateSection.classList.add('hidden');
                manualEntrySection.classList.remove('hidden');
                generatedNumberDisplay.classList.add('hidden');
                currentNumberDisplay.classList.add('hidden');
                document.querySelector('input[name="member_number_auto_generated"]').value = '0';
                updateInputStates('manual');
            }
        });
    });
    
    // Generate member number
    generateButton.addEventListener('click', function() {
        const branchId = branchSelect.value;
        if (!branchId) {
            alert('Please select a branch first');
            return;
        }
        
        generateButton.disabled = true;
        generateButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Generating...';
        
        fetch(`/admin/members/generate-number/${branchId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('generated_number').value = data.member_number;
                    document.getElementById('member_number_generated').value = data.member_number;
                    generatedNumberDisplay.classList.remove('hidden');
                    currentNumberDisplay.classList.add('hidden');
                    updateInputStates('generated');
                    
                    // Show reset button AFTER generation
                    resetButton.classList.remove('hidden');
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error generating member number');
            })
            .finally(() => {
                generateButton.disabled = false;
                generateButton.innerHTML = '<i class="fas fa-magic mr-2"></i>Generate New Member Number';
            });
    });
    
    // Reset member number (only visible after generation)
    resetButton.addEventListener('click', function() {
        // Clear any generated number
        document.getElementById('generated_number').value = '';
        document.getElementById('member_number_generated').value = '';
        
        // Restore original member number in current display
        document.getElementById('current_member_number').value = originalMemberNumber;
        document.getElementById('member_number_auto').value = originalMemberNumber;
        
        // Hide generated number display and show current number display
        generatedNumberDisplay.classList.add('hidden');
        currentNumberDisplay.classList.remove('hidden');
        
        // Hide reset button again
        resetButton.classList.add('hidden');
        
        // Set to auto-generate mode
        document.querySelector('input[name="member_number_type"][value="auto"]').checked = true;
        document.querySelector('input[name="member_number_auto_generated"]').value = '1';
        
        // Ensure auto-generate section is visible
        autoGenerateSection.classList.remove('hidden');
        manualEntrySection.classList.add('hidden');
        
        // Update input states to auto mode
        updateInputStates('auto');
    });
});
</script>

<script>
// Family Members Multi-Select Functionality
let selectedMembers = new Map(); // Changed from Set to Map
let allMembers = [];

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    initializeFamilyMembersSelect();
});

function initializeFamilyMembersSelect() {
    // Collect all member data
    document.querySelectorAll('.family-option').forEach(option => {
        allMembers.push({
            id: option.dataset.value,
            name: option.dataset.name,
            number: option.dataset.number,
            phone: option.dataset.phone,
            phoneSecondary: option.dataset.phoneSecondary,
            email: option.dataset.email
        });
    });
    
    // Initialize with pre-selected values (for edit form)
    document.querySelectorAll('.family-option input[type="checkbox"]:checked').forEach(checkbox => {
        const option = checkbox.closest('.family-option');
        const memberData = {
            id: option.dataset.value,
            name: option.dataset.name,
            number: option.dataset.number,
            phone: option.dataset.phone,
            phoneSecondary: option.dataset.phoneSecondary,
            email: option.dataset.email
        };
        // Use member ID as key for reliable storage
        selectedMembers.set(memberData.id, memberData);
    });
    
    updateDisplay();
    setupEventListeners();
}

function setupEventListeners() {
    // Search functionality
    document.getElementById('family_search').addEventListener('input', function(e) {
        filterMembers(e.target.value);
    });
    
    // Checkbox change events
    document.querySelectorAll('.family-option input[type="checkbox"]').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const option = this.closest('.family-option');
            const memberData = {
                id: option.dataset.value,
                name: option.dataset.name,
                number: option.dataset.number,
                phone: option.dataset.phone,
                phoneSecondary: option.dataset.phoneSecondary,
                email: option.dataset.email
            };
            
            if (this.checked) {
                // Use member ID as key for reliable storage
                selectedMembers.set(memberData.id, memberData);
            } else {
                // Simple deletion by ID
                selectedMembers.delete(memberData.id);
            }
            
            updateDisplay();
        });
    });
    
    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (!document.getElementById('family-members-container').contains(e.target)) {
            closeFamilyMembersDropdown();
        }
    });
}

function toggleFamilyMembersDropdown() {
    const dropdown = document.getElementById('family_members_dropdown');
    dropdown.classList.toggle('hidden');
    
    if (!dropdown.classList.contains('hidden')) {
        document.getElementById('family_search').focus();
    }
}

function closeFamilyMembersDropdown() {
    document.getElementById('family_members_dropdown').classList.add('hidden');
}

function filterMembers(searchTerm) {
    const options = document.querySelectorAll('.family-option');
    const term = searchTerm.toLowerCase();
    
    options.forEach(option => {
        const name = option.dataset.name.toLowerCase();
        const number = option.dataset.number.toLowerCase();
        const phone = (option.dataset.phone || '').toLowerCase();
        const phoneSecondary = (option.dataset.phoneSecondary || '').toLowerCase();
        const email = (option.dataset.email || '').toLowerCase();
        
        if (name.includes(term) || 
            number.includes(term) || 
            phone.includes(term) || 
            phoneSecondary.includes(term) || 
            email.includes(term)) {
            option.style.display = 'block';
        } else {
            option.style.display = 'none';
        }
    });
}

function updateDisplay() {
    const displayContainer = document.getElementById('selected_members_display');
    const placeholderText = document.getElementById('placeholder_text');
    const selectionCount = document.getElementById('selection_count');
    const hiddenInput = document.getElementById('family_members_input');
    
    // Clear display
    displayContainer.innerHTML = '';
    
    if (selectedMembers.size === 0) {
        displayContainer.appendChild(placeholderText);
        hiddenInput.value = '';
    } else {
        // Hide placeholder
        if (placeholderText.parentNode) {
            placeholderText.remove();
        }
        
        // Create simple tags for selected members
        selectedMembers.forEach(member => {
            const tag = document.createElement('div');
            tag.className = 'inline-flex items-center bg-blue-100 text-blue-800 text-sm font-medium px-2.5 py-0.5 rounded-full mr-2 mb-2';
            tag.innerHTML = `
                <span class="mr-1">${member.name}</span>
                <button type="button" onclick="removeMember('${member.id}')" class="ml-1 text-blue-600 hover:text-blue-800">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                    </svg>
                </button>
            `;
            displayContainer.appendChild(tag);
        });
        
        // Update hidden inputs for form submission
        const selectedIds = Array.from(selectedMembers.keys());
        hiddenInput.value = selectedIds.join(',');
        
        // Create hidden inputs for form submission
        const existingHiddenInputs = document.querySelectorAll('input[name="family_members[]"]');
        existingHiddenInputs.forEach(input => input.remove());
        
        selectedIds.forEach(id => {
            const hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = 'family_members[]';
            hiddenInput.value = id;
            document.getElementById('family-members-container').appendChild(hiddenInput);
        });
    }
    
    // Update selection count
    selectionCount.textContent = `${selectedMembers.size} member${selectedMembers.size !== 1 ? 's' : ''} selected`;
}

// REMOVE THIS ENTIRE FUNCTION - it should be completely deleted
// function updateFamilyMembersSummary() {
//     const summaryContainer = document.getElementById('family_members_summary');
//     if (!summaryContainer) return;
//     
//     if (selectedMembers.size === 0) {
//         summaryContainer.innerHTML = '<p class="text-gray-500 text-sm">No family members selected</p>';
//     } else {
//         let summaryHTML = `
//             <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
//                 <h4 class="font-medium text-blue-900 mb-3">Selected Family Members (${selectedMembers.size})</h4>
//                 <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
//         `;
//         
//         selectedMembers.forEach(member => {
//             summaryHTML += `
//                 <div class="bg-white border border-blue-100 rounded-md p-3 hover:shadow-sm transition-shadow">
//                     <div class="font-medium text-gray-900">${member.name}</div>
//                     <div class="text-sm text-gray-600">Member #${member.number}</div>
//                     ${member.phone ? `<div class="text-xs text-gray-500 mt-1">📞 ${member.phone}</div>` : ''}
//                     ${member.email ? `<div class="text-xs text-gray-500">✉️ ${member.email}</div>` : ''}
//                     <button type="button" onclick="removeMember('${member.id}')" class="mt-2 text-xs text-red-600 hover:text-red-800 font-medium">
//                         Remove
//                     </button>
//                 </div>
//             `;
//         });
//         
//         summaryHTML += `
//                 </div>
//             </div>
//         `;
//         
//         summaryContainer.innerHTML = summaryHTML;
//     }
// }

function removeMember(memberId) {
    // Simple deletion by ID
    selectedMembers.delete(memberId);
    
    // Uncheck the corresponding checkbox
    const checkbox = document.getElementById(`member_${memberId}`);
    if (checkbox) {
        checkbox.checked = false;
    }
    
    updateDisplay();
}

function clearAllSelections() {
    selectedMembers.clear();
    document.querySelectorAll('.family-option input[type="checkbox"]').forEach(checkbox => {
        checkbox.checked = false;
    });
    updateDisplay();
}

function selectAllMembers() {
    document.querySelectorAll('.family-option').forEach(option => {
        if (option.style.display !== 'none') {
            const checkbox = option.querySelector('input[type="checkbox"]');
            checkbox.checked = true;
            
            const memberData = {
                id: option.dataset.value,
                name: option.dataset.name,
                number: option.dataset.number,
                phone: option.dataset.phone,
                phoneSecondary: option.dataset.phoneSecondary,
                email: option.dataset.email
            };
            selectedMembers.set(memberData.id, memberData);
        }
    });
    updateDisplay();
}
</script>
@endsection