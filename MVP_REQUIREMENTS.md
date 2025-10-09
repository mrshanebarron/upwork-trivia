# MVP Requirements & Phase Planning

**Project:** Rick's Golden Question Contest System
**Document Purpose:** Categorize ALL requirements into MVP vs Future phases
**Last Updated:** October 8, 2025

---

## 🚨 MVP BLOCKERS (Must Have Before Launch)

These are non-negotiable for legal compliance, fair operation, and Rick's protection:

### 1. Legal & Compliance ⚖️
- **Terms & Conditions**
  - Contest eligibility rules
  - Age requirement: 18+ (standard for prize contests)
  - Entry limitations (1 per day, 30-day win cooldown)
  - Prize details ($10 digital gift card)
  - Winner selection method (first correct answer)
  - Geographic restrictions (US only? State-specific rules?)
  - Dispute resolution process
- **Privacy Policy**
  - Data collection disclosure (email, IP, geolocation, device info)
  - How data is used (contest operation, analytics, fraud prevention)
  - Third-party sharing (Tremendous for gift card delivery)
  - User rights (access, deletion, opt-out)
  - Cookie policy
- **Age Verification**
  - Checkbox on registration: "I am 18 years or older"
  - Date of birth field (validate 18+ server-side)
  - Block submission if under 18
- **Tax Implications**
  - Track total winnings per user per year
  - Alert system if user exceeds $600 (potential 1099 requirement)
  - Collect W-9 if threshold reached
  - Document in Terms that winners responsible for taxes

**Implementation:**
```php
// Age verification on registration
if ($birthdate->age < 18) {
    throw ValidationException::withMessages([
        'birthdate' => 'You must be 18 or older to participate.'
    ]);
}

// Annual winnings tracking
$annualWinnings = Winner::where('user_id', $userId)
    ->whereYear('created_at', now()->year)
    ->sum('prize_amount');

if ($annualWinnings >= 600) {
    // Flag for W-9 collection
}
```

---

### 2. Fair Contest Operation 🎯

#### Tie-Breaking Logic
**Problem:** Two users submit correct answer at exact same time.

**Solution:**
```php
// submissions table
created_at TIMESTAMP(6) // Microsecond precision
random_tiebreaker INT UNSIGNED // Random number 1-1000000

// Winner selection query
$winner = Submission::where('daily_question_id', $questionId)
    ->where('is_correct', true)
    ->orderBy('created_at', 'asc')
    ->orderBy('random_tiebreaker', 'asc')
    ->first();
```

**Process:**
1. Sort by `created_at` (microsecond precision)
2. If timestamps identical, sort by `random_tiebreaker`
3. First record wins
4. Document in Terms: "In case of simultaneous correct answers, winner determined by submission timestamp followed by random selection."

---

#### Gift Card Delivery Reliability
**Critical:** Winner MUST receive their prize or Rick looks bad.

**Failure Scenarios:**
1. **Tremendous API down**
   - Queue order for retry (Laravel Queue)
   - Retry: 1min, 5min, 15min, 1hr intervals
   - After 4 failures: Admin alert + manual distribution button

2. **Winner's email bounces**
   - Store redemption link in database
   - User dashboard: "Click to resend gift card email"
   - Admin panel: View/copy redemption link manually

3. **Webhook delivery confirmation never arrives**
   - Cron job: Check orders older than 5 minutes with status "pending"
   - Query Tremendous API directly for status
   - Update local database accordingly

**Database:**
```sql
CREATE TABLE gift_card_delivery_logs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    gift_card_id BIGINT UNSIGNED NOT NULL,
    attempt_number INT DEFAULT 1,
    status ENUM('success', 'failed', 'pending_retry') NOT NULL,
    error_message TEXT NULL,
    api_response JSON,
    attempted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (gift_card_id) REFERENCES gift_cards(id)
);
```

---

### 3. Location-Aware QR Codes 📍

**Current Gap:** Bag QR = `?code=1234` (no location tracking)

