<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class PageContentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // About Page Content
        Setting::updateOrCreate(
            ['key' => 'about_content'],
            [
                'value' => <<<HTML
<h1>About Poop Bag Trivia</h1>

<p>Welcome to Poop Bag Trivia - where learning meets everyday responsibilities!</p>

<h2>What We Do</h2>

<p>We believe that every moment is an opportunity to learn something new. Our platform delivers engaging trivia questions covering a wide range of topics including science, history, geography, pop culture, technology, sports, and general knowledge.</p>

<h2>How It Works</h2>

<p>Each day, we present 5 carefully selected trivia questions from our extensive database. These questions are randomly chosen to ensure variety and keep things fresh. Simply visit our homepage to see today's questions and test your knowledge!</p>

<h2>Our Mission</h2>

<p>We're dedicated to making learning fun and accessible. Whether you're a trivia enthusiast or just curious about the world around you, we've got something for everyone. Our questions are designed to be informative, entertaining, and suitable for all ages.</p>

<h2>Question Categories</h2>

<ul>
<li><strong>Science</strong> - Explore the wonders of the natural world</li>
<li><strong>History</strong> - Learn about important events and figures</li>
<li><strong>Geography</strong> - Discover facts about our planet</li>
<li><strong>Pop Culture</strong> - Test your knowledge of entertainment</li>
<li><strong>Technology</strong> - Stay informed about innovations</li>
<li><strong>Sports</strong> - Challenge yourself with athletic trivia</li>
<li><strong>General Knowledge</strong> - A mix of everything interesting</li>
</ul>

<h2>Join the Fun</h2>

<p>Visit us daily to see what new questions await. Share your favorite facts with friends and family, and make learning a part of your everyday routine!</p>
HTML,
                'type' => 'string',
                'description' => 'About page content (HTML)',
            ]
        );

        // Terms of Service Content
        Setting::updateOrCreate(
            ['key' => 'terms_content'],
            [
                'value' => <<<HTML
<h1>Terms of Service</h1>

<p><strong>Last Updated:</strong> October 17, 2025</p>

<h2>1. Acceptance of Terms</h2>

<p>By accessing and using Poop Bag Trivia ("the Website"), you accept and agree to be bound by the terms and provision of this agreement.</p>

<h2>2. Use of Service</h2>

<p>Our service provides daily trivia questions for educational and entertainment purposes. You may:</p>

<ul>
<li>Access and view trivia questions on our website</li>
<li>Read questions and answers for personal knowledge</li>
<li>Share the website with others</li>
</ul>

<h2>3. User Conduct</h2>

<p>You agree to use the Website only for lawful purposes. You agree not to:</p>

<ul>
<li>Use automated systems to access the Website in a manner that sends more requests than a human can reasonably produce</li>
<li>Attempt to interfere with or disrupt the Website's functionality</li>
<li>Copy, reproduce, or redistribute our content without permission</li>
<li>Use the Website in any way that could damage or overburden our servers</li>
</ul>

<h2>4. Intellectual Property</h2>

<p>All content on this Website, including but not limited to text, graphics, logos, and trivia questions, is the property of Poop Bag Trivia or its content suppliers and is protected by copyright laws.</p>

<h2>5. Disclaimer of Warranties</h2>

<p>The Website and its content are provided "as is" without warranties of any kind. We do not guarantee the accuracy, completeness, or usefulness of any information on the Website.</p>

<h2>6. Limitation of Liability</h2>

<p>Poop Bag Trivia shall not be liable for any direct, indirect, incidental, consequential, or punitive damages arising from your use of the Website.</p>

<h2>7. Changes to Terms</h2>

<p>We reserve the right to modify these terms at any time. Continued use of the Website after changes constitutes acceptance of the modified terms.</p>

<h2>8. Contact Information</h2>

<p>If you have any questions about these Terms of Service, please contact us through the information provided on our website.</p>
HTML,
                'type' => 'string',
                'description' => 'Terms of Service page content (HTML)',
            ]
        );

        // Privacy Policy Content
        Setting::updateOrCreate(
            ['key' => 'privacy_content'],
            [
                'value' => <<<HTML
<h1>Privacy Policy</h1>

<p><strong>Last Updated:</strong> October 17, 2025</p>

<h2>1. Introduction</h2>

<p>Welcome to Poop Bag Trivia. We respect your privacy and are committed to protecting any personal information you may provide while using our website.</p>

<h2>2. Information We Collect</h2>

<h3>2.1 Information You Provide</h3>

<p>We may collect information that you voluntarily provide when using our website, such as:</p>

<ul>
<li>Name and email address (if you create an account)</li>
<li>Any information you choose to provide through contact forms</li>
</ul>

<h3>2.2 Automatically Collected Information</h3>

<p>When you visit our website, we may automatically collect certain information, including:</p>

<ul>
<li>IP address</li>
<li>Browser type and version</li>
<li>Pages visited and time spent on pages</li>
<li>Referring website addresses</li>
<li>Device information</li>
</ul>

<h2>3. How We Use Your Information</h2>

<p>We use the information we collect to:</p>

<ul>
<li>Provide and maintain our trivia service</li>
<li>Improve and personalize user experience</li>
<li>Analyze website usage and trends</li>
<li>Communicate with you about the service</li>
<li>Protect against fraudulent or illegal activity</li>
</ul>

<h2>4. Cookies and Tracking Technologies</h2>

<p>We use cookies and similar tracking technologies to enhance your experience on our website. Cookies are small data files stored on your device. You can control cookie preferences through your browser settings.</p>

<h2>5. Data Security</h2>

<p>We implement appropriate technical and organizational measures to protect your personal information against unauthorized access, alteration, disclosure, or destruction.</p>

<h2>6. Third-Party Services</h2>

<p>Our website may contain links to third-party websites. We are not responsible for the privacy practices of these external sites. We encourage you to review their privacy policies.</p>

<h2>7. Children's Privacy</h2>

<p>Our service is intended for general audiences. We do not knowingly collect personal information from children under 13 years of age.</p>

<h2>8. Your Rights</h2>

<p>You have the right to:</p>

<ul>
<li>Access the personal information we hold about you</li>
<li>Request correction of inaccurate information</li>
<li>Request deletion of your personal information</li>
<li>Opt-out of certain data collection practices</li>
</ul>

<h2>9. Changes to This Policy</h2>

<p>We may update this Privacy Policy from time to time. We will notify you of any changes by posting the new Privacy Policy on this page and updating the "Last Updated" date.</p>

<h2>10. Contact Us</h2>

<p>If you have any questions about this Privacy Policy or our data practices, please contact us through the information provided on our website.</p>
HTML,
                'type' => 'string',
                'description' => 'Privacy Policy page content (HTML)',
            ]
        );
    }
}
