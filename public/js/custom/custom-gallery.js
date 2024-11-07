"use strict";
$(document).ready(function() {
    const itemsPerPage = 8; // Set how many items per page
    let currentPage = 1;
    let $gridItems = $('.grid-item');
    let totalItems = $gridItems.length;
    let totalPages = Math.ceil(totalItems / itemsPerPage);

    // Function to calculate total pages
    function calculateTotalPages() {
        totalItems = $gridItems.length; // Update to reflect all items
        totalPages = Math.ceil(totalItems / itemsPerPage);
        console.log(`Total Items: ${totalItems}, Total Pages: ${totalPages}`);
    }

    // Function to show items based on the current page
    function showPage(page) {
        calculateTotalPages(); // Recalculate total pages each time a page is shown

        // Correct current page if it goes beyond total pages
        if (page > totalPages) {
            currentPage = totalPages;
        } else {
            currentPage = page;
        }

        let start = (currentPage - 1) * itemsPerPage;
        let end = start + itemsPerPage;

        // Hide all items initially
        $gridItems.hide();

        // Show only the items for the current page
        $gridItems.slice(start, end).show();

        // Update pagination with accurate page count
        updatePagination();
    }

    // Function to update pagination buttons with only 4 numbers visible
    function updatePagination() {
        const $pagination = $('.pagination');

        // Clear existing page numbers
        $pagination.find('.page-item').not(':first-child, :last-child').remove();
        console.log(`Current Page: ${currentPage}, Total Pages: ${totalPages}`);

        // Update Previous button
        const $prevButton = $pagination.find('.page-item:first-child');
        if (currentPage === 1) {
            $prevButton.addClass('disabled').off('click'); // Fully disable
        } else {
            $prevButton.removeClass('disabled').off('click').on('click', function(e) {
                e.preventDefault();
                currentPage--;
                showPage(currentPage);
            });
        }

        // Calculate the range of pages to show
        let startPage = Math.max(1, currentPage - 1);
        let endPage = Math.min(totalPages, currentPage + 2);

        // Adjust the range to ensure exactly 4 pages are displayed whenever possible
        if (endPage - startPage < 3) {
            if (startPage === 1) {
                endPage = Math.min(4, totalPages);
            } else if (endPage === totalPages) {
                startPage = Math.max(1, totalPages - 3);
            }
        }

        // Dynamically create and insert only 4 page numbers based on range
        for (let pageNum = startPage; pageNum <= endPage; pageNum++) {
            const $pageItem = $('<li class="page-item"><a class="page-link" href="#"></a></li>');
            $pageItem.find('a').text(pageNum).off('click').on('click', function(e) {
                e.preventDefault();
                currentPage = pageNum;
                showPage(currentPage);
            });

            // Highlight the active page number
            if (pageNum === currentPage) {
                $pageItem.addClass('active');
                $pageItem.find('a').attr('tabindex', '-1').removeAttr('href');
            }

            // Insert page numbers between Previous and Next buttons
            $pagination.find('.page-item:last-child').before($pageItem);
        }

        // Update Next button
        const $nextButton = $pagination.find('.page-item:last-child');
        if (currentPage >= totalPages) {
            $nextButton.addClass('disabled').off('click'); // Fully disable
        } else {
            $nextButton.removeClass('disabled').off('click').on('click', function(e) {
                e.preventDefault();
                currentPage++;
                showPage(currentPage);
            });
        }
    }

    // Initial pagination setup
    function initializePagination() {
        $('.pagination').html(`
            <li class="page-item disabled">
                <a class="page-link" href="#" tabindex="-1">Previous</a>
            </li>
            <li class="page-item">
                <a class="page-link" href="#">Next</a>
            </li>
        `);
        showPage(currentPage); // Show the first page on load
    }

    initializePagination(); // Run on page load
});