**MVP Solution:**
Each physical sticker gets unique tracking code:

**Sticker QR Format:**
```
https://trivia.sbarron.com/scan/{unique_sticker_id}
```

**Database:**
```sql
CREATE TABLE stickers (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    unique_code VARCHAR(20) UNIQUE NOT NULL, -- abc123xyz
    location_name VARCHAR(255), -- "Oakwood Park Dog Station #3"
    property_name VARCHAR(255), -- "Oakwood Apartments"
    property_manager_id BIGINT UNSIGNED NULL, -- For multi-client tracking
    latitude DECIMAL(10, 8) NULL,
    longitude DECIMAL(11, 8) NULL,
    installed_at DATE NULL,
    status ENUM('active', 'inactive', 'damaged') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    INDEX idx_property (property_manager_id)
);

CREATE TABLE sticker_scans (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    sticker_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NULL, -- If logged in
    ip_address VARCHAR(45),
    user_agent TEXT,
    scan_latitude DECIMAL(10, 8) NULL, -- User's location
    scan_longitude DECIMAL(11, 8) NULL,
    scanned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (sticker_id) REFERENCES stickers(id),
    FOREIGN KEY (user_id) REFERENCES users(id),
    INDEX idx_sticker_scans (sticker_id, scanned_at)
);
```

**Rick's Admin Benefits:**
- Heatmap: "Oakwood Park = 47 scans, Maple Ridge = 12 scans"
- ROI per location: "Station #3 costs $X/month, generates Y engagement"
- Property manager reports: "Your residents scanned Z times this month"
- Placement optimization: "Move low-performing stickers to high-traffic areas"

---

### 4. Notifications & Alerts 📧

#### Winner Notifications
```php
// Email to winner
Mail::to($winner->user)->send(new WinnerNotification($giftCard));

// Email template
Subject: 🎉 You Won $10!
Body:
Congratulations {name}!

You correctly answered today's Golden Question and won a $10 digital gift card!

Your prize: [Click to Redeem]

Need help? Contact Tremendous Support (link)

---
Rick's Trivia Challenge
```

#### Rick's Admin Alerts
```php
// Real-time notification when someone wins
Notification::send($rickAdminUser, new WinnerAlertNotification($winner));

// SMS option (Twilio)
Twilio::message($rickPhone, "🏆 Winner: {$winner->user->name} won $10 at {$sticker->location_name}");

// Email digest option
// Daily summary: "Today: 5 winners, $50 spent, 127 scans across 8 locations"
```

#### Budget Alerts
```php
// Check prize pool balance
$remaining = $prizePool->balance - $prizePool->spent_this_month;

if ($remaining < 100) {
    Notification::send($rickAdminUser, new LowBudgetAlert($remaining));
}

// Auto-pause if depleted
if ($remaining < 10) {
    Setting::set('contest_active', false);
    Notification::send($rickAdminUser, new ContestPausedAlert());
}
```

---

### 5. Budget & Prize Pool Management 💰

**Rick's Dashboard Widget:**
```
┌─────────────────────────────────────┐
│  PRIZE POOL - OCTOBER 2025          │
│                                     │
│  Budget:     $500.00               │
│  Spent:      $230.00               │
│  Remaining:  $270.00               │
│                                     │
│  Winners:    23                     │
│  Avg/day:    $7.42                  │
│  Projected:  $458 (end of month)    │
│                                     │
│  [Refill Budget]  [View Winners]    │
└─────────────────────────────────────┘
```

**Database:**
```sql
CREATE TABLE prize_pools (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    month DATE NOT NULL UNIQUE, -- 2025-10-01
    budget DECIMAL(10,2) DEFAULT 0,
    spent DECIMAL(10,2) DEFAULT 0,
    sponsor_id BIGINT UNSIGNED NULL, -- For future Purina sponsorship
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_month (month)
);

CREATE TABLE budget_transactions (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    prize_pool_id BIGINT UNSIGNED NOT NULL,
    type ENUM('deposit', 'withdrawal', 'prize', 'refund') NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    description VARCHAR(255),
    reference_id BIGINT UNSIGNED NULL, -- gift_card_id if type='prize'
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (prize_pool_id) REFERENCES prize_pools(id)
);
```

