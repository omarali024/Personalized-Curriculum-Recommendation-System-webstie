/**
 * Personalized Curriculum Recommendation System - Main JavaScript
 */

$(document).ready(function() {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Initialize popovers
    var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });

    // Auto-hide alerts after 5 seconds
    $('.alert').each(function() {
        var alert = $(this);
        if (!alert.hasClass('alert-permanent')) {
            setTimeout(function() {
                alert.fadeOut();
            }, 5000);
        }
    });

    // Smooth scrolling for anchor links
    $('a[href*="#"]:not([href="#"])').click(function() {
        if (location.pathname.replace(/^\//, '') == this.pathname.replace(/^\//, '') && location.hostname == this.hostname) {
            var target = $(this.hash);
            target = target.length ? target : $('[name=' + this.hash.slice(1) + ']');
            if (target.length) {
                $('html, body').animate({
                    scrollTop: target.offset().top - 80
                }, 1000);
                return false;
            }
        }
    });

    // Form validation enhancements
    $('form').on('submit', function() {
        var form = $(this);
        var submitBtn = form.find('button[type="submit"]');
        
        // Add loading state
        submitBtn.prop('disabled', true);
        submitBtn.html('<i class="fas fa-spinner fa-spin me-2"></i>Processing...');
        
        // Remove loading state after 3 seconds (fallback)
        setTimeout(function() {
            submitBtn.prop('disabled', false);
            submitBtn.html(submitBtn.data('original-text') || 'Submit');
        }, 3000);
    });

    // Store original button text
    $('button[type="submit"]').each(function() {
        $(this).data('original-text', $(this).html());
    });

    // Password strength indicator
    $('#new_password, #password').on('input', function() {
        var password = $(this).val();
        var strength = calculatePasswordStrength(password);
        var strengthBar = $(this).siblings('.password-strength');
        
        if (strengthBar.length === 0) {
            $(this).after('<div class="password-strength mt-2"></div>');
            strengthBar = $(this).siblings('.password-strength');
        }
        
        var strengthText = '';
        var strengthClass = '';
        
        if (strength < 25) {
            strengthText = 'Weak';
            strengthClass = 'danger';
        } else if (strength < 50) {
            strengthText = 'Fair';
            strengthClass = 'warning';
        } else if (strength < 75) {
            strengthText = 'Good';
            strengthClass = 'info';
        } else {
            strengthText = 'Strong';
            strengthClass = 'success';
        }
        
        strengthBar.html(`
            <div class="progress" style="height: 5px;">
                <div class="progress-bar bg-${strengthClass}" style="width: ${strength}%"></div>
            </div>
            <small class="text-${strengthClass}">${strengthText}</small>
        `);
    });

    // Search functionality
    $('#search').on('input', function() {
        var searchTerm = $(this).val().toLowerCase();
        var table = $(this).closest('.card').find('table');
        
        if (table.length) {
            table.find('tbody tr').each(function() {
                var row = $(this);
                var text = row.text().toLowerCase();
                
                if (text.includes(searchTerm)) {
                    row.show();
                } else {
                    row.hide();
                }
            });
        }
    });

    // Confirm delete actions
    $('a[href*="delete"], button[onclick*="delete"]').on('click', function(e) {
        if (!confirm('Are you sure you want to delete this item? This action cannot be undone.')) {
            e.preventDefault();
            return false;
        }
    });

    // Auto-save form data to localStorage
    $('form input, form textarea, form select').on('change', function() {
        var form = $(this).closest('form');
        var formId = form.attr('id') || form.attr('action') || 'form_' + Math.random().toString(36).substr(2, 9);
        var formData = form.serialize();
        localStorage.setItem('form_' + formId, formData);
    });

    // Restore form data from localStorage
    $('form').each(function() {
        var form = $(this);
        var formId = form.attr('id') || form.attr('action') || 'form_' + Math.random().toString(36).substr(2, 9);
        var savedData = localStorage.getItem('form_' + formId);
        
        if (savedData && !form.find('input[name="action"][value*="update"]').length) {
            // Only restore for new forms, not updates
            var params = new URLSearchParams(savedData);
            params.forEach(function(value, key) {
                var input = form.find('[name="' + key + '"]');
                if (input.length && !input.val()) {
                    input.val(value);
                }
            });
        }
    });

    // Clear form data on successful submission
    $('form').on('submit', function() {
        var form = $(this);
        var formId = form.attr('id') || form.attr('action') || 'form_' + Math.random().toString(36).substr(2, 9);
        
        // Clear after a delay to allow for success message
        setTimeout(function() {
            localStorage.removeItem('form_' + formId);
        }, 2000);
    });

    // Add animation classes on scroll
    $(window).on('scroll', function() {
        $('.fade-in, .slide-in-left, .slide-in-right').each(function() {
            var element = $(this);
            var elementTop = element.offset().top;
            var elementBottom = elementTop + element.outerHeight();
            var viewportTop = $(window).scrollTop();
            var viewportBottom = viewportTop + $(window).height();
            
            if (elementBottom > viewportTop && elementTop < viewportBottom) {
                element.addClass('animated');
            }
        });
    });

    // Initialize animations
    $(window).trigger('scroll');

    // Course recommendation refresh
    $('.refresh-recommendations').on('click', function() {
        var button = $(this);
        var originalText = button.html();
        
        button.prop('disabled', true);
        button.html('<i class="fas fa-spinner fa-spin me-2"></i>Generating...');
        
        // Simulate API call
        setTimeout(function() {
            button.prop('disabled', false);
            button.html(originalText);
            location.reload();
        }, 2000);
    });

    // Dynamic form field addition
    $('.add-field').on('click', function() {
        var template = $(this).data('template');
        var container = $(this).data('container');
        var fieldHtml = $('#' + template).html();
        
        $(container).append(fieldHtml);
        
        // Initialize new field
        $(container).find('.field-item').last().find('input, select, textarea').focus();
    });

    // Remove dynamic fields
    $(document).on('click', '.remove-field', function() {
        $(this).closest('.field-item').fadeOut(300, function() {
            $(this).remove();
        });
    });

    // Course search with autocomplete
    if ($('#course-search').length) {
        $('#course-search').on('input', function() {
            var searchTerm = $(this).val();
            
            if (searchTerm.length >= 2) {
                $.ajax({
                    url: 'ajax/search_courses.php',
                    method: 'GET',
                    data: { q: searchTerm },
                    success: function(data) {
                        var results = JSON.parse(data);
                        var dropdown = $('#course-dropdown');
                        
                        dropdown.empty();
                        
                        if (results.length > 0) {
                            results.forEach(function(course) {
                                dropdown.append(`
                                    <div class="dropdown-item course-item" data-id="${course.id}">
                                        <strong>${course.name}</strong><br>
                                        <small class="text-muted">${course.category} - ${course.difficulty}</small>
                                    </div>
                                `);
                            });
                            dropdown.show();
                        } else {
                            dropdown.hide();
                        }
                    }
                });
            } else {
                $('#course-dropdown').hide();
            }
        });
    }

    // Course selection
    $(document).on('click', '.course-item', function() {
        var courseId = $(this).data('id');
        var courseName = $(this).find('strong').text();
        
        $('#course-search').val(courseName);
        $('#selected-course-id').val(courseId);
        $('#course-dropdown').hide();
    });

    // Hide dropdown when clicking outside
    $(document).on('click', function(e) {
        if (!$(e.target).closest('#course-search, #course-dropdown').length) {
            $('#course-dropdown').hide();
        }
    });

    // Print functionality
    $('.print-btn').on('click', function() {
        window.print();
    });

    // Export functionality
    $('.export-btn').on('click', function() {
        var format = $(this).data('format');
        var table = $('.table');
        
        if (format === 'csv') {
            exportTableToCSV(table, 'courses.csv');
        } else if (format === 'excel') {
            exportTableToExcel(table, 'courses.xlsx');
        }
    });
});

