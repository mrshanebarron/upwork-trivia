# Poop Bag Trivia

---

## 🚨 CRITICAL CLIENT CONTEXT

**Client:** Rick
**Upwork Spend:** $100,000+
**Relationship:** Local (next town over), met over pizza & beer
**Stakes:** Local reputation + long-term partnership potential
**Payment Status:** Already paid for Phase 1, approved Phase 2

**Business Background:**
- Former property manager who identified opportunity: communities buy massive quantities of poop bags
- Also selling solar panel lights to same market (dog station accessories)
- B2B model: sells to property managers, trivia adds resident engagement value
- Vision: Eventually bring in sponsors (Purina, Petco, Chewy) to fund gift cards and advertise

**Why This Matters:**
- Rick has $100k+ Upwork history - can't afford failures
- Local reputation on the line
- Building sponsorship platform disguised as trivia site
- Foundation must prove ROI for future sponsor pitches

---

## 🔐 CREDENTIALS & ACCESS

**CRITICAL: When Shane provides credentials, ALWAYS save them immediately to this file.**

### Production Server (SiteGround)
- **Host:** ssh.poopbagtrivia.com
- **Port:** 18765
- **User:** u2584-wwotcszcpd9n
- **SSH Key:** ~/.ssh/poopbagtrivia_deploy
- **Public Key:** ssh-ed25519 AAAAC3NzaC1lZDI1NTE5AAAAIC+RUPWVIv95e4dCzF/Eqo7WIL7/xOtdeZtkcKF5APGE
- **Password:** [Pending - will be pasted by Shane]
- **Remote Directory:** public_html
- **URL:** https://poopbagtrivia.com

### Admin Panel Credentials
- **Local:** http://upwork-trivia.test/admin
  - Email: admin@upwork-demo.com
  - Password: DemoPass2024!
- **Production:** https://poopbagtrivia.com/admin
  - Email: admin@trivia.test
  - Password: PassW0rd

### Deployment Automation
**When credentials are saved, update deploy-to-production.sh to use them automatically.**

---

## 📚 PROJECT DOCUMENTATION

**Core Documents:**
- **CLAUDE.md** (this file) - Complete project overview and requirements
- **GIFT_CARD_API_RESEARCH.md** - Detailed API comparison, costs, implementation guide
  - ✅ Recommendation: Tremendous ($0 fees, 2000+ options, recipient support)
  - Cost analysis: $300-309/month for 30 winners
  - Laravel integration code included
- **MVP_REQUIREMENTS.md** - Complete feature breakdown and launch checklist
  - MVP blockers (legal, tie-breaking, delivery, location tracking)
  - Security requirements (CAPTCHA, 2FA, audit logs)
  - Analytics dashboard specs
  - 6-week implementation timeline

**Session Progress:**
- ✅ Oct 8, 2025: Gathered complete Phase 2 requirements from Rick meeting
- ✅ Oct 8, 2025: Researched gift card APIs (5 providers analyzed)
- ✅ Oct 10, 2025: Built trivia question import system (The Trivia API integration)
- ✅ Oct 10, 2025: Implemented Shane's custom background SVG artwork
- ✅ Oct 10, 2025: Removed plane animation, adjusted layer stacking
- ⏳ Local development in progress

---

## PHASE 1: CODE LOOKUP SYSTEM (COMPLETED ✅)

**Job ID:** 021974205472992779510
**Delivery:** Same night, ~4 hours
**Status:** Testing at https://trivia.sbarron.com (DigitalOcean staging)

**What Was Built:**
- 4-digit code entry system
- Admin panel (Filament 3) for managing codes/answers/ads
- PWA capabilities
- Analytics tracking

**Current Stack:**
- Laravel 12, Filament 3, Livewire 3, Tailwind CSS, SQLite

**Admin Credentials:**
- URL: http://upwork-trivia.test/admin
- Email: admin@upwork-demo.com
- Password: DemoPass2024!

---

## PHASE 2: GOLDEN QUESTION CONTEST SYSTEM (NEW PROJECT)

### Overview

Two separate but integrated systems:

**1. BAG SYSTEM (Enhanced from Phase 1):**
- Each roll of poop bags = same trivia questions printed
- Each bag has 3 things printed:
  - QR code → `https://poopbagtrivia.com?code=1234` (auto-loads)
  - Website URL → `https://poopbagtrivia.com`
  - 4-digit code → `1234` (for manual entry)