**Variable Prize Amounts (Future-Proofing):**
```php
// Daily questions can have different prize amounts
daily_questions table:
  prize_amount DECIMAL(8,2) DEFAULT 10.00

// Special events
"Halloween Mega Question" → $25
"Normal Daily Question" → $10
"Sponsor Bonus Day" → $50
```

---

## ✅ MVP CORE FEATURES (Already Planned)

1. ✅ User authentication & dashboard
2. ✅ Daily Golden Question with multiple choice
3. ✅ Random time rotation
4. ✅ First correct answer wins
5. ✅ Anti-cheating (IP + user + cooldown)
6. ✅ Geolocation tracking
7. ✅ Tremendous gift card integration
8. ✅ Rick's Filament 4 admin panel
9. ✅ Google Maps heatmap
10. ✅ QR code generator

---

## 🔒 MVP SECURITY (Must Have)

### CAPTCHA on Answer Submission
```php
// Prevent bot submissions
use ReCaptcha\ReCaptcha;

public function submitAnswer(Request $request)
{
    $recaptcha = new ReCaptcha(config('services.recaptcha.secret'));
    $response = $recaptcha->verify($request->input('g-recaptcha-response'), $request->ip());

    if (!$response->isSuccess()) {
        return back()->withErrors(['captcha' => 'Please verify you are human.']);
    }

    // Process submission...
}
```

### Device Fingerprinting
```javascript
// Frontend: Generate device fingerprint
import FingerprintJS from '@fingerprintjs/fingerprintjs';

const fp = await FingerprintJS.load();
const result = await fp.get();
const visitorId = result.visitorId; // Send with submission
```

```php
// Backend: Track submissions per device
submissions table:
  device_fingerprint VARCHAR(255)

// Validation
$todaySubmissions = Submission::where('device_fingerprint', $fingerprint)
    ->where('daily_question_id', $questionId)
    ->whereDate('created_at', today())
    ->count();

if ($todaySubmissions > 0) {
    throw ValidationException::withMessages([
        'answer' => 'This device has already submitted an answer today.'
    ]);
}
```

### Admin 2FA
```php
// Filament Panel: Enable 2FA
use Filament\Panel;

public function panel(Panel $panel): Panel
{
    return $panel
        ->plugins([
            \Stephenjude\FilamentTwoFactorAuthentication\TwoFactorAuthenticationPlugin::make(),
        ]);
}
```

### Audit Logging
```sql
CREATE TABLE admin_audit_logs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    admin_user_id BIGINT UNSIGNED NOT NULL,
    action VARCHAR(100), -- 'created', 'updated', 'deleted'
    model_type VARCHAR(100), -- 'DailyQuestion', 'Winner', etc.
    model_id BIGINT UNSIGNED,
    changes JSON, -- Before/after values
    ip_address VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (admin_user_id) REFERENCES users(id),
    INDEX idx_admin_actions (admin_user_id, created_at)
);
```

---

## 📊 MVP ANALYTICS (Must Have for Sponsor Pitches)

### Conversion Funnel
```
Sticker Scans → Question Views → Submissions → Winners

Example:
  1000 scans
   ↓ 85% viewed question (850)
   ↓ 60% submitted answer (510)
   ↓ 1 winner

Conversion rate: 51% engagement (510/1000)
```

**Database queries:**
```php
// Dashboard widget
$scans = StickerScan::whereDate('scanned_at', today())->count();
$views = DailyQuestionView::whereDate('viewed_at', today())->count();
$submissions = Submission::whereDate('created_at', today())->count();
$winners = Winner::whereDate('created_at', today())->count();

$stats = [
    'scan_to_view' => $scans > 0 ? ($views / $scans) * 100 : 0,
    'view_to_submit' => $views > 0 ? ($submissions / $views) * 100 : 0,
    'submit_to_win' => $submissions > 0 ? ($winners / $submissions) * 100 : 0,
];
```

