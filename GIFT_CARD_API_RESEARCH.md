# Gift Card API Research & Recommendations

**Research Date:** October 8, 2025
**For:** Rick's Golden Question Contest System
**Use Case:** $10 digital gift cards for daily trivia winners

---

## Executive Summary

**RECOMMENDATION: Tremendous**

For Rick's trivia contest with $10 daily prizes, **Tremendous** is the optimal choice:
- ✅ **Zero fees** on gift cards (only 3% on credit card funding)
- ✅ **2000+ payout options** (recipients choose what they want)
- ✅ **Recipient self-service support** (deflects burden from Rick)
- ✅ **Developer-friendly** (5-minute integration claim)
- ✅ **No minimums or monthly fees**
- ✅ **Revenue sharing** at high volume (Rick benefits as he scales)

**Estimated Monthly Cost for Rick:**
- 30 winners/month × $10 = **$300/month face value**
- Credit card funding fee (3%): **$9/month**
- **Total: ~$309/month** (or $300 if funded via bank transfer - free)

---

## Detailed Provider Comparison

### 1. Tremendous ⭐ RECOMMENDED

**Pricing:**
- **Face value only:** $10 gift card = $10 cost
- **Zero API/setup/subscription fees**
- **Credit card funding:** 3% fee ($0.30 per $10 card)
- **Bank transfer funding:** FREE (ACH, wire, invoice)
- **Revenue sharing:** High-volume senders get rebates

**Payout Options:**
- 2000+ options in 200+ countries
- Amazon, Visa, PayPal, Venmo, charity donations
- Recipients choose what they want

**Developer Experience:**
- **Integration time:** "5 minutes to first API call"
- **Rate limits:** Not specified (no hard limits mentioned)
- **Authentication:** API key or OAuth 2.0
- **Libraries:** Ruby, Python, Node.js
- **Sandbox:** Free and open

**Customer Support:**
- **Recipient support:** World-class, typically next-day engineer response
- **Deflects Rick's burden:** Recipients contact Tremendous directly
- **Developer support:** Fast response times

**Why It's Best for Rick:**
- Zero upfront costs - perfect for testing concept
- Recipient choice = higher satisfaction (stronger engagement metric for sponsors)
- Bank transfer funding = literally just face value (no fees at all)
- Scales beautifully: revenue sharing kicks in at high volume
- Professional enough to show Purina/Petco in sponsor meetings

**Potential Downsides:**
- Not a "pure" Amazon/Visa card - recipients get choice (could be positive)
- Newer company vs. established players (but trusted by Google, Meta, Stripe)

---

### 2. Tango Card (Close Second)

**Pricing:**
- **Face value only:** $10 gift card = $10 cost
- **Zero additional fees**
- "Fair and attractive pricing model"

**Payout Options:**
- Robust catalog of digital gift cards
- 80+ countries supported

**Developer Experience:**
- **Integration:** Well-documented API v2
- **Rate limits:** 1 TPS (Transaction Per Second) maximum
- **Authentication:** Basic Auth or OAuth 2.0
- **Sandbox:** Free test console

**Customer Support:**
- **Recipient support:** Self-service chatbot + email/phone escalation
- **Developer support:** [email protected]
- **Include X-REQUEST-ID for faster resolution**

**Why Consider:**
- Established player (industry leader)
- Explicit recipient support chatbot deflects Rick's burden
- Program/UI review required before production (quality control)

**Why Not #1:**
- 1 TPS rate limit could be restrictive if multiple winners simultaneously
- Requires program review (slower to production)
- No revenue sharing mentioned

---

### 3. Amazon Incentives API (Official)

**Pricing:**
- **No program fees**
- Face value only (pricing not public - requires contact)
- Gift cards never expire

**Payout Options:**
- Amazon gift codes only
- Usable anywhere Amazon operates

**Developer Experience:**
- **Rate limits:** 10 requests/sec (most), 1 req/sec (balance checks)
- **Authentication:** AWS Signature Version 4
- **Security:** PCI compliant, TLS 1.2, SHA-256
- **Sandbox:** Available for testing

**Customer Support:**
- **Developer:** [email protected]
- **Recipient support:** Through Amazon's standard channels
- Must include Partner ID in support requests

**Why Consider:**
- Direct from Amazon (no middleman)
- Brand recognition (everyone uses Amazon)
- Never-expire guarantee

**Why Not #1:**
- Amazon-only (no choice for recipients)
- More complex auth (AWS Signature v4)
- Stricter rate limits (10 req/sec could be hit)
- Recipient support not explicitly designed for API use case
- Less flexible for international expansion

---

### 4. Runa

**Pricing:**
- **Face value only** (pay-as-you-go)
- **Zero setup fees or minimums**

**Payout Options:**
- 4000+ gift cards
- 38 countries, 20+ currencies

**Developer Experience:**
- **Integration:** Couple hours to couple weeks (depends on complexity)
- **Sandbox:** Free unlimited testing
- **Docs:** docs.runa.io

**Customer Support:**
- Portal for tracking and raw data
- Technical support for API
- Recipient support mentioned but not detailed

