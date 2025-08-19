@props([
    'name' => 'date',
    'id' => null,
    'value' => '',
    'placeholder' => 'Select Date',
    'required' => false,
    'class' => '',
    'dateFormat' => '%y-%m-%d',
    'closeOnDateSelect' => true,
    'minDate' => null,
    'maxDate' => null,
    'label' => null
])

@php
    $id = $id ?? $name;
    $inputClasses = 'nepali-date-picker-input block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm ' . $class;
    $currentValue = old($name, $value);
    
    // Display current value in English format
$currentValueFormatted = null;
if ($currentValue) {
    try {
        $currentValueFormatted = \Carbon\Carbon::parse($currentValue)->format('M d, Y');
    } catch (Exception $e) {
        $currentValueFormatted = $currentValue;
    }
}
@endphp

<!-- Hidden input to preserve current value if no new date is selected -->
@if($currentValue)
    <input type="hidden" name="{{ $name }}_current" value="{{ $currentValue }}" />
@endif

<div class="nepali-date-picker-container flex items-center space-x-3">
    <!-- Current Value Display (20% width) - Read Only -->
    @if($currentValue)
        <div class="current-value-display w-1/5 min-w-0">
            <div class="text-xs text-gray-500 mb-1">{{ $label ? $label . ' (Current)' : 'Current Value' }}</div>
            <div class="px-3 py-2 bg-gray-50 border border-gray-200 rounded-md text-sm text-gray-700 truncate" title="{{ $currentValueFormatted ?: $currentValue }}">
            {{ $currentValueFormatted ?: $currentValue }}
            </div>
        </div>
    @endif
    
    <!-- Date Picker Input (80% or full width if no current value) - This will be submitted -->
    <div class="{{ $currentValue ? 'w-4/5' : 'w-full' }}">
        @if($currentValue)
            <div class="text-xs text-gray-500 mb-1">{{ $label ? 'Update ' . $label : 'Update Date' }}</div>
        @endif
        <input
            type="text"
            name="{{ $name }}"
            id="{{ $id }}"
            value=""
            placeholder="{{ $placeholder }}"
            class="{{ $inputClasses }}"
            @if($required) required @endif
            {{ $attributes }}
        />
    </div>
</div>

@once
    @push('styles')
        <style>
            .nepali-date-picker-input {
                background-color: white;
                cursor: pointer;
            }
            .calendar-popup {
                z-index: 9999 !important;
                box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
                border-radius: 0.375rem;
            }
            .nepali-date-picker-container {
                min-height: 2.5rem;
            }
            .current-value-display {
                flex-shrink: 0;
            }
            .current-value-display .truncate {
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
            }
            
            /* Responsive design for smaller screens */
            @media (max-width: 640px) {
                .nepali-date-picker-container {
                    flex-direction: column;
                    space-x: 0;
                    gap: 0.75rem;
                }
                .current-value-display {
                    width: 100% !important;
                }
                .current-value-display + div {
                    width: 100% !important;
                }
            }
        </style>
    @endpush

    @push('scripts')
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Wait for all scripts to load
            function initializeNepaliDatePickers() {
                console.log('Initializing Nepali date pickers...');
                
                if (typeof window.jQuery === 'undefined') {
                    console.error('jQuery is not loaded');
                    return;
                }
                
                if (typeof window.jQuery.fn.nepaliDatePicker === 'undefined') {
                    console.error('nepaliDatePicker plugin is not loaded');
                    return;
                }
                
                jQuery('.nepali-date-picker-input').each(function() {
                    const $element = jQuery(this);
                    const elementId = $element.attr('id');
                    
                    // Skip if already initialized
                    if ($element.data('nepali-initialized')) {
                        console.log('Date picker already initialized for:', elementId);
                        return;
                    }
                    
                    try {
                        console.log('Initializing date picker for:', elementId);
                        
                        // Initialize the date picker with empty value (for new updates only)
                         $element.val('');
                         
                         // Initialize the date picker with minimal configuration
                          $element.nepaliDatePicker({
                              dateFormat: '%y-%m-%d',
                              closeOnDateSelect: true,
                              ndpYear: true,
                              ndpMonth: true,
                              ndpYearCount: 10,
                              disableAfter: "2090-12-30",
                              disableBefore: "1970-01-01",
                              language: 'english'
                          });
                        
                        // Mark as initialized
                        $element.data('nepali-initialized', true);
                        console.log('Successfully initialized date picker for:', elementId);
                        
                    } catch (error) {
                        console.error('Error initializing date picker for', elementId, ':', error);
                        
                        // Fallback: try without calendarType
                         try {
                             console.log('Trying fallback initialization for:', elementId);
                             
                             // Keep value empty for fallback too
                             $element.val('');
                             
                             $element.nepaliDatePicker({
                                  dateFormat: '%y-%m-%d',
                                  closeOnDateSelect: true,
                                  ndpYear: true,
                                  ndpMonth: true,
                                  language: 'english'
                              });
                            
                            $element.data('nepali-initialized', true);
                            console.log('Fallback initialization successful for:', elementId);
                        } catch (fallbackError) {
                            console.error('Fallback initialization also failed for', elementId, ':', fallbackError);
                        }
                    }
                });
            }
            
            // Start initialization
            initializeNepaliDatePickers();
            
            // Handle form submission to use current value if date picker is empty
            jQuery(document).on('submit', 'form', function(e) {
                jQuery('.nepali-date-picker-input').each(function() {
                    const $datePicker = jQuery(this);
                    const fieldName = $datePicker.attr('name');
                    const $currentValueInput = jQuery('input[name="' + fieldName + '_current"]');
                    
                    // If date picker is empty but we have a current value, use the current value
                    if ((!$datePicker.val() || $datePicker.val().trim() === '') && $currentValueInput.length && $currentValueInput.val()) {
                        $datePicker.val($currentValueInput.val());
                    }
                });
            });
        });
        </script>
    @endpush
@endonce