### Time-of-Day Patterns
```php
// When do most people play?
$hourlyActivity = Submission::selectRaw('HOUR(created_at) as hour, COUNT(*) as count')
    ->groupBy('hour')
    ->get();

// Chart in admin panel
// 8am: ████░░░░░░ 40%
// 12pm: ██████████ 100%
// 5pm: ████████░░ 80%

// Insight: "Most submissions happen at lunch (12pm). Consider rotating questions at 11:30am for max engagement."
```

### Question Performance
```php
// Which questions are too easy/too hard?
$questionStats = DailyQuestion::withCount([
    'submissions',
    'submissions as correct_submissions_count' => fn($q) => $q->where('is_correct', true)
])->get();

foreach ($questionStats as $question) {
    $difficulty = ($question->correct_submissions_count / $question->submissions_count) * 100;

    if ($difficulty > 80) {
        // Too easy - everyone getting it right
    } elseif ($difficulty < 10) {
        // Too hard - no one getting it right
    }
}

// Optimal: 30-50% correct rate (challenging but achievable)
```

---

## 🎨 MVP USER EXPERIENCE

### Account Recovery
```php
// Forgot Password flow (Laravel Breeze standard)
Route::post('/forgot-password', [PasswordResetLinkController::class, 'store']);

// Email verification
Route::get('/verify-email/{id}/{hash}', [VerifyEmailController::class, '__invoke'])
    ->middleware(['signed']);
```

### Answer Explanations
```php
// After submission, show educational content
daily_questions table:
  correct_answer_explanation TEXT

// UI after wrong answer:
"❌ Not quite! The correct answer was C: Golden Retriever.
Did you know? Golden Retrievers were originally bred in Scotland in the 1860s for hunting."
```

### Contest History / Transparency
```php
// Public page: Recent winners (with privacy controls)
Route::get('/winners', function() {
    return Winner::with('user', 'dailyQuestion')
        ->where('show_publicly', true) // User opted in
        ->latest()
        ->paginate(20);
});

// Display
"🏆 Sarah M. from Denver won $10 - October 7, 2025"
"🏆 Anonymous won $10 - October 6, 2025"
```

---

## 🚀 PHASE 2 FEATURES (Post-Launch)

These are valuable but not blocking MVP:

### Enhanced User Engagement
- **Offline submission** - PWA caches question, submits when online
- **Streaks tracking** - "You've played 7 days in a row!"
- **Leaderboard** - Most wins all-time (opt-in only)
- **Achievement badges** - "First Win", "5-Day Streak", "Perfect Week"
- **Social sharing** - "I just won $10 on Rick's Trivia!" (viral marketing)

### Sponsor Features
- **Logo placement controls** - Upload, preview, approve workflow
- **Branded winner emails** - Purina logo + "Sponsored by Purina"
- **White-label options** - "Powered by Purina's Pet Rewards"
- **Sponsor analytics dashboard** - Read-only access to their campaign stats
- **API access for sponsors** - Programmatic access to their data

### Operational Tools
- **Scheduled maintenance mode** - "Contest paused for updates. Back at 3pm!"
- **Question bank management** - Import CSV of 365 questions
- **Approval workflow** - Draft → Review → Approve → Schedule
- **A/B testing** - Test two questions, see which gets more engagement
- **Multi-language support** - Spanish for Hispanic communities
- **Accessibility audit** - WCAG 2.1 AA compliance, screen reader testing

### Advanced Analytics
- **Cohort analysis** - "Users who won once return 3x more often"
- **Retention curves** - Day 1, 7, 30, 90 retention rates
- **Geographic clustering** - "Denver users more engaged than suburbs"
- **Device performance** - iOS vs Android conversion rates
- **Attribution tracking** - Which marketing channel drives best users?

