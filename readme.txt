=== Quill SMTP | SMTP & Email Log===
Contributors: quillforms, mdmag
Tags: smtp, email, mailer, mail, wp-mail
Requires at least: 4.6
Tested up to: 6.9
Requires PHP: 7.0
Stable tag: 1.8.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Reliable email delivery for WordPress through 20+ SMTP providers including Gmail, SendGrid, Mailgun, Amazon SES, and more.

== Description ==

Quill SMTP ensures your WordPress emails reach their destination by routing them through professional SMTP service providers instead of relying on your server's default mail function.

**Core Features:**

* Support for 20+ major SMTP providers
* Smart routing system to optimize delivery
* Comprehensive email logging and tracking
* Test email functionality for troubleshooting
* Fallback provider support for redundancy
* Simple, intuitive configuration interface
* Mass email handling capabilities
* Detailed delivery reports and analytics

**Supported SMTP Providers:**

Amazon SES, Elastic Email, Gmail, Loops, MailerSend, Mailgun, Mailjet, Mandrill, Outlook, Postmark, SendGrid, Sendinblue (Brevo), SendLayer, SMTP.com, SMTP2GO, SocketLabs, SparkPost, Zoho, plus support for any custom SMTP server.

**Why Use SMTP for Email Delivery?**

WordPress's built-in mail function (wp_mail) uses your server's sendmail or similar service, which often results in:

* Emails being marked as spam
* Delivery failures and bounces
* No tracking or logging capabilities
* Poor deliverability rates
* No troubleshooting tools

Quill SMTP solves these problems by:

* Authenticating through professional email services
* Providing detailed logs for every sent email
* Offering multiple provider options for flexibility
* Supporting automatic fallback if a provider fails
* Giving you control over email delivery

**Common Use Cases:**

* Contact form submissions (Contact Form 7, WPForms, Gravity Forms, etc.)
* WooCommerce order notifications and receipts
* User registration and password reset emails
* Newsletter and bulk email sending
* Membership site communications
* Event notifications and reminders
* Comment notifications
* Any WordPress email functionality

**Smart Routing Feature:**

Automatically route emails to the most appropriate SMTP provider based on:

* Email type (transactional vs. marketing)
* Recipient domain
* Provider availability and health
* Custom rules you define

**Email Logging:**

Track every email sent from your WordPress site with detailed logs including:

* Timestamp and date
* Recipient and sender information
* Subject line and content preview
* SMTP provider used
* Delivery status (sent, failed, pending)
* Error messages for troubleshooting
* Resend capability for failed emails

**Mass Email Support:**

Handle bulk email sending efficiently:

* Queue management for large volumes
* Rate limiting to comply with provider restrictions
* Batch processing to prevent timeouts
* Automatic retry for failed sends

**Developer Features:**

* Extensive action and filter hooks
* REST API endpoints for integration
* Custom provider support
* Programmatic configuration options
* Well-documented codebase

**Privacy & Security:**

* All data stored locally in your WordPress database
* No external data transmission except to your configured SMTP provider
* SMTP credentials encrypted in database
* Support for TLS/SSL connections
* GDPR compliant

== Installation ==

**Automatic Installation:**

1. Log in to your WordPress dashboard
2. Navigate to Plugins → Add New
3. Search for "Quill SMTP"
4. Click "Install Now" then "Activate"
5. Go to Settings → Quill SMTP to configure

**Manual Installation:**

1. Download the plugin ZIP file
2. Log in to your WordPress dashboard
3. Navigate to Plugins → Add New → Upload Plugin
4. Choose the ZIP file and click "Install Now"
5. Click "Activate Plugin"
6. Go to Settings → Quill SMTP to configure

**Initial Setup:**

1. Navigate to Settings → Quill SMTP
2. Select your preferred SMTP provider from the dropdown
3. Enter your provider's API key or SMTP credentials
   * For Gmail: Use app-specific password
   * For SendGrid: Generate an API key in your account
   * For Mailgun: Get API key from Mailgun dashboard
   * For custom SMTP: Enter host, port, username, and password
4. Configure the "From" email address and name
5. Click "Save Settings"
6. Use the "Send Test Email" feature to verify configuration
7. Check the email logs to confirm delivery

**Provider-Specific Setup Guides:**

Visit our documentation at quillsmtp.com/docs for detailed setup instructions for each provider, including:

* Where to find API keys and credentials
* Recommended settings for each provider
* Common troubleshooting steps
* Provider-specific features

