<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Inertia\Inertia;

class PageController extends Controller
{
    public function about()
    {
        return Inertia::render('About', [
            'content' => Setting::getValue('about_content', $this->getDefaultAbout()),
        ]);
    }

    public function terms()
    {
        return Inertia::render('Legal/Terms', [
            'content' => Setting::getValue('terms_content', $this->getDefaultTerms()),
        ]);
    }

    public function privacy()
    {
        return Inertia::render('Legal/Privacy', [
            'content' => Setting::getValue('privacy_content', $this->getDefaultPrivacy()),
        ]);
    }

    private function getDefaultAbout(): string
    {
        return <<<HTML
<h1>About Poop Bag Trivia</h1>
<p>Welcome to Poop Bag Trivia - where dog owners can enjoy fun trivia questions while picking up after their furry friends!</p>

<h2>How It Works</h2>
<p>Each poop bag features a unique QR code and 4-digit code that gives you access to trivia questions. Simply scan the code or enter it manually to play!</p>

<h2>Daily Golden Question</h2>
<p>Visit our dispensers to scan the special sticker QR code for a chance to win daily prizes. First person to answer correctly wins!</p>

<h2>Our Mission</h2>
<p>We believe in making everyday tasks more enjoyable. By adding a fun trivia element to pet waste management, we're building a community of engaged dog owners who love learning new things.</p>
HTML;
    }

    private function getDefaultTerms(): string
    {
        return <<<HTML
<h1>Terms of Service</h1>
<p><strong>Last Updated:</strong> October 2025</p>

<h2>1. Acceptance of Terms</h2>
<p>By accessing and using Poop Bag Trivia, you accept and agree to be bound by these Terms of Service.</p>

<h2>2. Eligibility</h2>
<p>You must be at least 18 years old to participate in contests and win prizes. Trivia questions are available to all users.</p>

<h2>3. Contest Rules</h2>
<ul>
<li>One entry per person per day for the Golden Question contest</li>
<li>Winners can only win once every 30 days</li>
<li>Prizes are distributed electronically via email</li>
<li>We reserve the right to verify winners and disqualify fraudulent entries</li>
</ul>

<h2>4. User Conduct</h2>
<p>Users agree not to:</p>
<ul>
<li>Use automated systems to submit answers</li>
<li>Share or sell contest codes</li>
<li>Attempt to manipulate or cheat the contest system</li>
</ul>

<h2>5. Modifications</h2>
<p>We reserve the right to modify these terms at any time. Continued use of the service constitutes acceptance of modified terms.</p>

<h2>6. Limitation of Liability</h2>
<p>Poop Bag Trivia is provided "as is" without warranties of any kind. We are not liable for any damages arising from use of this service.</p>
HTML;
    }

    private function getDefaultPrivacy(): string
    {
        return <<<HTML
<h1>Privacy Policy</h1>
<p><strong>Last Updated:</strong> October 2025</p>

<h2>1. Information We Collect</h2>
<p>We collect the following information:</p>
<ul>
<li>Account information (name, email) when you register</li>
<li>Location data when you scan QR codes</li>
<li>IP addresses for security and anti-fraud purposes</li>
<li>Usage data (answers submitted, questions viewed)</li>
</ul>

<h2>2. How We Use Your Information</h2>
<p>Your information is used to:</p>
<ul>
<li>Operate the trivia contest and distribute prizes</li>
<li>Prevent fraud and abuse</li>
<li>Improve our service and user experience</li>
<li>Send prize notifications and important updates</li>
</ul>

<h2>3. Information Sharing</h2>
<p>We do not sell your personal information. We may share data with:</p>
<ul>
<li>Gift card providers for prize fulfillment</li>
<li>Service providers who help operate our platform</li>
<li>Law enforcement when required by law</li>
</ul>

<h2>4. Data Security</h2>
<p>We implement reasonable security measures to protect your information. However, no method of transmission over the internet is 100% secure.</p>

<h2>5. Your Rights</h2>
<p>You have the right to:</p>
<ul>
<li>Access your personal data</li>
<li>Request deletion of your account</li>
<li>Opt out of non-essential communications</li>
</ul>

<h2>6. Children's Privacy</h2>
<p>Our contest is only available to users 18 and older. We do not knowingly collect information from children under 18.</p>

<h2>7. Contact Us</h2>
<p>For privacy concerns, please contact us through our support channels.</p>
HTML;
    }
}
