<?php

/**
 * Polylang String Registration for OMSAR Theme
 * 
 * This file contains all translatable strings used throughout the OMSAR theme.
 * Strings are organized by sections for better maintainability.
 * 
 * Note: This file only runs if Polylang plugin is installed and active.
 */

// Only register strings if Polylang is available
if (!function_exists('pll_register_string')) {
    return; // Exit early if Polylang is not available
}

// Register strings only after Polylang is loaded
add_action('init', 'omsar_register_polylang_strings', 20);

function omsar_register_polylang_strings() {
    // Double check Polylang is available
    if (!function_exists('pll_register_string')) {
        return;
    }

    pll_register_string('Read More', 'Read More', 'omsar', false);
    pll_register_string('Upcoming', 'Upcoming', 'omsar', false);
    pll_register_string('Notify me', 'Notify me', 'omsar', false);

    pll_register_string('Month', 'Month', 'omsar', false);
    pll_register_string('Year', 'Year', 'omsar', false);
    pll_register_string('January', 'January', 'omsar', false);
    pll_register_string('February', 'February', 'omsar', false);
    pll_register_string('March', 'March', 'omsar', false);
    pll_register_string('April', 'April', 'omsar', false);
    pll_register_string('May', 'May', 'omsar', false);
    pll_register_string('June', 'June', 'omsar', false);
    pll_register_string('July', 'July', 'omsar', false);
    pll_register_string('August', 'August', 'omsar', false);
    pll_register_string('September', 'September', 'omsar', false);
    pll_register_string('October', 'October', 'omsar', false);
    pll_register_string('November', 'November', 'omsar', false);
    pll_register_string('December', 'December', 'omsar', false);

    // Partnership Form Strings
    pll_register_string('Inquire About Our Partnership Program', 'Inquire About Our Partnership Program', 'omsar', false);
    pll_register_string('Full Name', 'Full Name', 'omsar', false);
    pll_register_string('Enter your full name', 'Enter your full name', 'omsar', false);
    pll_register_string('Email Address', 'Email Address', 'omsar', false);
    pll_register_string('Enter your email address', 'Enter your email address', 'omsar', false);
    pll_register_string('Phone Number', 'Phone Number', 'omsar', false);
    pll_register_string('Enter your phone number', 'Enter your phone number', 'omsar', false);
    pll_register_string('Website URL', 'Website URL', 'omsar', false);
    pll_register_string('Address', 'Address', 'omsar', false);
    pll_register_string('Enter your address', 'Enter your address', 'omsar', false);
    pll_register_string('Notes', 'Notes', 'omsar', false);
    pll_register_string('Tell us about your partnership interest...', 'Tell us about your partnership interest...', 'omsar', false);
    pll_register_string('Submit', 'Submit', 'omsar', false);


    pll_register_string('All', 'All', 'omsar', false);
    pll_register_string('No posts found matching your filters.', 'No posts found matching your filters.', 'omsar', false);

    // Recruitments Listing Page Strings
    pll_register_string('Open', 'Open', 'omsar', false);
    pll_register_string('Closed', 'Closed', 'omsar', false);
    pll_register_string('Cancelled', 'Cancelled', 'omsar', false);
    pll_register_string('Opening Date', 'Opening Date', 'omsar', false);
    pll_register_string('Closing Date', 'Closing Date', 'omsar', false);
    pll_register_string('Entity', 'Entity', 'omsar', false);
    pll_register_string('Apply', 'Apply', 'omsar', false);
    pll_register_string('No recruitments found.', 'No recruitments found.', 'omsar', false);
    pll_register_string('Load More', 'Load More', 'omsar', false);
    pll_register_string('Loading...', 'Loading...', 'omsar', false);
    pll_register_string('Filter by status', 'Filter by status', 'omsar', false);
    pll_register_string('Filter by:', 'Filter by:', 'omsar', false);

    // Procurement Notices Single Page Strings
    pll_register_string('Reference No.', 'Reference No.', 'omsar', false);
    pll_register_string('Notice Type', 'Notice Type', 'omsar', false);
    pll_register_string('Dates', 'Dates', 'omsar', false);
    pll_register_string('Description', 'Description', 'omsar', false);
    pll_register_string('Documents', 'Documents', 'omsar', false);
    pll_register_string('Download', 'Download', 'omsar', false);
    pll_register_string('Download Document', 'Download Document', 'omsar', false);

    // Procurement Apply Modal
    pll_register_string('Close', 'Close', 'omsar', false);
    pll_register_string('Enter your email to apply for this procurement notice.', 'Enter your email to apply for this procurement notice.', 'omsar', false);
    pll_register_string('Thank you. Your application has been submitted successfully.', 'Thank you. Your application has been submitted successfully.', 'omsar', false);
    pll_register_string('Please enter your email.', 'Please enter your email.', 'omsar', false);
    pll_register_string('Please enter a valid email address.', 'Please enter a valid email address.', 'omsar', false);
    pll_register_string('You have already applied for this procurement notice with this email address.', 'You have already applied for this procurement notice with this email address.', 'omsar', false);
    pll_register_string('An error occurred. Please try again.', 'An error occurred. Please try again.', 'omsar', false);
    pll_register_string('No procurement notices are available at this time. Please check back later.', 'No procurement notices are available at this time. Please check back later.', 'omsar', false);
    pll_register_string('No application link is set for this notice. Please add a URL in the procurement link field.', 'No application link is set for this notice. Please add a URL in the procurement link field.', 'omsar', false);
    // Procurement application email
    pll_register_string('New procurement application for: %s', 'New procurement application for: %s', 'omsar', false);
    pll_register_string('New Procurement Application', 'New Procurement Application', 'omsar', false);
    pll_register_string('A new application has been submitted for the following procurement notice.', 'A new application has been submitted for the following procurement notice.', 'omsar', false);
    pll_register_string('Procurement:', 'Procurement:', 'omsar', false);
    pll_register_string('Applicant email:', 'Applicant email:', 'omsar', false);
    pll_register_string('Submission date:', 'Submission date:', 'omsar', false);
    pll_register_string('No knowledge and resources are available at this time.', 'No knowledge and resources are available at this time.', 'omsar', false);
    pll_register_string('Search posts...', 'Search posts...', 'omsar', false);
    pll_register_string('No results found.', 'No results found.', 'omsar', false);
    pll_register_string('Error loading search results. Please try again.', 'Error loading search results. Please try again.', 'omsar', false);
    pll_register_string('Knowledge Center and Resources', 'Knowledge Center and Resources', 'omsar', false);


    // chatbot
    pll_register_string('OMSAR ChatBot', 'OMSAR ChatBot', 'omsar', false);
    pll_register_string('Your Intelligent Digital Assistant', 'Your Intelligent Digital Assistant', 'omsar', false);
    

    pll_register_string('Page Not Found', 'Page Not Found', 'omsar', false);
    pll_register_string('Sorry, the page you are looking for does not exist or has been moved', 'Sorry, the page you are looking for does not exist or has been moved', 'omsar', false);


    // Ministers
    pll_register_string('Government', 'Government', 'omsar', false);
    pll_register_string('Assignment Period', 'Assignment Period', 'omsar', false);
    pll_register_string('Current Minister of OMSAR', 'Current Minister of OMSAR', 'omsar', false);

    

    pll_register_string('No posts found in this category.', 'No posts found in this category.', 'omsar', false);
    pll_register_string('Home', 'Home', 'omsar', false);

    pll_register_string('No workshops found.', 'No workshops found.', 'omsar', false);
    pll_register_string('No events found.', 'No events found.', 'omsar', false);
    pll_register_string('No news found.', 'No news found.', 'omsar', false);


    // Contact Us
    pll_register_string('Send us a Message', 'Send us a Message', 'omsar', false);
    pll_register_string('Full Name', 'Full Name', 'omsar', false);
    pll_register_string('Full Name*', 'Full Name*', 'omsar', false);
    pll_register_string('Email Address', 'Email Address', 'omsar', false);
    pll_register_string('Email Address*', 'Email Address*', 'omsar', false);
    pll_register_string('Phone', 'Phone', 'omsar', false);
    pll_register_string('Message ', 'Message ', 'omsar', false);
    pll_register_string('Message *', 'Message *', 'omsar', false);
    pll_register_string('Enter your message', 'Enter your message', 'omsar', false);
    pll_register_string('characters', 'characters', 'omsar', false);
    pll_register_string('Contact Information', 'Contact Information', 'omsar', false);
    pll_register_string('Follow Us', 'Follow Us', 'omsar', false);
    
    // Contact Form Handler Messages
    pll_register_string('Security check failed. Please refresh the page and try again.', 'Security check failed. Please refresh the page and try again.', 'omsar', false);
    pll_register_string('Full name is required.', 'Full name is required.', 'omsar', false);
    pll_register_string('A valid email address is required.', 'A valid email address is required.', 'omsar', false);
    pll_register_string('Message is required.', 'Message is required.', 'omsar', false);
    pll_register_string('Contact Form Submission from %s - %s', 'Contact Form Submission from %s - %s', 'omsar', false);
    pll_register_string('Full Name:', 'Full Name:', 'omsar', false);
    pll_register_string('Email:', 'Email:', 'omsar', false);
    pll_register_string('Phone:', 'Phone:', 'omsar', false);
    pll_register_string('Message:', 'Message:', 'omsar', false);
    pll_register_string('Failed to save your submission. Please try again later.', 'Failed to save your submission. Please try again later.', 'omsar', false);
    pll_register_string('Thank you for your submission!', 'Thank you for your submission!', 'omsar', false);
    pll_register_string('New Contact Form Submission from %s', 'New Contact Form Submission from %s', 'omsar', false);
    pll_register_string('New Contact Form Submission', 'New Contact Form Submission', 'omsar', false);
    pll_register_string('View submission in WordPress admin', 'View submission in WordPress admin', 'omsar', false);

    pll_register_string('Downloads', 'Downloads', 'omsar', false);

    pll_register_string('Overview', 'Overview', 'omsar', false);

    // Complaint / Inquiry Submission Form (Wizard)
    pll_register_string('Complaint / Inquiry Submission Form', 'Complaint / Inquiry Submission Form', 'omsar', false);
    pll_register_string('Form progress', 'Form progress', 'omsar', false);
    pll_register_string('You may submit a complaint or inquiry anonymously.', 'You may submit a complaint or inquiry anonymously.', 'omsar', false);
    pll_register_string('The Complaint and Inquiry Form provides a safe, accessible, and confidential channel for citizens to raise inquiries or complaints. The mechanism is designed in line with the do-no-harm principle and aims to ensure fairness, transparency, and accountability in the handling of all complaints.', 'The Complaint and Inquiry Form provides a safe, accessible, and confidential channel for citizens to raise inquiries or complaints. The mechanism is designed in line with the do-no-harm principle and aims to ensure fairness, transparency, and accountability in the handling of all complaints.', 'omsar', false);
    pll_register_string('Explanation', 'Explanation', 'omsar', false);
    pll_register_string('Issue type', 'Issue type', 'omsar', false);

    pll_register_string('Introduction', 'Introduction', 'omsar', false);
    pll_register_string('Contact', 'Contact', 'omsar', false);
    pll_register_string('Scope', 'Scope', 'omsar', false);
    pll_register_string('Type', 'Type', 'omsar', false);
    pll_register_string('Details', 'Details', 'omsar', false);
    pll_register_string('Consent', 'Consent', 'omsar', false);
    pll_register_string('Continue', 'Continue', 'omsar', false);
    pll_register_string('Back', 'Back', 'omsar', false);
    pll_register_string('Inquiry', 'Inquiry', 'omsar', false);
    pll_register_string('Complaint', 'Complaint', 'omsar', false);
    pll_register_string('Type your inquiry here', 'Type your inquiry here', 'omsar', false);
    pll_register_string('3. What type of issue are you reporting?', '3. What type of issue are you reporting?', 'omsar', false);
    pll_register_string('Please specify', 'Please specify', 'omsar', false);
    pll_register_string('Type your complaint here', 'Type your complaint here', 'omsar', false);
    pll_register_string('Please attach supporting documents, screenshots, or photos', 'Please attach supporting documents, screenshots, or photos', 'omsar', false);
    pll_register_string('Do you consent to be contacted for follow-up?', 'Do you consent to be contacted for follow-up?', 'omsar', false);
    pll_register_string('Yes', 'Yes', 'omsar', false);
    pll_register_string('No', 'No', 'omsar', false);
    pll_register_string('Submit', 'Submit', 'omsar', false);
    pll_register_string('General or administrative concern', 'General or administrative concern', 'omsar', false);
    pll_register_string('Working environment or interaction issue', 'Working environment or interaction issue', 'omsar', false);
    pll_register_string('Fraud or corruption concern', 'Fraud or corruption concern', 'omsar', false);
    pll_register_string('Procurement related', 'Procurement related', 'omsar', false);
    pll_register_string('Other', 'Other', 'omsar', false);
    pll_register_string('Environmental or social concern', 'Environmental or social concern', 'omsar', false);
    pll_register_string('SEA/SH complaint (confidential pathway)', 'SEA/SH complaint (confidential pathway)', 'omsar', false);
    pll_register_string('No identifying information is required. The project does not investigate SEA/SH cases but will offer referral to specialized support services.', 'No identifying information is required. The project does not investigate SEA/SH cases but will offer referral to specialized support services.', 'omsar', false);
    pll_register_string('This section applies ONLY if you selected “SEA/SH complaint” above.', 'This section applies ONLY if you selected “SEA/SH complaint” above.', 'omsar', false);
    pll_register_string('SEA/SH complaints may be submitted anonymously.', 'SEA/SH complaints may be submitted anonymously.', 'omsar', false);
    pll_register_string('Include any information you believe is relevant', 'Include any information you believe is relevant', 'omsar', false);
    pll_register_string('Please select whichever applies:', 'Please select whichever applies:', 'omsar', false);
    pll_register_string('If you choose to remain anonymous, we will not be able to communicate updates to you, but the complaint/inquiry will still be reviewed and addressed.', 'If you choose to remain anonymous, we will not be able to communicate updates to you, but the complaint/inquiry will still be reviewed and addressed.', 'omsar', false);
    pll_register_string('Would you like to remain anonymous?', 'Would you like to remain anonymous?', 'omsar', false);
    pll_register_string('Would you like referral to specialized support?', 'Would you like referral to specialized support?', 'omsar', false);
    pll_register_string('Unsure', 'Unsure', 'omsar', false);
    pll_register_string('Please indicate any information you are comfortable sharing', 'Please indicate any information you are comfortable sharing', 'omsar', false);
    pll_register_string('Optional', 'Optional', 'omsar', false);
    pll_register_string('For SEA/SH complaints, confidentiality is guaranteed and names are not required.', 'For SEA/SH complaints, confidentiality is guaranteed and names are not required.', 'omsar', false);
    pll_register_string('Email', 'Email', 'omsar', false);
    pll_register_string('Preferred method of contact', 'Preferred method of contact', 'omsar', false);
    pll_register_string('Phone/WhatsApp', 'Phone/WhatsApp', 'omsar', false);
    pll_register_string('No response needed', 'No response needed', 'omsar', false);
    pll_register_string('Are you submitting on behalf of someone else?', 'Are you submitting on behalf of someone else?', 'omsar', false);
    pll_register_string('2. What is your inquiry?', '2. What is your inquiry?', 'omsar', false);
    pll_register_string('4. What is your complaint?', '4. What is your complaint?', 'omsar', false);
    pll_register_string('Supporting documents', 'Supporting documents', 'omsar', false);
    pll_register_string('1. Contact Details', '1. Contact Details', 'omsar', false);
    pll_register_string('5. SEA/SH Complaints', '5. SEA/SH Complaints', 'omsar', false);
    pll_register_string('5. Confidentiality and Consent', '5. Confidentiality and Consent', 'omsar', false);
    pll_register_string('6. Confidentiality and Consent', '6. Confidentiality and Consent', 'omsar', false);
    pll_register_string('Optional information', 'Optional information', 'omsar', false);
    pll_register_string('Conduct or Code of Conduct issue', 'Conduct or Code of Conduct issue', 'omsar', false);

    
    // Complaint / Inquiry Form - Messages (AJAX + validation + UI)
    pll_register_string('Thank you. Your submission has been received.', 'Thank you. Your submission has been received.', 'omsar', false);
    pll_register_string('An error occurred. Please try again.', 'An error occurred. Please try again.', 'omsar', false);
    pll_register_string('Please limit your response to 200 words.', 'Please limit your response to 200 words.', 'omsar', false);
    pll_register_string('Please specify the public administration or ministry.', 'Please specify the public administration or ministry.', 'omsar', false);
    pll_register_string('Please enter your inquiry.', 'Please enter your inquiry.', 'omsar', false);
    pll_register_string('Please enter your complaint.', 'Please enter your complaint.', 'omsar', false);
    pll_register_string('Please select an issue type.', 'Please select an issue type.', 'omsar', false);
    pll_register_string('Please specify the issue type.', 'Please specify the issue type.', 'omsar', false);
    pll_register_string('Please select your consent preference.', 'Please select your consent preference.', 'omsar', false);
    pll_register_string('To allow follow-up, please provide at least an email address or phone number.', 'To allow follow-up, please provide at least an email address or phone number.', 'omsar', false);
    pll_register_string('One or more uploaded files are not allowed.', 'One or more uploaded files are not allowed.', 'omsar', false);
    pll_register_string('File upload failed. Please try again.', 'File upload failed. Please try again.', 'omsar', false);
    pll_register_string('New Complaint / Inquiry Submission', 'New Complaint / Inquiry Submission', 'omsar', false);
    pll_register_string('Please select whether you would like to remain anonymous.', 'Please select whether you would like to remain anonymous.', 'omsar', false);
    pll_register_string('Please select your referral preference.', 'Please select your referral preference.', 'omsar', false);
    
    // Validation error messages
    pll_register_string('Please fill in all required fields before continuing.', 'Please fill in all required fields before continuing.', 'omsar', false);
    pll_register_string('Please provide the required information.', 'Please provide the required information.', 'omsar', false);



    // project details
    pll_register_string('Cost', 'Cost', 'omsar', false);
    pll_register_string('Source of Fund', 'Source of Fund', 'omsar', false);
    pll_register_string('Scope', 'Scope', 'omsar', false);
    pll_register_string('Specific Objectives', 'Specific Objectives', 'omsar', false);
    pll_register_string('Results to be Achieved', 'Results to be Achieved', 'omsar', false);
    pll_register_string('Sector', 'Sector', 'omsar', false);
    pll_register_string('Executing Agency', 'Executing Agency', 'omsar', false);
    pll_register_string('Regions', 'Regions', 'omsar', false);
    pll_register_string('Directorate', 'Directorate', 'Directorate', 'omsar', false);



    //share   
    pll_register_string('Share', 'Share', 'omsar', false);

    // recruitment 
    pll_register_string('Share your email to be notified when this position becomes available.', 'Share your email to be notified when this position becomes available.', 'omsar', false);
    pll_register_string('A valid email address is required.', 'A valid email address is required.', 'omsar', false);
    pll_register_string('Invalid recruitment. Please try again.', 'Invalid recruitment. Please try again.', 'omsar', false);
    pll_register_string('You are already subscribed to notifications for this recruitment.', 'You are already subscribed to notifications for this recruitment.', 'omsar', false);
    // Recruitment admin notification emails (sent to recruitment_recipient_email)
    pll_register_string('[%s] New subscription: notify when recruitment opens', '[%s] New subscription: notify when recruitment opens', 'omsar', false);
    pll_register_string('Someone subscribed to be notified when this recruitment opens.', 'Someone subscribed to be notified when this recruitment opens.', 'omsar', false);
    pll_register_string('[%s] Someone clicked Apply for a recruitment', '[%s] Someone clicked Apply for a recruitment', 'omsar', false);
    pll_register_string('Someone clicked Apply for the following recruitment.', 'Someone clicked Apply for the following recruitment.', 'omsar', false);
    pll_register_string('Recruitment / Job', 'Recruitment / Job', 'omsar', false);
    pll_register_string('User email', 'User email', 'omsar', false);
    pll_register_string('Date', 'Date', 'omsar', false);
    // Recruitment "position open" email default subject
    pll_register_string('Application Now Open for {{Job Title}}', 'Application Now Open for {{Job Title}}', 'omsar', false);
    pll_register_string('Failed to save your subscription. Please try again later.', 'Failed to save your subscription. Please try again later.', 'omsar', false);
    pll_register_string('Thank you for your interest. You will be notified by email as soon as this position becomes available. Please check your junk folder frequently to avoid missing our notifications.', 
    'Thank you for your interest. You will be notified by email as soon as this position becomes available. Please check your junk folder frequently to avoid missing our notifications.', 
    'omsar', false);
    // pll_register_string('Recruitment Position Now Available', 'Recruitment Position Now Available', 'omsar', false);
    pll_register_string('Please check your spam/junk folder if you did not receive this email.', 'Please check your spam/junk folder if you did not receive this email.', 'omsar', false);
    pll_register_string('Thank you for your interest. We look forward to receiving your application.', 'Thank you for your interest. We look forward to receiving your application.', 'omsar', false);
    pll_register_string('Submitting...', 'Submitting...', 'omsar', false);

    pll_register_string('Submitting...', 'Submitting...', 'omsar', false);

    pll_register_string('There are currently no open positions. Check upcoming opportunities and sign-up to be notified when they become available.', 'There are currently no open positions. Check upcoming opportunities and sign-up to be notified when they become available.', 'omsar', false);


    // Search  
    pll_register_string('Search...', 'Search...', 'omsar', false);
    pll_register_string('search_results_title','Search Results for: %s', 'omsar', false);
    pll_register_string('Sorry, but nothing matched your search terms. Please try again with different keywords.', 'Sorry, but nothing matched your search terms. Please try again with different keywords.', 'omsar', false);


    // Pagination
    pll_register_string('Next', 'Next', 'omsar', false);
    pll_register_string('Previous', 'Previous', 'omsar', false);

    // Post Type Labels (for search results)
    pll_register_string('Post', 'Post', 'omsar', false);
    pll_register_string('Page', 'Page', 'omsar', false);
    pll_register_string('Testimonial', 'Testimonial', 'omsar', false);
    pll_register_string('Project', 'Project', 'omsar', false);
    pll_register_string('Minister', 'Minister', 'omsar', false);
    pll_register_string('Publication', 'Publication', 'omsar', false);
    pll_register_string('Contact Us Submission', 'Contact Us Submission', 'omsar', false);
    pll_register_string('Partnership', 'Partnership', 'omsar', false);
    pll_register_string('Recruitment', 'Recruitment', 'omsar', false);
    pll_register_string('Procurement Notice', 'Procurement Notice', 'omsar', false);
    pll_register_string('Knowledge Resource', 'Knowledge Resource', 'omsar', false);
    pll_register_string('result', 'result', 'omsar', false);

    pll_register_string('results', 'results', 'omsar', false);

    pll_register_string('Open for applications', 'Open for applications', 'omsar', false);
    pll_register_string('Upcoming positions', 'Upcoming positions', 'omsar', false);
    pll_register_string('Closed positions', 'Closed positions', 'omsar', false);

    // Survey Form Strings
    pll_register_string('Verification Code', 'Verification Code', 'omsar', false);
    pll_register_string('Enter verification code', 'Enter verification code', 'omsar', false);
    pll_register_string('Verify', 'Verify', 'omsar', false);
    pll_register_string('Please enter the verification code to access the survey form.', 'Please enter the verification code to access the survey form.', 'omsar', false);
    pll_register_string('Survey Form', 'Survey Form', 'omsar', false);
    pll_register_string('Note', 'Note', 'omsar', false);
    pll_register_string('Invalid verification code. Please try again.', 'Invalid verification code. Please try again.', 'omsar', false);
    pll_register_string('Security check failed. Please refresh and try again.', 'Security check failed. Please refresh and try again.', 'omsar', false);
    pll_register_string('Please fill in all required fields.', 'Please fill in all required fields.', 'omsar', false);
    pll_register_string('This field is required.', 'This field is required.', 'omsar', false);
    pll_register_string('Thank you for completing the survey.', 'Thank you for completing the survey.', 'omsar', false);
    pll_register_string('Go to Home', 'Go to Home', 'omsar', false);
    pll_register_string('New Survey Submission', 'New Survey Submission', 'omsar', false);
    pll_register_string('New Citizen Survey Submission', 'New Citizen Survey Submission', 'omsar', false);
    pll_register_string('New Citizen Survey Submission from %s', 'New Citizen Survey Submission from %s', 'omsar', false);

    
    // Survey Form - New Strings
    pll_register_string('Introduction', 'Introduction', 'omsar', false);
    pll_register_string('General Information', 'General Information', 'omsar', false);
    pll_register_string('Properties', 'Properties', 'omsar', false);
    pll_register_string('Contact Information', 'Contact Information', 'omsar', false);
    pll_register_string('This survey form collects information about administrative properties and buildings. Please fill in all required fields accurately.', 'This survey form collects information about administrative properties and buildings. Please fill in all required fields accurately.', 'omsar', false);
    pll_register_string('General Information about the Administration', 'General Information about the Administration', 'omsar', false);
    pll_register_string('Administration Name', 'Administration Name', 'omsar', false);
    pll_register_string('Enter administration name', 'Enter administration name', 'omsar', false);
    pll_register_string('Number of properties/buildings under the administration', 'Number of properties/buildings under the administration', 'omsar', false);
    pll_register_string('Enter number of properties', 'Enter number of properties', 'omsar', false);
    // pll_register_string('Please enter the total number of properties or buildings.', 'Please enter the total number of properties or buildings.', 'omsar', false);
    pll_register_string('List of Properties / Buildings', 'List of Properties / Buildings', 'omsar', false);
    pll_register_string('Please fill in information for each property separately.', 'Please fill in information for each property separately.', 'omsar', false);
    pll_register_string('Property', 'Property', 'omsar', false);
    pll_register_string('Property location', 'Property location', 'omsar', false);
    pll_register_string('Enter property location', 'Enter property location', 'omsar', false);
    pll_register_string('Governorate', 'Governorate', 'omsar', false);
    pll_register_string('Enter governorate', 'Enter governorate', 'omsar', false);
    pll_register_string('Select governorate', 'Select governorate', 'omsar', false);
    pll_register_string('Please select a governorate for each property.', 'Please select a governorate for each property.', 'omsar', false);
    pll_register_string('District', 'District', 'omsar', false);
    pll_register_string('Select district', 'Select district', 'omsar', false);
    pll_register_string('Please select a district for each property.', 'Please select a district for each property.', 'omsar', false);  
    pll_register_string('Enter district', 'Enter district', 'omsar', false);
    pll_register_string('Area', 'Area', 'omsar', false);
    pll_register_string('Enter area', 'Enter area', 'omsar', false);
    pll_register_string('Select area', 'Select area', 'omsar', false);
    pll_register_string('Please select an area for each property.', 'Please select an area for each property.', 'omsar', false);
    pll_register_string('Property Number', 'Property Number', 'omsar', false);
    pll_register_string('Enter property number', 'Enter property number', 'omsar', false);
    pll_register_string('Section Number', 'Section Number', 'omsar', false);
    pll_register_string('Enter section number', 'Enter section number', 'omsar', false);
    pll_register_string('Property area in square meters', 'Property area in square meters', 'omsar', false);
    pll_register_string('Enter property area', 'Enter property area', 'omsar', false);
    pll_register_string('Property ownership type', 'Property ownership type', 'omsar', false);
    pll_register_string('State-owned', 'State-owned', 'omsar', false);
    pll_register_string('Rented', 'Rented', 'omsar', false);
    pll_register_string('Rental contract details', 'Rental contract details', 'omsar', false);
    pll_register_string('Date of first lease', 'Date of first lease', 'omsar', false);
    pll_register_string('Current contract duration', 'Current contract duration', 'omsar', false);
    pll_register_string('Start date', 'Start date', 'omsar', false);
    pll_register_string('End date', 'End date', 'omsar', false);
    pll_register_string('Lease value', 'Lease value', 'omsar', false);
    pll_register_string('Enter lease value', 'Enter lease value', 'omsar', false);
    pll_register_string('Exchange rate used', 'Exchange rate used', 'omsar', false);
    pll_register_string('Enter exchange rate', 'Enter exchange rate', 'omsar', false);
    pll_register_string('Price per square meter', 'Price per square meter', 'omsar', false);
    pll_register_string('Enter price per square meter', 'Enter price per square meter', 'omsar', false);
    pll_register_string('Attach property certificate', 'Attach property certificate', 'omsar', false);
    pll_register_string('Choose file', 'Choose file', 'omsar', false);
    pll_register_string('No file chosen', 'No file chosen', 'omsar', false);
    pll_register_string('Survey Filler Information', 'Survey Filler Information', 'omsar', false);
    pll_register_string('Job Title', 'Job Title', 'omsar', false);
    pll_register_string('Enter your job title', 'Enter your job title', 'omsar', false);
    pll_register_string('Please enter a valid number of properties.', 'Please enter a valid number of properties.', 'omsar', false);
    pll_register_string('Please fill in all property information before continuing.', 'Please fill in all property information before continuing.', 'omsar', false);
    pll_register_string('Property', 'Property', 'omsar', false);
    pll_register_string('General Information', 'General Information', 'omsar', false);
    pll_register_string('Properties Information', 'Properties Information', 'omsar', false);
    pll_register_string('Administration Name:', 'Administration Name:', 'omsar', false);
    pll_register_string('Number of Properties:', 'Number of Properties:', 'omsar', false);
    pll_register_string('Job Title:', 'Job Title:', 'omsar', false);
    pll_register_string('Location:', 'Location:', 'omsar', false);
    pll_register_string('Governorate:', 'Governorate:', 'omsar', false);
    pll_register_string('District:', 'District:', 'omsar', false);
    pll_register_string('Property Number:', 'Property Number:', 'omsar', false);
    pll_register_string('Section Number:', 'Section Number:', 'omsar', false);
    pll_register_string('Property Area (sqm):', 'Property Area (sqm):', 'omsar', false);
    pll_register_string('Ownership Type:', 'Ownership Type:', 'omsar', false);
    pll_register_string('First Lease Date:', 'First Lease Date:', 'omsar', false);
    pll_register_string('Contract Start:', 'Contract Start:', 'omsar', false);
    pll_register_string('Contract End:', 'Contract End:', 'omsar', false);
    pll_register_string('Lease Value:', 'Lease Value:', 'omsar', false);
    pll_register_string('Exchange Rate:', 'Exchange Rate:', 'omsar', false);
    pll_register_string('Price per Square Meter:', 'Price per Square Meter:', 'omsar', false);
    pll_register_string('Please fill in all required rental contract details for rented properties.', 'Please fill in all required rental contract details for rented properties.', 'omsar', false);
    pll_register_string('End date must be later than start date.', 'End date must be later than start date.', 'omsar', false);
    pll_register_string('Contract end date must be later than contract start date.', 'Contract end date must be later than contract start date.', 'omsar', false);
    pll_register_string('Date of first lease must be before or equal to contract start date.', 'Date of first lease must be before or equal to contract start date.', 'omsar', false);
    pll_register_string('Duration of the current contract', 'Duration of the current contract', 'omsar', false);
    pll_register_string('Full name of the person filling out the survey', 'Full name of the person filling out the survey', 'omsar', false);

    // Survey 2030 – Citizen Opinion Survey (group: Survey 2030)
    // pll_register_string('Citizen Opinion Survey', 'Citizen Opinion Survey', 'Survey 2030', false);
    pll_register_string('Public Administration Reform Program 2030', 'Public Administration Reform Program 2030', 'Survey 2030', false);
    // pll_register_string('The Ministry of State for Administrative Development invites you to participate in this survey as part of the "Public Administration Reform Program 2030".', 'The Ministry of State for Administrative Development invites you to participate in this survey as part of the "Public Administration Reform Program 2030".', 'Survey 2030', false);
    // pll_register_string('This survey aims to engage citizens in defining priorities and shaping a modern, innovative, and forward-looking public administration built on accountability and trust between the state and citizens.', 'This survey aims to engage citizens in defining priorities and shaping a modern, innovative, and forward-looking public administration built on accountability and trust between the state and citizens.', 'Survey 2030', false);
    // pll_register_string('All responses will be treated with strict confidentiality. Collected data will be used solely for policy guidance and future reform initiatives.', 'All responses will be treated with strict confidentiality. Collected data will be used solely for policy guidance and future reform initiatives.', 'Survey 2030', false);
    // pll_register_string('For inquiries: info@omsar.gov.lb', 'For inquiries: info@omsar.gov.lb', 'Survey 2030', false);
    pll_register_string('Interaction with Public Institutions', 'Interaction with Public Institutions', 'Survey 2030', false);
    pll_register_string('Priorities and Reform Perceptions', 'Priorities and Reform Perceptions', 'Survey 2030', false);
    pll_register_string('Which of the following public institutions have you interacted with during the past 12 months? (You may select more than one)', 'Which of the following public institutions have you interacted with during the past 12 months? (You may select more than one)', 'Survey 2030', false);
    pll_register_string('Ministry of Interior and Municipalities', 'Ministry of Interior and Municipalities', 'Survey 2030', false);
    pll_register_string('Ministry of Energy and Water', 'Ministry of Energy and Water', 'Survey 2030', false);
    pll_register_string('Ministry of Finance', 'Ministry of Finance', 'Survey 2030', false);
    pll_register_string('Ministry of Public Health', 'Ministry of Public Health', 'Survey 2030', false);
    pll_register_string('Ministry of Social Affairs', 'Ministry of Social Affairs', 'Survey 2030', false);
    pll_register_string('Ministry of Education and Higher Education', 'Ministry of Education and Higher Education', 'Survey 2030', false);
    pll_register_string('Ministry of Justice', 'Ministry of Justice', 'Survey 2030', false);
    pll_register_string('Ministry of Labor', 'Ministry of Labor', 'Survey 2030', false);
    pll_register_string('Ministry of Information', 'Ministry of Information', 'Survey 2030', false);
    pll_register_string('Ministry of Economy and Trade', 'Ministry of Economy and Trade', 'Survey 2030', false);
    pll_register_string('Ministry of Telecommunications', 'Ministry of Telecommunications', 'Survey 2030', false);
    pll_register_string('Ministry of Environment', 'Ministry of Environment', 'Survey 2030', false);
    pll_register_string('Ministry of Culture', 'Ministry of Culture', 'Survey 2030', false);
    pll_register_string('Ministry of Foreign Affairs and Emigrants', 'Ministry of Foreign Affairs and Emigrants', 'Survey 2030', false);
    pll_register_string('Ministry of Agriculture', 'Ministry of Agriculture', 'Survey 2030', false);
    pll_register_string('Ministry of Tourism', 'Ministry of Tourism', 'Survey 2030', false);
    pll_register_string('Ministry of Youth and Sports', 'Ministry of Youth and Sports', 'Survey 2030', false);
    pll_register_string('Ministry of Industry', 'Ministry of Industry', 'Survey 2030', false);
    pll_register_string('Ministry of Administrative Development', 'Ministry of Administrative Development', 'Survey 2030', false);
    pll_register_string('Ministry of Displaced', 'Ministry of Displaced', 'Survey 2030', false);
    pll_register_string('Ministry of Public Works and Transport', 'Ministry of Public Works and Transport', 'Survey 2030', false);
    pll_register_string('Ministry of Defense', 'Ministry of Defense', 'Survey 2030', false);
    pll_register_string('Municipalities', 'Municipalities', 'Survey 2030', false);
    pll_register_string('Governorates', 'Governorates', 'Survey 2030', false);
    pll_register_string('Mukhtars', 'Mukhtars', 'Survey 2030', false);
    pll_register_string('I have not interacted with any of the above', 'I have not interacted with any of the above', 'Survey 2030', false);
    pll_register_string('Overall, how satisfied are you with the performance of the institutions you interacted with in the past 12 months?', 'Overall, how satisfied are you with the performance of the institutions you interacted with in the past 12 months?', 'Survey 2030', false);
    pll_register_string('Very satisfied', 'Very satisfied', 'Survey 2030', false);
    pll_register_string('Somewhat satisfied', 'Somewhat satisfied', 'Survey 2030', false);
    pll_register_string('Somewhat dissatisfied', 'Somewhat dissatisfied', 'Survey 2030', false);
    pll_register_string('Very dissatisfied', 'Very dissatisfied', 'Survey 2030', false);
    pll_register_string('Not applicable / I don\'t know', 'Not applicable / I don\'t know', 'Survey 2030', false);
    pll_register_string('How satisfied are you with the following aspects?', 'How satisfied are you with the following aspects?', 'Survey 2030', false);
    pll_register_string('Clarity of required procedures', 'Clarity of required procedures', 'Survey 2030', false);
    pll_register_string('Ease of completing the service', 'Ease of completing the service', 'Survey 2030', false);
    pll_register_string('Processing time', 'Processing time', 'Survey 2030', false);
    pll_register_string('Cost of service', 'Cost of service', 'Survey 2030', false);
    pll_register_string('Staff cooperation', 'Staff cooperation', 'Survey 2030', false);
    pll_register_string('Accessibility to responsible officials when needed', 'Accessibility to responsible officials when needed', 'Survey 2030', false);
    pll_register_string('What are the three most important reforms that would improve your experience?', 'What are the three most important reforms that would improve your experience?', 'Survey 2030', false);
    pll_register_string('Providing clear and accessible information', 'Providing clear and accessible information', 'Survey 2030', false);
    pll_register_string('Inclusive services for all groups including persons with disabilities and elderly', 'Inclusive services for all groups including persons with disabilities and elderly', 'Survey 2030', false);
    pll_register_string('Simplifying procedures and reducing timeframes', 'Simplifying procedures and reducing timeframes', 'Survey 2030', false);
    pll_register_string('Unified government online portal', 'Unified government online portal', 'Survey 2030', false);
    pll_register_string('Reducing service costs', 'Reducing service costs', 'Survey 2030', false);
    pll_register_string('Enabling complaint submission and quick response', 'Enabling complaint submission and quick response', 'Survey 2030', false);
    pll_register_string('Decentralizing procedures to municipalities', 'Decentralizing procedures to municipalities', 'Survey 2030', false);
    pll_register_string('Improving infrastructure and cleanliness of public offices', 'Improving infrastructure and cleanliness of public offices', 'Survey 2030', false);
    pll_register_string('Are there any other reforms you would like to suggest?', 'Are there any other reforms you would like to suggest?', 'Survey 2030', false);
    pll_register_string('Your suggestions...', 'Your suggestions...', 'Survey 2030', false);
    pll_register_string('How much trust do you have in public institutions overall?', 'How much trust do you have in public institutions overall?', 'Survey 2030', false);
    pll_register_string('Full trust', 'Full trust', 'Survey 2030', false);
    pll_register_string('High trust', 'High trust', 'Survey 2030', false);
    pll_register_string('Moderate trust', 'Moderate trust', 'Survey 2030', false);
    pll_register_string('Low trust', 'Low trust', 'Survey 2030', false);
    pll_register_string('No trust at all', 'No trust at all', 'Survey 2030', false);
    pll_register_string('Which three sectors should be prioritized?', 'Which three sectors should be prioritized?', 'Survey 2030', false);
    pll_register_string('Health', 'Health', 'Survey 2030', false);
    pll_register_string('Education', 'Education', 'Survey 2030', false);
    pll_register_string('Energy and electricity', 'Energy and electricity', 'Survey 2030', false);
    pll_register_string('Water, waste and sanitation', 'Water, waste and sanitation', 'Survey 2030', false);
    pll_register_string('Public transport and road safety', 'Public transport and road safety', 'Survey 2030', false);
    pll_register_string('Employment and labor market', 'Employment and labor market', 'Survey 2030', false);
    pll_register_string('Banking, finance and insurance', 'Banking, finance and insurance', 'Survey 2030', false);
    pll_register_string('Housing and real estate', 'Housing and real estate', 'Survey 2030', false);
    pll_register_string('Justice and public security', 'Justice and public security', 'Survey 2030', false);
    pll_register_string('Environment and natural resources', 'Environment and natural resources', 'Survey 2030', false);
    pll_register_string('Are there other priority sectors not listed above?', 'Are there other priority sectors not listed above?', 'Survey 2030', false);
    pll_register_string('Your response...', 'Your response...', 'Survey 2030', false);
    pll_register_string('Are you Lebanese or another nationality?', 'Are you Lebanese or another nationality?', 'Survey 2030', false);
    pll_register_string('Lebanese', 'Lebanese', 'Survey 2030', false);
    pll_register_string('Other nationality', 'Other nationality', 'Survey 2030', false);
    pll_register_string('Are you residing in Lebanon or abroad?', 'Are you residing in Lebanon or abroad?', 'Survey 2030', false);
    pll_register_string('Residing in Lebanon', 'Residing in Lebanon', 'Survey 2030', false);
    pll_register_string('Residing abroad', 'Residing abroad', 'Survey 2030', false);
    pll_register_string('Governorate (if residing in Lebanon)', 'Governorate (if residing in Lebanon)', 'Survey 2030', false);
    pll_register_string('Region (if residing abroad)', 'Region (if residing abroad)', 'Survey 2030', false);
    pll_register_string('Beirut', 'Beirut', 'Survey 2030', false);
    pll_register_string('Mount Lebanon', 'Mount Lebanon', 'Survey 2030', false);
    pll_register_string('South', 'South', 'Survey 2030', false);
    pll_register_string('Nabatieh', 'Nabatieh', 'Survey 2030', false);
    pll_register_string('North', 'North', 'Survey 2030', false);
    pll_register_string('Akkar', 'Akkar', 'Survey 2030', false);
    pll_register_string('Baalbek-Hermel', 'Baalbek-Hermel', 'Survey 2030', false);
    pll_register_string('Bekaa', 'Bekaa', 'Survey 2030', false);
    pll_register_string('GCC countries', 'GCC countries', 'Survey 2030', false);
    pll_register_string('Other Arab countries', 'Other Arab countries', 'Survey 2030', false);
    pll_register_string('Africa', 'Africa', 'Survey 2030', false);
    pll_register_string('Asia', 'Asia', 'Survey 2030', false);
    pll_register_string('North America', 'North America', 'Survey 2030', false);
    pll_register_string('South America', 'South America', 'Survey 2030', false);
    pll_register_string('Europe', 'Europe', 'Survey 2030', false);
    pll_register_string('Australia', 'Australia', 'Survey 2030', false);
    pll_register_string('Age group:', 'Age group:', 'Survey 2030', false);
    pll_register_string('Under 18', 'Under 18', 'Survey 2030', false);
    pll_register_string('18–34', '18–34', 'Survey 2030', false);
    pll_register_string('35–49', '35–49', 'Survey 2030', false);
    pll_register_string('50–64', '50–64', 'Survey 2030', false);
    pll_register_string('65+', '65+', 'Survey 2030', false);
    pll_register_string('Gender:', 'Gender:', 'Survey 2030', false);
    pll_register_string('Female', 'Female', 'Survey 2030', false);
    pll_register_string('Male', 'Male', 'Survey 2030', false);
    pll_register_string('Do you have any type of disability?', 'Do you have any type of disability?', 'Survey 2030', false);
    pll_register_string('Prefer not to answer', 'Prefer not to answer', 'Survey 2030', false);
    pll_register_string('Annual income:', 'Annual income:', 'Survey 2030', false);
    pll_register_string('0–4000$ (0–360,000,000 LBP)', '0–4000$ (0–360,000,000 LBP)', 'Survey 2030', false);
    pll_register_string('4001–10000$ (360,000,001–900,000,000 LBP)', '4001–10000$ (360,000,001–900,000,000 LBP)', 'Survey 2030', false);
    pll_register_string('10001–20000$ (900,000,001–1,800,000,000 LBP)', '10001–20000$ (900,000,001–1,800,000,000 LBP)', 'Survey 2030', false);
    pll_register_string('20001–40000$ (1,800,000,001–3,600,000,000 LBP)', '20001–40000$ (1,800,000,001–3,600,000,000 LBP)', 'Survey 2030', false);
    pll_register_string('40001–80000$ (3,600,000,001–7,200,000,000 LBP)', '40001–80000$ (3,600,000,001–7,200,000,000 LBP)', 'Survey 2030', false);
    pll_register_string('80001–150000$ (7,200,000,001–13,500,000,000 LBP)', '80001–150000$ (7,200,000,001–13,500,000,000 LBP)', 'Survey 2030', false);
    pll_register_string('More than 13,500,000,000 LBP (more than 150,000$)', 'More than 13,500,000,000 LBP (more than 150,000$)', 'Survey 2030', false);
    pll_register_string('Are you currently employed?', 'Are you currently employed?', 'Survey 2030', false);
    pll_register_string('Employment sector', 'Employment sector', 'Survey 2030', false);
    pll_register_string('Public sector', 'Public sector', 'Survey 2030', false);
    pll_register_string('Private sector', 'Private sector', 'Survey 2030', false);
    pll_register_string('NGO sector', 'NGO sector', 'Survey 2030', false);
    pll_register_string('Status', 'Status', 'Survey 2030', false);
    pll_register_string('Student', 'Student', 'Survey 2030', false);
    pll_register_string('Homemaker', 'Homemaker', 'Survey 2030', false);
    pll_register_string('Retired', 'Retired', 'Survey 2030', false);
    pll_register_string('Medical condition', 'Medical condition', 'Survey 2030', false);
    pll_register_string('Unemployed', 'Unemployed', 'Survey 2030', false);
    pll_register_string('Response', 'Response', 'Survey 2030', false);
    pll_register_string('Please select exactly 3 options and assign unique rankings from 1 to 3.', 'Please select exactly 3 options and assign unique rankings from 1 to 3.', 'Survey 2030', false);
    pll_register_string('Please enter a number between 1 and 3.', 'Please enter a number between 1 and 3.', 'Survey 2030', false);
    pll_register_string('Select', 'Select', 'Survey 2030', false);
    
}