### Security Enhancements
- **IP reputation checking** - Block known VPN/proxy IPs
- **Behavioral analysis** - Flag suspiciously fast answer times
- **Photo verification** - Require photo of dog for first win (proves dog owner)
- **SMS verification** - Phone number confirmation for high-value prizes

---

## 📋 MVP LAUNCH CHECKLIST

### Legal (Week 1)
- [ ] Draft Terms & Conditions (consider hiring lawyer - $500-1000)
- [ ] Draft Privacy Policy (template + customization)
- [ ] Implement age verification (18+ checkbox + DOB)
- [ ] Add tax tracking (annual winnings > $600 alert)
- [ ] Geographic restriction decision (US-only? State exclusions?)

### Core Contest (Week 2-3)
- [ ] Tie-breaking logic (microsecond timestamp + random)
- [ ] Gift card delivery with retry queue
- [ ] Webhook fallback (cron job checks pending orders)
- [ ] User dashboard: Resend gift card email button

### Location Tracking (Week 2)
- [ ] Stickers table with unique codes
- [ ] QR code format: `/scan/{unique_sticker_id}`
- [ ] Sticker scans tracking table
- [ ] Admin: Generate sticker QR codes with location names

### Notifications (Week 3)
- [ ] Winner email template (Tremendous link)
- [ ] Rick admin alerts (email + optional SMS)
- [ ] Budget alerts (low balance, depleted)
- [ ] Failed delivery alerts

### Budget Management (Week 3)
- [ ] Prize pools table (monthly budgets)
- [ ] Budget transactions log
- [ ] Admin dashboard widget (spent/remaining)
- [ ] Auto-pause if budget depleted

### Security (Week 4)
- [ ] CAPTCHA on answer submission
- [ ] Device fingerprinting (optional but recommended)
- [ ] Admin 2FA (Filament plugin)
- [ ] Audit logging (admin actions)

### Analytics (Week 4)
- [ ] Conversion funnel (scan → view → submit → win)
- [ ] Time-of-day heatmap
- [ ] Question difficulty tracking
- [ ] Sticker performance comparison

### Testing (Week 5)
- [ ] Legal review (have lawyer check Terms/Privacy)
- [ ] Test tie-breaking with simultaneous submissions
- [ ] Test gift card delivery failures + retry
- [ ] Test budget depletion + auto-pause
- [ ] Load test: 100 simultaneous submissions
- [ ] Security audit: Try to cheat the system

### Launch Prep (Week 6)
- [ ] Rick training: Admin panel walkthrough
- [ ] Rick training: What to do if X goes wrong
- [ ] Monitor first 10 winners closely
- [ ] Budget funded: $500 initial
- [ ] Tremendous account verified and production-ready

---

## 🎯 SUCCESS METRICS

### MVP Success = Rick Keeps Paying You
- **Zero customer service burden on Rick** (Tremendous handles, docs are clear)
- **Legal compliance** (no lawsuits, no state gaming commission issues)
- **Fair operation** (tie-breaking works, everyone trusts it)
- **Reliable delivery** (winners get prizes within 5 minutes)
- **Actionable analytics** (Rick can prove ROI to property managers)

### Rick's Success = Sponsor Ready
- **Engagement proof:** "X scans/day, Y% conversion, Z returning users"
- **Location data:** "47 active stations, heatmap shows clustering in Denver"
- **Cost efficiency:** "$10/winner beats $50 CPM Facebook ads"
- **Scalability demonstrated:** "Handled 100 winners in one day, no issues"

### Your Success = Long-term Partnership
- **Exceed expectations** (puppy surprise, polish, thoughtfulness)
- **Protect Rick** (legal compliance, security, fraud prevention)
- **Enable growth** (architecture ready for Purina integration)
- **Be memorable** (Rick tells other property managers about you)

---

**This is a big MVP. But every item listed is justified and necessary for professional launch.**

Rick's $100k+ Upwork status means he values thoroughness. Better to take 6 weeks and launch bulletproof than rush 2 weeks and have legal issues or fraud problems.

Next: Database schema design incorporating all these requirements?
