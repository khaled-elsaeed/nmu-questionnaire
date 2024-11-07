$(document).ready(function() {
    // Function to export files
    function exportFile(button, url, filename) {
        const originalText = button.html(); // Store original button text
        button.html('<i class="fa fa-spinner fa-spin"></i> Downloading...'); // Change button text and add spinner
        button.addClass('loading'); // Disable button interactions

        // Get the CSRF token from a meta tag
        const csrfToken = $('meta[name="csrf-token"]').attr('content');

        // Make the fetch request
        fetch(url, {
            method: 'GET', // Use GET request
            headers: {
                'X-Requested-With': 'XMLHttpRequest', // Indicate an AJAX request
                'X-CSRF-Token': csrfToken // Include CSRF token for security
            }
        })
        .then(response => {
            if (response.ok) {
                return response.blob(); // Get the response as a Blob
            }
            throw new Error('Network response was not ok.');
        })
        .then(blob => {
            const url = window.URL.createObjectURL(blob); // Create a URL for the Blob
            const a = document.createElement('a'); // Create an anchor element
            a.style.display = 'none';
            a.href = url;
            a.download = filename; // Set the desired filename
            document.body.appendChild(a); // Append the anchor to the body
            a.click(); // Trigger the download
            a.remove(); // Clean up: remove the anchor element from the DOM
            window.URL.revokeObjectURL(url); // Clean up the URL object
        })
        .catch(error => {
            console.error('There was a problem with the fetch operation:', error);
            alert('Error downloading the file. Please try again.'); // User feedback
        })
        .finally(() => {
            button.html(originalText); // Reset the button text to original
            button.removeClass('loading'); // Enable button interactions
        });
    }

    // Attach the click event to export buttons using the dynamic routes
    $('#exportExcel').on('click', function(e) {
        e.preventDefault(); // Prevent default action
        exportFile($('#downloadButton'), window.routes.exportExcel, 'applicants.xlsx'); // Use dynamic URL
    });

    $('#exportPDF').on('click', function(e) {
        e.preventDefault(); // Prevent default action
        exportFile($('#downloadButton'), window.routes.exportPdf, 'applicants.pdf'); // Use dynamic URL
    });

    // Attach click events for applicant actions
    $('#email-btn').on('click', function() {
        sendEmail();
    });

    $('#reset-password-btn').on('click', function() {
        resetPassword();
    });

    $('#delete-btn').on('click', function() {
        deleteApplication();
    });

    $('#details-btn').on('click', function() {
        showMoreDetails();
    });

    // Function to send an email
    function sendEmail() {
        alert('Send email functionality is triggered.');
        // Implement your email sending logic here
    }

    // Function to reset the password
    function resetPassword() {
        let confirmReset = confirm('Are you sure you want to reset the password?');
        if (confirmReset) {
            alert('Password reset functionality is triggered.');
            // Implement your password reset logic here
        }
    }

    // Function to delete the application
    function deleteApplication() {
        let confirmDelete = confirm('Are you sure you want to delete this application?');
        if (confirmDelete) {
            alert('Delete application functionality is triggered.');
            // Implement your application deletion logic here
        }
    }

    // Function to show more details
    function showMoreDetails() {
        alert('Show more details functionality is triggered.');
        // You can implement a modal or a new page to show details here
    }
});