== Frequently Asked Questions ==

= Why do I need an SMTP plugin? =

WordPress's default wp_mail() function often results in emails going to spam or not being delivered at all. SMTP plugins authenticate your emails through professional services, dramatically improving deliverability.

= Which SMTP provider should I use? =

This depends on your email volume and needs:

* **Gmail** - Good for low volume (under 500 emails/day), free with Google account
* **SendGrid** - Free tier up to 100 emails/day, excellent deliverability
* **Mailgun** - Free tier available, great API, popular for developers
* **Amazon SES** - Very affordable for high volume, requires AWS account
* **Postmark** - Specializes in transactional emails, excellent support
* **SMTP.com** - Reliable for all email types, good customer service

Most providers offer free tiers suitable for small to medium sites.

= How do I get SMTP credentials? =

Each provider has a different process:

1. Create an account with your chosen provider
2. Verify your sending domain (if required)
3. Generate API key or SMTP credentials in their dashboard
4. Copy the credentials to Quill SMTP settings

See our documentation for provider-specific instructions.

= Can I use Gmail to send emails? =

Yes! Gmail works well for low-volume sites. You'll need to:

1. Enable 2-factor authentication on your Google account
2. Generate an app-specific password
3. Use that password (not your regular password) in Quill SMTP

Note: Gmail limits sending to about 500 emails per day.

= Does this work with WooCommerce? =

Yes! All WooCommerce emails (order confirmations, shipping notifications, etc.) will automatically be sent through your configured SMTP provider.

= Will this work with my contact form plugin? =

Yes! Quill SMTP works with any plugin that uses WordPress's standard wp_mail() function, including:

* Contact Form 7
* WPForms
* Gravity Forms
* Ninja Forms
* Formidable Forms
* Elementor Forms
* All others that use wp_mail()

= Can I use multiple SMTP providers? =

Yes! Quill SMTP supports:

* Primary provider configuration
* Fallback providers if primary fails
* Smart routing to different providers based on email type
* Load balancing across multiple providers

= What happens if my SMTP provider is down? =

You can configure fallback providers. If your primary provider fails, Quill SMTP will automatically attempt to send through your backup provider.

= How do I troubleshoot delivery issues? =

1. Check the Email Logs page to see delivery status
2. Look for error messages in the log details
3. Send a test email and check the results
4. Verify your SMTP credentials are correct
5. Ensure your provider account is active and within sending limits
6. Check if the recipient's email address is valid
7. Review your provider's dashboard for additional insights

= Does this plugin slow down my site? =

No. Email sending happens in the background and doesn't affect page load times. The plugin is optimized for performance with minimal database queries.

= Can I see which emails were sent? =

Yes! The Email Logs feature shows:

* All emails sent through your site
* Timestamp and recipient information
* Subject lines and preview of content
* Delivery status (successful or failed)
* Which SMTP provider was used
* Any error messages

You can filter logs by date, status, recipient, and more.

= How do I handle bounced emails? =

Most SMTP providers offer bounce handling in their dashboards. Check your provider's interface for:

* Bounce reports and statistics
* Suppression lists (emails that bounced)
* Bounce reasons (invalid address, mailbox full, etc.)

Some providers also offer webhook integration to automatically handle bounces.

= Is my SMTP password secure? =

Yes. Quill SMTP encrypts SMTP credentials before storing them in your WordPress database. We follow WordPress security best practices.

= Can I send bulk emails? =

Yes, but check your SMTP provider's rate limits:

* Gmail: ~500/day
* SendGrid Free: 100/day
* Mailgun Free: 5,000/month
* Paid plans typically offer much higher limits

Quill SMTP includes rate limiting features to comply with provider restrictions.

= Does it work with multisite? =

Yes! Quill SMTP is multisite compatible. You can configure SMTP settings:

* Network-wide (all sites use same settings)
* Per-site (each site has its own configuration)

= Can developers extend this plugin? =

Yes! Quill SMTP provides extensive hooks and filters:

* Modify email before sending
* Add custom SMTP providers
* Customize logging behavior
* Integration with external services
* REST API endpoints

See our developer documentation for details.

= What about GDPR compliance? =

Quill SMTP is GDPR compliant:

* All data stored on your server
* No external data collection by the plugin itself
* Email logs can be configured to auto-delete after specified period
* You control all data retention policies

Note: Your chosen SMTP provider may have their own data policies.