**Why Consider:**
- Huge catalog (4000+ options)
- Strong international support
- Free to start

**Why Not #1:**
- Less information on recipient self-service support
- Integration time variable (not as quick as Tremendous)
- No mention of revenue sharing

---

### 5. Giftbit

**Pricing:**
- "No added fees or delays"
- Face value cost model

**Payout Options:**
- Global rewards and incentives
- Multiple brand options

**Developer Experience:**
- **Integration:** "Hours not days"
- Developer-friendly API

**Customer Support:**
- **Customer Success:** Included at no cost
- **Recipient Support:** "Industry-leading" at no cost
- Keeps recipients "in good hands"

**Why Consider:**
- Explicit recipient support at no extra cost
- Fast integration claims
- Good for global deployment

**Why Not #1:**
- Less detailed information available
- Smaller catalog than Tremendous
- Less proven at scale

---

## Technical Implementation Requirements

### Tremendous API Integration (Recommended)

**Authentication:**
```bash
Authorization: Bearer YOUR_API_KEY
```

**Create Order Endpoint:**
```
POST https://api.tremendous.com/api/v2/orders
```

**Request Example (for $10 Amazon card):**
```json
{
  "payment": {
    "funding_source_id": "FUNDING_SOURCE_ID"
  },
  "reward": {
    "value": {
      "denomination": 10,
      "currency_code": "USD"
    },
    "delivery": {
      "method": "EMAIL"
    },
    "recipient": {
      "email": "winner@example.com",
      "name": "Contest Winner"
    },
    "products": ["PRODUCT_ID"]
  }
}
```

**Response:**
```json
{
  "order": {
    "id": "ORDER_ID",
    "status": "EXECUTED",
    "reward": {
      "id": "REWARD_ID",
      "delivery_status": "DELIVERED",
      "redemption_link": "https://tremendous.com/redeem/..."
    }
  }
}
```

**Laravel Implementation Pattern:**
```php
use Illuminate\Support\Facades\Http;

class GiftCardService
{
    protected $apiKey;
    protected $fundingSourceId;

    public function sendGiftCard(User $winner, float $amount = 10.00)
    {
        $response = Http::withToken($this->apiKey)
            ->post('https://api.tremendous.com/api/v2/orders', [
                'payment' => [
                    'funding_source_id' => $this->fundingSourceId
                ],
                'reward' => [
                    'value' => [
                        'denomination' => $amount,
                        'currency_code' => 'USD'
                    ],
                    'delivery' => [
                        'method' => 'EMAIL'
                    ],
                    'recipient' => [
                        'email' => $winner->email,
                        'name' => $winner->name
                    ]
                ]
            ]);

        return $response->json();
    }
}
```

---

## Rick's Cost Scenarios

### Conservative Estimate (30 winners/month)
- **Face value:** 30 × $10 = $300
- **Credit card fee (3%):** $9
- **Total:** **$309/month**
- **Annual:** **$3,708**

### Moderate Growth (100 winners/month - multiple locations)
- **Face value:** 100 × $10 = $1,000
- **Bank transfer funding:** FREE (setup ACH)
- **Total:** **$1,000/month**
- **Annual:** **$12,000**

### High Volume (300 winners/month - sponsor funded)
- **Face value:** 300 × $10 = $3,000
- **Revenue sharing kickback:** ~2-5% ($60-150)
- **Net cost:** **$2,850-2,940/month**
- **Annual:** **$34,200-35,280**
- **Sponsor-funded model:** Purina pays, Rick profits from platform fees

---

## Recipient Experience Comparison

### Tremendous (BEST)
1. Winner receives email: "You won! Click to claim your reward"
2. Click link → Choose from 2000+ options (Amazon, Visa, PayPal, charity, etc.)
3. Select preference → Receive code/transfer instantly
4. Issues? Self-service support portal or contact Tremendous directly

**User satisfaction:** ⭐⭐⭐⭐⭐ (choice = delight)
**Rick's support burden:** ✅ Minimal (Tremendous handles)

### Tango Card
1. Winner receives email with gift card code
2. Redeem on specified platform (Amazon, Starbucks, etc.)
3. Issues? Chatbot → escalate to Tango support

**User satisfaction:** ⭐⭐⭐⭐ (good but less choice)
**Rick's support burden:** ✅ Minimal (Tango chatbot deflects)

### Amazon Direct
1. Winner receives email with Amazon gift code
2. Apply to Amazon account
3. Issues? Contact Amazon support (generic, not API-specific)

**User satisfaction:** ⭐⭐⭐⭐ (Amazon = trusted)
**Rick's support burden:** ⚠️ Moderate (Amazon support not designed for this)

---

## Database Schema Requirements