// Password strength calculation
function calculatePasswordStrength(password) {
    var strength = 0;
    
    if (password.length >= 6) strength += 20;
    if (password.length >= 8) strength += 10;
    if (password.length >= 12) strength += 10;
    
    if (/[a-z]/.test(password)) strength += 10;
    if (/[A-Z]/.test(password)) strength += 10;
    if (/[0-9]/.test(password)) strength += 10;
    if (/[^A-Za-z0-9]/.test(password)) strength += 20;
    
    return Math.min(strength, 100);
}

// Export table to CSV
function exportTableToCSV(table, filename) {
    var csv = [];
    var rows = table.find('tr');
    
    rows.each(function() {
        var row = [];
        $(this).find('th, td').each(function() {
            var text = $(this).text().replace(/"/g, '""');
            row.push('"' + text + '"');
        });
        csv.push(row.join(','));
    });
    
    var csvContent = csv.join('\n');
    var blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    var link = document.createElement('a');
    
    if (link.download !== undefined) {
        var url = URL.createObjectURL(blob);
        link.setAttribute('href', url);
        link.setAttribute('download', filename);
        link.style.visibility = 'hidden';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }
}

// Show notification
function showNotification(message, type = 'info') {
    var alertClass = 'alert-' + type;
    var iconClass = type === 'success' ? 'fa-check-circle' : 
                   type === 'error' ? 'fa-exclamation-circle' : 
                   type === 'warning' ? 'fa-exclamation-triangle' : 'fa-info-circle';
    
    var notification = $(`
        <div class="alert ${alertClass} alert-dismissible fade show position-fixed" 
             style="top: 20px; right: 20px; z-index: 9999; min-width: 300px;">
            <i class="fas ${iconClass} me-2"></i>${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `);
    
    $('body').append(notification);
    
    setTimeout(function() {
        notification.fadeOut();
    }, 5000);
}

// Loading overlay
function showLoading() {
    if ($('#loading-overlay').length === 0) {
        $('body').append(`
            <div id="loading-overlay" class="position-fixed w-100 h-100 d-flex align-items-center justify-content-center" 
                 style="background: rgba(0,0,0,0.5); z-index: 9999;">
                <div class="text-center text-white">
                    <i class="fas fa-spinner fa-spin fa-3x mb-3"></i>
                    <p>Loading...</p>
                </div>
            </div>
        `);
    }
}

function hideLoading() {
    $('#loading-overlay').remove();
}

// Utility function to format date
function formatDate(dateString) {
    var date = new Date(dateString);
    return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
}

// Utility function to truncate text
function truncateText(text, maxLength) {
    if (text.length <= maxLength) return text;
    return text.substr(0, maxLength) + '...';
}

// Initialize when DOM is ready
$(document).ready(function() {
    // Add any additional initialization code here
    
    // Example: Initialize data tables if present
    if ($.fn.DataTable) {
        $('.data-table').DataTable({
            responsive: true,
            pageLength: 25,
            order: [[0, 'asc']],
            language: {
                search: "Search:",
                lengthMenu: "Show _MENU_ entries",
                info: "Showing _START_ to _END_ of _TOTAL_ entries",
                paginate: {
                    first: "First",
                    last: "Last",
                    next: "Next",
                    previous: "Previous"
                }
            }
        });
    }
});