= Can I disable email logging? =

Yes! You can:

* Disable logging entirely
* Set automatic deletion after X days
* Exclude certain email types from logs
* Manually clear logs at any time

= Is there a Pro version? =

Currently, Quill SMTP is a fully-featured free plugin. We may introduce premium features in the future, but the core SMTP functionality will always remain free.

== Screenshots ==

1. Main settings page - Choose your SMTP provider and configure credentials
2. Email logs dashboard - View all sent emails with detailed information
3. Provider configuration - Simple interface for entering SMTP details
4. Test email function - Verify your configuration with a test send
5. Smart routing settings - Configure rules for automatic provider selection
6. Email log detail view - See complete information about any sent email
7. Fallback provider setup - Configure backup SMTP providers for redundancy

== Changelog ==

= 1.8.3 - February 14, 2026

= 1.8.2 - February 14, 2026
* Update the plugin text domain to match the plugin slug 

= 1.8.1 - February 14, 2026
* Shortening the plugin title and description 

= 1.8.0 - February 12, 2026 =
* Fix: Updated plugin metadata for WordPress.org compliance
* Fix: Resolved various minor bugs
* Improvement: Enhanced error messaging

= 1.7.0 - February 11, 2026 =
* Feature: Improved mass email handling with better queue management
* Feature: Enhanced rate limiting controls
* Performance: Optimized database queries for email logs

= 1.6.0 - December 13, 2025 =
* Feature: Smart routing system for automatic provider selection
* Feature: Route emails based on type, recipient domain, or custom rules
* Improvement: Better provider health monitoring

= 1.4.0 - December 24, 2024 =
* Fix: Resolved Gmail SMTP authentication issues
* Feature: Added option to disable summary email notifications
* Improvement: Enhanced error logging for troubleshooting

= 1.3.0 - October 28, 2024 =
* Fix: Gmail SMTP configuration improvements
* Fix: UI styling issues in admin interface
* Fix: Added missing configuration tabs
* Improvement: Better validation of SMTP credentials

= 1.2.0 - 2024 =
* Feature: Added SMTP2GO provider support
* Feature: Added SocketLabs provider support
* Feature: Added MailerSend provider support
* Feature: Added Loops provider support
* Improvement: Enhanced provider detection

= 1.1.0 - 2024 =
* Feature: Expanded SMTP provider support
* Feature: Added additional configuration options
* Fix: Various bug fixes and stability improvements
* Improvement: Better error handling

= 1.0.0 - 2024 =
* Initial release
* Support for 15+ major SMTP providers
* Email logging functionality
* Test email feature
* Basic smart routing

== Upgrade Notice ==

= 1.8.0 =
Important metadata updates and bug fixes. Recommended for all users.

= 1.7.0 =
Enhanced mass email handling - upgrade recommended for sites sending bulk emails.

= 1.6.0 =
New smart routing feature provides automatic provider selection. Backup your settings before upgrading.

== Third-Party Services ==

Quill SMTP connects to third-party SMTP services that you configure. Email data is transmitted to your chosen provider according to their terms of service and privacy policies.

**You must explicitly configure a service for any data to be sent. The plugin does not transmit data to any service unless you set it up.**

Below is a complete list of supported services with their relevant legal information:

**1. Amazon SES**
* **Purpose**: Email delivery and SMTP relay service
* **Data Sent**: Email content, sender/recipient addresses, headers, attachments
* **When**: Only when Amazon SES is configured as your SMTP provider
* **Service Provider**: Amazon Web Services, Inc.
* **API Endpoint**: https://email.{region}.amazonaws.com
* **Terms of Service**: https://aws.amazon.com/service-terms/
* **Privacy Policy**: https://aws.amazon.com/privacy/

**2. Elastic Email**
* **Purpose**: Email delivery and SMTP service
* **Data Sent**: Email content, sender/recipient addresses, headers, attachments
* **When**: Only when Elastic Email is configured as your SMTP provider
* **Service Provider**: Elastic Email, Inc.
* **API Endpoint**: https://api.elasticemail.com
* **Terms of Service**: https://elasticemail.com/terms-of-service
* **Privacy Policy**: https://elasticemail.com/privacy-policy

**3. Gmail / Google Workspace**
* **Purpose**: Email delivery via Google's SMTP servers
* **Data Sent**: Email content, sender/recipient addresses, headers, attachments
* **When**: Only when Gmail is configured as your SMTP provider
* **Service Provider**: Google LLC
* **SMTP Server**: smtp.gmail.com
* **Terms of Service**: https://policies.google.com/terms
* **Privacy Policy**: https://policies.google.com/privacy