### Gift Cards Table
```sql
CREATE TABLE gift_cards (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    winner_id BIGINT UNSIGNED NOT NULL,
    order_id VARCHAR(255) NOT NULL UNIQUE, -- Tremendous order ID
    reward_id VARCHAR(255) NOT NULL UNIQUE, -- Tremendous reward ID
    amount DECIMAL(8,2) NOT NULL,
    currency VARCHAR(3) DEFAULT 'USD',
    status ENUM('pending', 'delivered', 'redeemed', 'failed') DEFAULT 'pending',
    redemption_link TEXT,
    delivery_method VARCHAR(50) DEFAULT 'EMAIL',
    delivered_at TIMESTAMP NULL,
    redeemed_at TIMESTAMP NULL,
    provider VARCHAR(50) DEFAULT 'tremendous',
    provider_response JSON,
    error_message TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (winner_id) REFERENCES winners(id),
    INDEX idx_status (status),
    INDEX idx_user_id (user_id),
    INDEX idx_delivered_at (delivered_at)
);
```

### Support Tickets Table (Optional - for tracking)
```sql
CREATE TABLE gift_card_support_tickets (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    gift_card_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NOT NULL,
    issue_type VARCHAR(100),
    description TEXT,
    status ENUM('open', 'escalated', 'resolved') DEFAULT 'open',
    escalated_to_provider BOOLEAN DEFAULT FALSE,
    provider_ticket_id VARCHAR(255) NULL,
    resolved_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (gift_card_id) REFERENCES gift_cards(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
);
```

---

## Integration Checklist

### Phase 1: Setup (Week 1)
- [ ] Sign up for Tremendous account
- [ ] Complete KYC verification
- [ ] Get API credentials (sandbox + production)
- [ ] Set up funding source (bank account recommended for zero fees)
- [ ] Test sandbox integration
- [ ] Verify recipient email delivery

### Phase 2: Development (Week 2-3)
- [ ] Create `GiftCardService` class in Laravel
- [ ] Implement order creation endpoint
- [ ] Add webhook handling for delivery confirmation
- [ ] Build admin panel integration (Filament 4)
- [ ] Create user dashboard for redemption links
- [ ] Add error handling and retry logic

### Phase 3: Testing (Week 4)
- [ ] Test successful delivery flow
- [ ] Test error scenarios (API down, invalid email, etc.)
- [ ] Verify recipient support portal access
- [ ] Load test: Can handle 10 simultaneous winners?
- [ ] Confirm webhook reliability

### Phase 4: Production Launch
- [ ] Switch to production API keys
- [ ] Fund account with initial balance ($500 recommended)
- [ ] Monitor first 10 deliveries closely
- [ ] Document support escalation process for Rick
- [ ] Set up alerts for failed deliveries

---

## Risk Mitigation

### What if Tremendous has an outage?
**Backup plan:** Manual gift card purchase + email delivery
- Rick keeps $100 Amazon/Visa cards on hand
- Admin panel has "Manual Distribution" button
- Logs manual distributions for reconciliation

### What if recipient doesn't receive email?
**Solution 1:** Tremendous resend feature (automated)
**Solution 2:** Admin panel shows redemption link - Rick can copy/paste
**Solution 3:** User dashboard shows all redemption links

### What if user disputes they won?
**Protection:** Database stores:
- Question ID, user answer, timestamp
- IP address, geolocation at submission
- Winner validation logic (first correct answer)
- Gift card order ID linked to winner record

### What if gift card is fraudulent/stolen?
**Tremendous handles:** Their fraud detection + recipient verification
**Rick's protection:** User accounts linked to email verification
**Audit trail:** All submissions logged with IP/geolocation

---

## Sponsor Integration Strategy

### Current (Rick-Funded):
```
Winner → Tremendous API → $10 gift card → Winner chooses reward
Cost: Rick pays $10 (+ optional 3% if credit card)
```

### Future (Sponsor-Funded - Purina Example):
```
Winner → Check sponsor balance → Tremendous API → $10 Purina-branded email → Winner chooses reward
Cost: Purina pays $10 + platform fee to Rick (2-5%)
Revenue: Rick earns $0.20-0.50 per redemption
```

**Platform fee justification for sponsors:**
- Access to verified dog owner audience
- Geolocation data proves physical presence at dog stations
- Analytics dashboard shows ROI (engagement, redemption rates)
- Branded email delivery (Purina logo + message)
- Lower cost than traditional advertising ($10 direct to consumer vs. $50+ CPM)

---

## Final Recommendation

**Use Tremendous for Rick's Golden Question Contest**

**Why:**
1. **Cost-effective:** Zero fees beyond face value (if bank-funded)
2. **Recipient satisfaction:** 2000+ choices = higher engagement
3. **Support deflection:** Recipient self-service = Rick's time saved
4. **Developer-friendly:** Fast integration, good docs, sandbox testing
5. **Scalable:** Revenue sharing at high volume benefits Rick
6. **Sponsor-ready:** Professional enough for Purina pitch meetings

**Implementation timeline:** 2-3 weeks
**Monthly cost:** $300-309 (30 winners) to $1,000+ (growth)
**Risk level:** Low (sandbox testing, good support, established platform)

**Next steps:**
1. Shane sets up Tremendous account (Rick's business details)
2. Integrate during Phase 2 development
3. Test with fake winners in sandbox
4. Launch with monitoring
5. Document for Rick's admin training

---

**Research completed by Vision System**
**Contact Tremendous:** [https://www.tremendous.com/](https://www.tremendous.com/)
**API Docs:** [https://developers.tremendous.com/](https://developers.tremendous.com/)
