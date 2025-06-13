jQuery(document).ready(function($) {
    'use strict';
    
    // Mobile menu functionality
    $('#mobile-menu-button').on('click', function() {
        $('#mobile-menu').toggleClass('hidden');
    });
    
    // Novel search functionality
    let searchTimeout;
    $('#novel-search').on('input', function() {
        clearTimeout(searchTimeout);
        const query = $(this).val();
        
        if (query.length < 2) {
            $('#search-results').hide();
            return;
        }
        
        searchTimeout = setTimeout(function() {
            searchNovels(query);
        }, 300);
    });
    
    function searchNovels(query) {
        $.ajax({
            url: novelreader_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'search_novels',
                search: query,
                nonce: novelreader_ajax.nonce
            },
            success: function(response) {
                if (response.success) {
                    displaySearchResults(response.data);
                }
            }
        });
    }
    
    function displaySearchResults(results) {
        const $resultsContainer = $('#search-results');
        $resultsContainer.empty();
        
        if (results.length === 0) {
            $resultsContainer.html('<div class="p-4 text-gray-500">No novels found</div>');
        } else {
            results.forEach(function(novel) {
                const $item = $('<a>', {
                    href: '/novel/' + novel.id,
                    class: 'block p-4 hover:bg-gray-50 border-b border-gray-200',
                    text: novel.title
                });
                $resultsContainer.append($item);
            });
        }
        
        $resultsContainer.show();
    }
    
    // Hide search results when clicking outside
    $(document).on('click', function(e) {
        if (!$(e.target).closest('#novel-search, #search-results').length) {
            $('#search-results').hide();
        }
    });
    
    // Bookmark functionality
    window.toggleBookmark = function(novelId) {
        if (!novelreader_ajax.user_logged_in) {
            alert('Please log in to bookmark novels.');
            return;
        }
        
        $.ajax({
            url: novelreader_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'toggle_bookmark',
                novel_id: novelId,
                nonce: novelreader_ajax.nonce
            },
            success: function(response) {
                if (response.success) {
                    const $button = $('[onclick="toggleBookmark(' + novelId + ')"]');
                    if (response.data.bookmarked) {
                        $button.addClass('bg-yellow-500 text-white').removeClass('border-gray-300 text-gray-700');
                        $button.find('svg').attr('fill', 'currentColor');
                    } else {
                        $button.removeClass('bg-yellow-500 text-white').addClass('border-gray-300 text-gray-700');
                        $button.find('svg').attr('fill', 'none');
                    }
                }
            }
        });
    };
    
    // Chapter bookmark functionality
    window.bookmarkChapter = function(chapterId) {
        if (!novelreader_ajax.user_logged_in) {
            alert('Please log in to bookmark chapters.');
            return;
        }
        
        $.ajax({
            url: novelreader_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'bookmark_chapter',
                chapter_id: chapterId,
                nonce: novelreader_ajax.nonce
            },
            success: function(response) {
                if (response.success) {
                    alert('Chapter bookmarked!');
                }
            }
        });
    };
    
    // Infinite scroll for novel archive
    if ($('.novels-archive').length) {
        let loading = false;
        let page = 2;
        
        $(window).on('scroll', function() {
            if (loading) return;
            
            if ($(window).scrollTop() + $(window).height() >= $(document).height() - 1000) {
                loading = true;
                loadMoreNovels();
            }
        });
        
        function loadMoreNovels() {
            $.ajax({
                url: novelreader_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'load_more_novels',
                    page: page,
                    nonce: novelreader_ajax.nonce
                },
                success: function(response) {
                    if (response.success && response.data.html) {
                        $('#novels-grid').append(response.data.html);
                        page++;
                        loading = false;
                    } else {
                        // No more posts
                        $(window).off('scroll');
                    }
                },
                error: function() {
                    loading = false;
                }
            });
        }
    }
    
    // Reading progress tracking
    if ($('#chapter-content').length) {
        let progressTimer;
        
        $(window).on('scroll', function() {
            clearTimeout(progressTimer);
            progressTimer = setTimeout(updateReadingProgress, 1000);
        });
        
        function updateReadingProgress() {
            const scrollTop = $(window).scrollTop();
            const docHeight = $(document).height() - $(window).height();
            const progress = Math.round((scrollTop / docHeight) * 100);
            
            if (progress > 10) { // Only track if user has read at least 10%
                $.ajax({
                    url: novelreader_ajax.ajax_url,
                    type: 'POST',
                    data: {
                        action: 'update_reading_progress',
                        chapter_id: novelreader_ajax.chapter_id,
                        progress: progress,
                        nonce: novelreader_ajax.nonce
                    }
                });
            }
        }
    }
    
    // Footnotes functionality
    function initializeFootnotes() {
        const $content = $('#chapter-content');
        const $footnotesList = $('#footnotes-list');
        
        // Find footnote references in content
        $content.find('[data-footnote]').each(function(index) {
            const footnoteText = $(this).data('footnote');
            const footnoteId = 'footnote-' + (index + 1);
            
            // Add clickable footnote number
            $(this).after('<sup class="footnote-ref"><a href="#' + footnoteId + '">[' + (index + 1) + ']</a></sup>');
            
            // Add footnote to list
            $footnotesList.append(
                '<div id="' + footnoteId + '" class="footnote">' +
                '<span class="footnote-number">' + (index + 1) + '.</span> ' +
                '<span class="footnote-text">' + footnoteText + '</span>' +
                '</div>'
            );
        });
        
        // Smooth scroll to footnotes
        $('.footnote-ref a').on('click', function(e) {
            e.preventDefault();
            const target = $($(this).attr('href'));
            if (target.length) {
                $('html, body').animate({
                    scrollTop: target.offset().top - 100
                }, 500);
            }
        });
    }
    
    // Initialize footnotes if chapter content exists
    if ($('#chapter-content').length) {
        initializeFootnotes();
    }
    
    // Keyboard navigation for chapters
    $(document).on('keydown', function(e) {
        if ($('body').hasClass('single-chapter')) {
            if (e.key === 'ArrowLeft' && $('.prev-chapter').length) {
                window.location = $('.prev-chapter').attr('href');
            } else if (e.key === 'ArrowRight' && $('.next-chapter').length) {
                window.location = $('.next-chapter').attr('href');
            }
        }
    });
    
    // Auto-save reading position
    if ($('#chapter-content').length) {
        setInterval(function() {
            const scrollPosition = $(window).scrollTop();
            localStorage.setItem('reading-position-' + novelreader_ajax.chapter_id, scrollPosition);
        }, 5000);
        
        // Restore reading position
        const savedPosition = localStorage.getItem('reading-position-' + novelreader_ajax.chapter_id);
        if (savedPosition) {
            $(window).scrollTop(parseInt(savedPosition));
        }
    }
});