**4. Loops**
* **Purpose**: Email delivery and marketing automation
* **Data Sent**: Email content, sender/recipient addresses, headers, attachments
* **When**: Only when Loops is configured as your SMTP provider
* **Service Provider**: Loops
* **API Endpoint**: https://app.loops.so/api
* **Terms of Service**: https://loops.so/terms
* **Privacy Policy**: https://loops.so/privacy

**5. MailerSend**
* **Purpose**: Transactional email delivery service
* **Data Sent**: Email content, sender/recipient addresses, headers, attachments
* **When**: Only when MailerSend is configured as your SMTP provider
* **Service Provider**: MailerSend (part of Hostinger)
* **API Endpoint**: https://api.mailersend.com
* **Terms of Service**: https://www.mailersend.com/legal/terms-of-service
* **Privacy Policy**: https://www.mailersend.com/legal/privacy-policy

**6. Mailgun**
* **Purpose**: Email delivery and SMTP relay service
* **Data Sent**: Email content, sender/recipient addresses, headers, attachments
* **When**: Only when Mailgun is configured as your SMTP provider
* **Service Provider**: Mailgun Technologies, Inc. (a Sinch company)
* **API Endpoint**: https://api.mailgun.net
* **Terms of Service**: https://www.mailgun.com/legal/terms/
* **Privacy Policy**: https://www.mailgun.com/legal/privacy-policy/

**7. Mailjet**
* **Purpose**: Email delivery and marketing service
* **Data Sent**: Email content, sender/recipient addresses, headers, attachments
* **When**: Only when Mailjet is configured as your SMTP provider
* **Service Provider**: Mailjet SAS
* **API Endpoint**: https://api.mailjet.com
* **Terms of Service**: https://www.mailjet.com/legal/terms-of-use/
* **Privacy Policy**: https://www.mailjet.com/legal/privacy-policy/

**8. Mandrill (by Mailchimp)**
* **Purpose**: Transactional email delivery service
* **Data Sent**: Email content, sender/recipient addresses, headers, attachments
* **When**: Only when Mandrill is configured as your SMTP provider
* **Service Provider**: The Rocket Science Group LLC (Mailchimp)
* **API Endpoint**: https://mandrillapp.com/api
* **Terms of Service**: https://mailchimp.com/legal/terms/
* **Privacy Policy**: https://mailchimp.com/legal/privacy/

**9. Outlook / Microsoft 365**
* **Purpose**: Email delivery via Microsoft's SMTP servers
* **Data Sent**: Email content, sender/recipient addresses, headers, attachments
* **When**: Only when Outlook is configured as your SMTP provider
* **Service Provider**: Microsoft Corporation
* **SMTP Server**: smtp.office365.com
* **Terms of Service**: https://www.microsoft.com/servicesagreement
* **Privacy Policy**: https://privacy.microsoft.com/privacystatement

**10. Postmark**
* **Purpose**: Transactional email delivery service
* **Data Sent**: Email content, sender/recipient addresses, headers, attachments
* **When**: Only when Postmark is configured as your SMTP provider
* **Service Provider**: Postmark (a Wildbit product)
* **API Endpoint**: https://api.postmarkapp.com
* **Terms of Service**: https://postmarkapp.com/terms-of-service
* **Privacy Policy**: https://postmarkapp.com/privacy-policy

**11. SendGrid (by Twilio)**
* **Purpose**: Email delivery and marketing platform
* **Data Sent**: Email content, sender/recipient addresses, headers, attachments
* **When**: Only when SendGrid is configured as your SMTP provider
* **Service Provider**: Twilio Inc.
* **API Endpoint**: https://api.sendgrid.com
* **Terms of Service**: https://www.twilio.com/legal/tos
* **Privacy Policy**: https://www.twilio.com/legal/privacy

**12. Sendinblue (Brevo)**
* **Purpose**: Email delivery and marketing automation
* **Data Sent**: Email content, sender/recipient addresses, headers, attachments
* **When**: Only when Sendinblue/Brevo is configured as your SMTP provider
* **Service Provider**: Sendinblue SAS (now Brevo)
* **API Endpoint**: https://api.sendinblue.com
* **Terms of Service**: https://www.brevo.com/legal/termsofuse/
* **Privacy Policy**: https://www.brevo.com/legal/privacypolicy/