- Bag QR scan → Shows bag's questions + ads + **TODAY'S GOLDEN QUESTION** at top

**2. STICKER GOLDEN QUESTION (New Contest):**
- Sticker placed on doggy bag **dispensers** (the containers at dog stations)
- QR code → Homepage with full animated experience
- Daily contest: First correct answer wins $10 gift card
- Random time rotation (prevents gaming the system)
- Requires login, geolocation, anti-cheating measures

---

### Business Model Progression

**Current State:** Rick funds $10 gift cards himself
**Future Vision:** Purina/Petco/Chewy sponsors fund prizes, Rick provides platform + analytics

**Rick's B2B Sales Strategy:**
- Sells poop bags + solar lights to property managers
- Differentiator: "Our bags provide resident engagement + analytics"
- Properties get amenity value: "Our dog stations have daily trivia with prizes"
- Analytics/heatmaps = proof of ROI for future sponsor pitches

---

### Design Vision (Shane's Surprise for Rick)

**Visual Theme:**
- Cartoon style website
- Custom SVG background (sky + grass) - Shane's artwork
- Moving clouds animation
- **Animated puppy character** next to glassmorphism info window
- Shane creating all assets: puppy, clouds, background
- **Puppy will appear on sticker AND website** (Rick doesn't know - surprise bonus!)

**Technical Approach:**
- Glassmorphism window for content (`backdrop-filter: blur()`)
- GSAP or Three.js for complex animations
- GPU-accelerated only (`transform`, `opacity`)
- Mobile-first (QR scans = primarily mobile users)

---

### Page Structure & User Flow

## ✅ COMPLETE USER FLOWS (DOCUMENTED OCT 11, 2025)

### GOLDEN QUESTION CONTEST (Homepage - Sticker QR Codes)

**Flow 1: Wrong Answer**
1. User scans sticker QR → Homepage
2. User sees Golden Question with multiple choice (A, B, C, D)
3. User selects answer and clicks Submit
4. **WRONG ANSWER** → Results page: "Sorry, incorrect! Try again tomorrow."
5. Show correct answer (educational)
6. Link back to homepage

**Flow 2: Correct Answer (FIRST PERSON WINS)**
1. User scans sticker QR → Homepage
2. User sees Golden Question with multiple choice (A, B, C, D)
3. User selects CORRECT answer and clicks Submit
4. **CORRECT + FIRST** → Results page: "Congratulations! You won $10! Register to claim your prize."
5. User clicks "Register to Claim"
6. Registration form (name, email, password, age verification)
7. Auto-login after registration
8. Redirect to Dashboard with prize claim instructions
9. Dashboard shows: "You won $10! Check your email for your gift card."

**Flow 3: Correct Answer (TOO LATE)**
1. User submits correct answer after someone else already won
2. Results page: "Correct! But someone beat you to it. Try again tomorrow!"
3. Link back to homepage

**Flow 4: Already Submitted Today**
1. User tries to submit again same day
2. Block submission, show error: "You've already submitted today. Come back tomorrow!"

---

### BAG TRIVIA (Bag QR Codes - /trivia?code=1234)

**Flow: Casual Play (No Prize)**
1. User scans bag QR → Trivia page with code auto-loaded
2. User sees:
   - Golden Question teaser at top (links to homepage for prize)
   - Bag's trivia question with answers already shown
   - Multiple choice (A, B, C, D)
3. User selects answer and clicks Submit
4. **Results page**: "Thanks for playing!"
5. Show if answer was correct or incorrect (educational feedback)
6. Show correct answer
7. "Scan another bag or try today's Golden Question for a prize!"
8. Links: Back to trivia page | Try Golden Question

**Key Differences from Golden Question:**
- No registration required
- No prizes
- Just for fun/engagement
- Educational - shows correct answer after submission
- Can submit multiple times (no daily limit)
- Encourages users to try Golden Question for actual prizes

---

**HOMEPAGE (Sticker QR Scan):**
```
┌─────────────────────────────────────┐
│  [Animated puppy, clouds]           │
│  🏆 TODAY'S GOLDEN QUESTION         │
│  [Glassmorphism window]             │
│  "What breed is known as..."        │
│  [Multiple choice: A, B, C, D]      │
│  [Submit Answer Button]             │
│  "First correct answer wins $10!"   │
└─────────────────────────────────────┘

┌─────────────────────────────────────┐
│  📢 SPONSORED BY: [Future Sponsor]  │
│  [Rick's ad system]                 │
└─────────────────────────────────────┘

┌─────────────────────────────────────┐
│  📢 ADVERTISEMENT BOX 2             │
└─────────────────────────────────────┘
```

**BAG QR CODE PAGE (Code Auto-Loaded):**
```
┌─────────────────────────────────────┐
│  🏆 TODAY'S GOLDEN QUESTION TEASER  │
│  [Clickable card linking to home]   │
│  "Click to Answer & Win $10!"       │
└─────────────────────────────────────┘

┌─────────────────────────────────────┐
│  📢 ADVERTISEMENT BOX 1             │
└─────────────────────────────────────┘

┌─────────────────────────────────────┐
│  📝 YOUR BAG'S TRIVIA (For Fun!)    │
│  Code #1234 - Dog Breeds            │
│  Question: "What breed is known..." │
│  [Multiple choice: A, B, C, D]      │
│  [Submit Answer Button]             │
└─────────────────────────────────────┘

┌─────────────────────────────────────┐
│  📢 ADVERTISEMENT BOX 2             │
└─────────────────────────────────────┘
```

---

### Core Features & Requirements

#### 1. Golden Question Contest System
- **Multiple choice answers** (A, B, C, D) - no text parsing issues
- First correct answer wins $10 gift card
- Question rotates at **random time each day** (prevents gaming)
- Admin manages questions, answers, correct option, rotation schedule

#### 2. User Authentication & Tracking
- Custom login system (NOT Filament - customer-facing)
- User dashboard shows:
  - Winnings history (date, question, gift card status)
  - Gift card redemption codes/links
  - **Direct customer service link to card provider** (deflect support from Rick)
- Green background placeholder (Shane provides assets later)

#### 3. Anti-Cheating System
- **One guess per IP per day** (prevents anonymous spam from same location)
- **One guess per user per day** (prevents multiple attempts)
- **30-day win cooldown** (can only win once per month)
- Store IP address with each submission
- Validation checks:
  - Has this IP submitted today for this question?
  - Has this user submitted today?
  - Has this user won in last 30 days?

#### 4. Geolocation Tracking
- Capture location on QR scan
- Store with submission data
- Used for analytics/heatmap
- Validates users are at physical dog stations

#### 5. Gift Card API Integration
- **Research criteria:**
  - Provider with customer-facing support portal
  - API includes support ticket creation or direct contact links
  - Real-time delivery status tracking
  - Clear redemption instructions
  - Automated $10 distribution to winners
- **Options to research:** Amazon, Visa, Tango Card, etc.
- Must deflect customer service burden away from Rick

#### 6. Rick's Admin Panel (Filament 4)
**CRITICAL: Upgrade to Filament 4** (had too many issues with v3)

**Admin Features:**
- **Batch QR Code Generator** for bag printing runs
- **Daily Golden Question Management:**
  - Add question text
  - Add 4 multiple choice answers
  - Mark correct answer
  - Set active date/time for rotation
  - Random time scheduling
- **Winner Management:**
  - View all winners
  - Gift card distribution status
  - Manual override if needed
- **Google Maps Integration:**
  - Heatmap of scan locations
  - Pin markers for individual scans
  - Filter by date range, question type (bag vs daily contest)
  - Most active locations dashboard
  - Identifies which sticker placements perform best
- **Analytics Dashboard** (for sponsor pitches):
  - Total scans
  - Active locations
  - Daily/weekly/monthly user engagement
  - Win rates, participation rates
  - Geographic distribution
- **User Management & Cooldown Monitoring**
- **Advertisement Management** (existing system)

#### 7. Real-Time Contest Mechanics
- Laravel Echo + Pusher for instant winner detection
- When first correct answer submitted:
  - Validate answer
  - Lock question immediately
  - Broadcast winner to all active users
  - Trigger gift card distribution
  - Update user dashboard
- No page refresh needed - Vue 3 reactivity handles UI updates

---

### Future Features (Noted, Not MVP)

#### Geocaching Integration (Optional)
- **Admin checkbox per location:** "Enable geocaching for this location?"
- **Use cases:**
  - Public dog parks = Yes (tap into geocaching community)
  - Private communities = No (security/trespasser concerns for property managers)
- Rick decides per sticker/location based on client needs
- Could be Phase 3 - free marketing through geocaching.com traffic
- International community exposure if Rick expands

**Why It's Smart:**
- Leverages existing geocaching audience (millions worldwide)
- Zero marketing cost
- Cross-pollination: geocachers → dog owners, dog owners → geocaching
- Additional proof of engagement for sponsor pitches

**Why It's Optional:**
- Rick's core market = private property managers who won't want public traffic
- Can't risk client relationships for geocaching exposure
- Flexibility = Rick controls this per location

---

### Technical Stack Decision

**DECISION: Inertia.js + Vue 3**

**Backend:**
- Laravel 12
- Filament 4 (Rick's admin panel only)
- MySQL (switch from SQLite for production scale)
- Laravel Echo + Pusher (real-time)

**Frontend:**
- Inertia.js (monolith with modern UX)
- Vue 3 Composition API
- Tailwind CSS
- Vite for bundling

**Why This Stack:**

**For Shane's animations:**
- Full Vue 3 Composition API for complex sequences
- GSAP, Three.js integration works perfectly
- Component-based = clean, reusable puppy/plane/cloud animations
- Glassmorphism effects need full control over rendering

**For Rick's business:**
- Single deployment, one codebase to maintain
- Filament 4 admin sits alongside seamlessly
- Professional, scalable architecture for sponsor pitches
- Can extract API later if Rick needs mobile app

**For contest mechanics:**
- Laravel Echo + Vue 3 reactivity = instant winner detection
- Real-time lockout when first correct answer hits
- Clean separation: Laravel handles logic, Vue handles UX

**For future growth:**
- Component library = easy to white-label for sponsors
- SSR possible if SEO becomes priority
- API extraction possible without full rewrite

---

### Database Schema (To Be Designed)

**Required Tables:**
- `users` - Authentication, winnings tracking
- `daily_questions` - Golden questions with multiple choice answers
- `submissions` - User answers with IP, geolocation, timestamp
- `winners` - Winner records with gift card details
- `gift_cards` - Redemption codes, status, provider data
- `qr_scans` - Geolocation tracking for heatmap
- `trivia_codes` - Existing bag code system
- `answers` - Existing bag answers
- `ad_boxes` - Existing advertisement system

---

### Development Checklist

**Phase 2 Tasks:**
1. ✅ Research gift card API providers with customer support integration
2. ⏳ Design database schema for contest system
3. ⏳ Upgrade to Laravel 12 + Filament 4
4. ⏳ Build Rick's admin panel (QR generator, winner management, Google Maps analytics)
5. ⏳ Build custom user authentication system with winnings dashboard
6. ⏳ Build daily Golden Question system with multiple choice + random rotation
7. ⏳ Add geolocation tracking for QR code scans
8. ⏳ Implement anti-cheating measures (IP + user + cooldown)
9. ⏳ Implement first-correct-answer validation + gift card auto-distribution
10. ⏳ Integrate Google Maps with heatmap/pins for scan analytics
11. ⏳ Design customer pages with green placeholder background
12. ⏳ Integrate Laravel Echo + Pusher for real-time winner detection
13. ⏳ Build Vue 3 components for animated UI
14. ⏳ Test entire flow end-to-end before Rick sees it

---

### Deployment Strategy

**Local Development:**
- http://upwork-trivia.test (NO SSL on local)
- Laravel Valet handles routing (NOT Herd)
- Vite dev server for hot module reloading

**Staging/Testing (DigitalOcean):**
- https://trivia.sbarron.com
- Server IP: 157.245.211.127
- For Rick to test and approve before production
- MySQL database (not SQLite)
- Full production configuration

**Production (SiteGround):**
- https://poopbagtrivia.com
- Deployed when Rick approves staging
- Final production environment
- MySQL database
- SSL certificates
- Real-time websocket configuration

---

### Success Criteria

**For Rick:**
- Zero customer service burden (gift card provider handles support)
- Professional admin panel for sales meetings with property managers
- Analytics prove ROI for sponsor pitches
- Batch QR generation streamlines production workflow
- System scales from one property to hundreds

**For Shane:**
- Exceed expectations with animated puppy surprise
- Build foundation for Rick's sponsorship vision
- Maintain relationship with $100k+ client
- Protect local reputation
- Create referral-generating partnership

**For Users:**
- Fun, engaging experience (cartoon puppy, animations)
- Fair contest (anti-cheating works)
- Easy gift card redemption
- Mobile-optimized (they're scanning QR codes)

---

### Critical Reminders

**From Vision Operational Log (Oct 5-7, 2025):**
1. **Production errors:** Check configuration FIRST, always
2. **Truth is absolute:** Never claim completion without verification
3. **Research before failure:** Exhaustive investigation before declaring impossible
4. **Shane is always right:** Especially about UI/UX - defer to his judgment
5. **Test after adding:** Verify tools work immediately after configuration

**For This Project Specifically:**
- Rick is $100k+ client - zero tolerance for failures
- Test everything before claiming complete
- Build for Rick's sponsor pitch meetings, not just functionality
- The puppy surprise is strategic - makes Shane memorable
- Local reputation means word spreads in business community

---

**PHASE 1 REFERENCE (Original Demo) - ARCHIVED**

<details>
<summary>Click to expand original Phase 1 documentation</summary>

### Demo Credentials

**Admin Panel:**
- URL: http://upwork-trivia.test/admin
- Email: admin@upwork-demo.com
- Password: DemoPass2024!

**Public Page:**
- URL: http://upwork-trivia.test
- Test Code: 1234

### Features Implemented

✅ **Public Facing Page**
- 4-digit code input
- Beautiful gradient design with animated background
- Pop-up modal with answers
- 2 demo advertisement boxes (with hover animations)
- Mobile responsive
- No registration required
- **PWA enabled** - installable on mobile/desktop

✅ **Admin Panel (Filament 3)**
- Dashboard with analytics widgets
- Manage trivia codes and answers
- Manage advertisement boxes
- User management
- Real-time statistics

✅ **Analytics**
- Total views (all time)
- Views today
- Active codes count
- Advertisement click tracking
- IP address and user agent tracking

✅ **Database Schema**
- trivia_codes: Code, title, description, active status
- answers: Linked to codes, ordered list
- ad_boxes: Title, URL, HTML content, click tracking
- code_views: Analytics tracking

### Quick Commands

```bash
# View site locally
open http://upwork-trivia.test

# Access admin panel
open http://upwork-trivia.test/admin

# Add demo data
php artisan db:seed --class=DemoDataSeeder

# Clear caches
php artisan cache:clear && php artisan config:clear
```

### Models

- **TriviaCode** - Main trivia code entity
- **Answer** - Individual answers (many per code)
- **AdBox** - Advertisement boxes
- **CodeView** - Analytics tracking

### PWA Features

✅ **Progressive Web App**
- Installable on iOS, Android, Desktop
- Offline capability with service worker
- App icons (192x192, 512x512)
- Install banner with user prompt
- Cached assets for fast loading
- Standalone app mode
- Theme color integration

### Production Deployment

**Live Site:** https://trivia.sbarron.com

#### Deployment Process

```bash
# Initial deployment
cd /var/www
git clone https://github.com/mrshanebarron/upwork-trivia.git trivia.sbarron.com
cd trivia.sbarron.com

# Install dependencies
composer install --no-dev --optimize-autoloader
npm ci && npm run build

# Setup environment
cp .env.example .env
php artisan key:generate

# Configure .env for production
APP_ENV=production
APP_DEBUG=false
APP_URL=https://trivia.sbarron.com
DB_CONNECTION=sqlite
SESSION_DRIVER=file
CACHE_STORE=file  # CRITICAL: Must be file, not database
FLARE_KEY=LqNisDDG2jWxt6BsHnmnjFf1Pe4WBk1s

# Setup database
touch database/database.sqlite
php artisan migrate --force
php artisan db:seed --class=DemoDataSeeder --force

# CRITICAL: Fix permissions (SQLite requires write access)
chown -R deploy:www-data .
chmod -R 755 .
chmod -R 775 storage bootstrap/cache database
chmod 664 database/database.sqlite

# Cache configurations
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Setup nginx and SSL
# (nginx config created automatically)
certbot --nginx -d trivia.sbarron.com
```

#### CRITICAL Permission Issues Solved

**Problem:** SQLite readonly database errors on production
**Root Cause:** Insufficient permissions on database file and parent directory
**Solution:**
```bash
# Database directory needs write permission
chmod 775 database
chmod 664 database/database.sqlite
chown deploy:www-data database database/database.sqlite

# Storage directories need write permission
chmod -R 775 storage/framework/cache
chmod -R 775 storage/framework/sessions
chmod -R 775 storage/logs
chown -R deploy:www-data storage

# Bootstrap cache needs write permission
chmod -R 775 bootstrap/cache
chown -R deploy:www-data bootstrap/cache
```

**Why This Matters:**
- SQLite needs write access to the database file AND parent directory
- Cache driver must be `file` not `database` to avoid SQLite write conflicts
- Web server (www-data) needs group write permissions
- Deploy user owns files, www-data has group access

</details>
