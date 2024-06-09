=== Quill SMTP | Email Delivery & Transactional Email | The best SMTP Plugin for WordPress that integrates with 20+ SMTP mailers
Contributors: quillforms, mdmag
Requires at least: 4.6
Tested up to: 6.5.3
Requires PHP: 7.0
Stable tag: 1.0.0
Donate link: https://www.paypal.com/paypalme/mohamedmagdymohamed
Tags: quill, smtp, mailer, logs
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

The best SMTP Plugin for WordPress that integrates with 20+ SMTP mailers

== Description ==

Quill SMTP is the only SMTP plugin that integrates with 20+ mailers.

## Use of Third-Party Services

This plugin relies on several third-party services to function properly. Below, you will find a detailed explanation of each service used, the circumstances under which they are used, and the relevant links to their terms of use and privacy policies.

### 1. SMTP.com

This plugin uses SMTP.com for sending emails. The following operations involve contacting the SMTP.com service:

- **Endpoint:** `https://api.smtp.com/v4/messages`
  - **Method:** POST
  - **Headers:** Accept: application/json, Content-Type: application/json, Authorization: Bearer [API_KEY]
  - **Data:** Email content and metadata

- **Endpoint:** `https://api.smtp.com/v4/account`
  - **Method:** GET
  - **Headers:** Accept: application/json, Content-Type: application/json, Authorization: Bearer [API_KEY]

**Service Terms:** [SMTP.com Terms of Use](https://www.smtp.com/policies/terms-and-conditions/)  
**Privacy Policy:** [SMTP.com Privacy Policy](https://www.smtp.com/policies/privacy-policy/)

### 2. Elastic Email

This plugin uses Elastic Email for handling email transactions.

- **Endpoint:** `https://api.elasticemail.com/v2/account/load`
  - **Method:** GET
  - **Headers:** Accept: application/json, Content-Type: application/json; charset=[Charset], Cache-Control: no-cache

**Service Terms:** [Elastic Email Terms of Use](https://elasticemail.com/resources/usage-policies)  
**Privacy Policy:** [Elastic Email Privacy Policy](https://elasticemail.com/resources/usage-policies/privacy-policy)

### 3. SendLayer

This plugin utilizes SendLayer for email delivery services.

- **Endpoint:** `https://console.sendlayer.com/api/v1/email`
  - **Method:** POST
  - **Headers:** Accept: application/json, Content-Type: application/json, Authorization: Bearer [API_KEY]
  - **Data:** Email content and metadata

**Service Terms:** [SendLayer Terms of Use](https://sendlayer.com/terms-of-service/)  
**Privacy Policy:** [SendLayer Privacy Policy](https://sendlayer.com/privacy-policy)

### 4. Mailgun

This plugin uses Mailgun for sending emails.

- **Endpoint:** `https://api.[region].mailgun.net/v3/[DOMAIN]/messages`
  - **Method:** POST
  - **Headers:** Accept: application/json, Content-Type: application/json, Authorization: Basic [API_KEY]
  - **Data:** Email content and metadata

**Service Terms:** [Mailgun Terms of Use](https://www.mailgun.com/legal/terms/)
**Privacy Policy:** [Mailgun Privacy Policy](https://www.mailgun.com/legal/privacy-policy/)

### 5. SparkPost

This plugin uses SparkPost for sending emails.

- **Endpoint:** `https://api.sparkpost.com/api/v1/transmissions`
  - **Method:** POST
  - **Headers:** Accept: application/json, Content-Type: application/json, Authorization: Bearer [API_KEY]
  - **Data:** Email content and metadata

**Service Terms:** [SparkPost Terms of Use](https://www.sparkpost.com/policies/tos/)
**Privacy Policy:** [SparkPost Privacy Policy](https://www.sparkpost.com/policies/privacy/)


## Important Notes

- Data is sent to these third-party services as necessary for the plugin to function correctly.
- Users should review the terms and privacy policies of each service to understand how their data is handled.
- Proper documentation and disclosure of these services are provided to ensure transparency and legal compliance.

= Be a contributor =
If you want to contribute, go to our [Quill SMTP GitHub Repository](https://github.com/quillsmtp/quillsmtp) and see where you can help.

== Changelog ==

= 1.0.0 =
* Initial release