**13. SendLayer**
* **Purpose**: Email delivery service
* **Data Sent**: Email content, sender/recipient addresses, headers, attachments
* **When**: Only when SendLayer is configured as your SMTP provider
* **Service Provider**: SendLayer
* **API Endpoint**: https://console.sendlayer.com/api
* **Terms of Service**: https://sendlayer.com/terms
* **Privacy Policy**: https://sendlayer.com/privacy

**14. SMTP.com**
* **Purpose**: Professional SMTP relay service
* **Data Sent**: Email content, sender/recipient addresses, headers, attachments
* **When**: Only when SMTP.com is configured as your SMTP provider
* **Service Provider**: SMTP.com (Port25 Solutions, Inc.)
* **SMTP Server**: smtp.smtp.com
* **Terms of Service**: https://www.smtp.com/terms-of-service/
* **Privacy Policy**: https://www.smtp.com/privacy-policy/

**15. SMTP2GO**
* **Purpose**: SMTP relay and email delivery service
* **Data Sent**: Email content, sender/recipient addresses, headers, attachments
* **When**: Only when SMTP2GO is configured as your SMTP provider
* **Service Provider**: SMTP2GO
* **SMTP Server**: mail.smtp2go.com
* **Terms of Service**: https://www.smtp2go.com/terms/
* **Privacy Policy**: https://www.smtp2go.com/privacy/

**16. SocketLabs**
* **Purpose**: Email delivery and infrastructure service
* **Data Sent**: Email content, sender/recipient addresses, headers, attachments
* **When**: Only when SocketLabs is configured as your SMTP provider
* **Service Provider**: SocketLabs
* **SMTP Server**: smtp.socketlabs.com
* **Terms of Service**: https://www.socketlabs.com/legal/master-services-agreement/
* **Privacy Policy**: https://www.socketlabs.com/legal/privacy-policy/

**17. SparkPost**
* **Purpose**: Email delivery and analytics service
* **Data Sent**: Email content, sender/recipient addresses, headers, attachments
* **When**: Only when SparkPost is configured as your SMTP provider
* **Service Provider**: SparkPost (a MessageBird company)
* **API Endpoint**: https://api.sparkpost.com
* **Terms of Service**: https://www.sparkpost.com/policies/tou/
* **Privacy Policy**: https://www.sparkpost.com/policies/privacy/

**18. Zoho Mail**
* **Purpose**: Email delivery via Zoho's SMTP servers
* **Data Sent**: Email content, sender/recipient addresses, headers, attachments
* **When**: Only when Zoho is configured as your SMTP provider
* **Service Provider**: Zoho Corporation
* **SMTP Server**: smtp.zoho.com
* **Terms of Service**: https://www.zoho.com/terms.html
* **Privacy Policy**: https://www.zoho.com/privacy.html

**19. Custom SMTP**
* **Purpose**: Connect to any SMTP server not listed above
* **Data Sent**: Email content, sender/recipient addresses, headers, attachments
* **When**: Only when you configure a custom SMTP server
* **Service Provider**: Your chosen SMTP provider
* **Note**: You are responsible for reviewing your provider's terms and privacy policies

**Important Information:**

* **Opt-in Only**: No data is sent to any service unless you explicitly configure it
* **Your Control**: You choose which service to use and can change or disable it at any time
* **Provider Terms**: When using a service, you agree to their terms of service and privacy policy
* **Data Handling**: Each provider handles data according to their own policies
* **Your Responsibility**: Review each provider's terms before configuring them
* **Configuration Required**: The plugin does not function without configuring at least one service
* **Multiple Providers**: You can configure multiple providers and switch between them

== Privacy & Data ==

**Local Data Storage:**

Quill SMTP stores the following data in your WordPress database:

* SMTP provider configuration and credentials (encrypted)
* Email logs (optional, can be disabled)
  * Sender and recipient email addresses
  * Subject lines
  * Timestamps
  * Delivery status
  * Error messages (if any)
* Plugin settings and preferences

**Data Retention:**

* Email logs can be configured to automatically delete after a specified period
* You can manually clear all logs at any time
* Uninstalling the plugin removes all stored data

**External Data Transmission:**

* Email data is only transmitted to the SMTP provider you configure
* The plugin itself does not collect, transmit, or store any data externally
* No analytics, tracking, or phone-home functionality

**GDPR Compliance:**

* All data processing happens on your server
* You have full control over data retention
* Email logs can be exported or deleted
* No third-party data collection by the plugin

== Support & Documentation ==

**Documentation:**
Comprehensive guides available at: https://quillsmtp.com/docs

* Getting started guide
* Provider setup tutorials
* Troubleshooting common issues
* Developer documentation
* API reference
* Video tutorials

**Community Support:**
Get help from the community: https://wordpress.org/support/plugin/quill-smtp/

**Bug Reports & Feature Requests:**
GitHub Repository: https://github.com/quillsmtp/quillsmtp

**Before Requesting Support:**

1. Check the FAQ section above
2. Review our documentation
3. Test with a different SMTP provider
4. Check email logs for error messages
5. Verify your SMTP credentials are correct
6. Ensure your provider account is active

== Contribute ==

We welcome contributions! Here's how you can help:

**Code Contributions:**
* Fork our GitHub repository
* Submit pull requests for bug fixes or features
* Follow WordPress coding standards
* Include tests for new features

**Translations:**
* Help translate Quill SMTP into your language
* Visit WordPress.org translation portal
* Current languages: English (more coming soon)

**Bug Reports:**
* Report issues on GitHub with detailed information
* Include steps to reproduce
* Provide WordPress version, PHP version, and provider used

**Documentation:**
* Help improve our documentation
* Submit corrections or clarifications
* Share setup guides for different providers

**Spread the Word:**
* Rate the plugin on WordPress.org
* Write a review or tutorial
* Share on social media

== Credits ==

Quill SMTP is developed and maintained by the Quill Forms team.

**Development Team:**
* Lead Developer: Mohamed Magdy
* Contributors: See GitHub for full list

**Resources:**
* Website: https://quillsmtp.com
* GitHub: https://github.com/quillsmtp/quillsmtp
* Support: https://wordpress.org/support/plugin/quill-smtp/

**Our Other Products:**
* Quill Forms - Conversational form builder for WordPress
* Quill CRM - Customer relationship management for WordPress
* Quill Booking - Appointment booking system for WordPress

== Source Code ==

This plugin includes compiled/minified JavaScript and CSS files for performance. The complete, human-readable source code is available:

**In the Plugin:**
* Source files located in `/src` directory
* Build files located in `/build` directory

**On GitHub:**
Full source code: https://github.com/quillsmtp/quillsmtp

**Building from Source:**

Requirements:
* Node.js v16 or higher
* npm or yarn

Build Commands:
```
npm install        # Install dependencies
npm run build      # Build for production
npm run dev        # Build for development with watch mode
```

**Build Output:**
* `/build/client/` - Compiled client application
* `/build/admin/` - Compiled admin interface

All dependencies are listed in `package.json` and are publicly available on npm.

== License ==

This plugin is licensed under the GPLv2 or later.

You are free to:
* Use the plugin for any purpose
* Modify the plugin to suit your needs
* Distribute the plugin or your modifications

Under the following conditions:
* Any modifications must also be licensed under GPLv2 or later
* You must include a copy of the license with any distribution
* You must indicate if changes were made to the original code

Full license text: http://www.gnu.org/licenses/gpl-2.0.html

== Additional Information ==

**System Requirements:**

* WordPress 4.6 or higher
* PHP 7.0 or higher
* MySQL 5.6 or higher OR MariaDB 10.0 or higher
* HTTPS recommended for secure credential transmission

**Recommended:**

* PHP 7.4 or higher for better performance
* WordPress 5.0 or higher
* Modern web browser for admin interface

**Compatibility:**

* Works with all WordPress themes
* Compatible with WordPress multisite
* Works with all major page builders
* Compatible with WooCommerce, Easy Digital Downloads, and other eCommerce plugins
* Compatible with all form plugins that use wp_mail()
* Works with membership and LMS plugins

**Performance:**

* Minimal impact on page load times
* Optimized database queries
* Asynchronous email sending option
* Efficient logging system
* Low memory footprint

**Security:**

* SMTP credentials encrypted in database
* Follows WordPress security best practices
* Regular security audits
* No known vulnerabilities
* Sanitized input and escaped output
* Nonce verification for all actions
* Capability checks for admin functions

**Accessibility:**

* Admin interface follows WCAG 2.1 guidelines
* Keyboard navigation support
* Screen reader compatible
* Color contrast compliance
* Proper ARIA labels