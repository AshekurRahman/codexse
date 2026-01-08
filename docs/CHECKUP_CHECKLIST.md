# Website Feature Checkup Checklist

A comprehensive 100-item checklist for auditing all features of the CodexSE marketplace.

---

## AUTHENTICATION & SECURITY (1-20)

1. **Password Policy**: Verify 12+ character passwords with mixed case, numbers, symbols enforced (`AppServiceProvider.php`)
2. **Breached Password Check**: Confirm `Password::uncompromised()` blocks passwords from known breaches
3. **Two-Factor Authentication**: Test TOTP 2FA setup, verification, and backup codes for all users
4. **Admin 2FA Enforcement**: Verify admins cannot bypass 2FA requirement (`AdminTwoFactorMiddleware.php`)
5. **Account Lockout**: Test 5 failed logins trigger 15-minute lockout (`LoginRequest.php` MAX_ATTEMPTS=5)
6. **Login Rate Limiting**: Confirm brute force attempts are rate-limited per IP and email
7. **Session Regeneration**: Verify session ID regenerates after login to prevent session fixation
8. **Open Redirect Prevention**: Test redirect URLs validate to same domain only (`AuthenticatedSessionController.php`)
9. **Email Change Verification**: Confirm email changes require token verification (`ProfileController.php`)
10. **Password Change Security**: Test current password required before password change
11. **Remember Me Tokens**: Verify remember tokens are hashed and rotated on use
12. **Logout Invalidation**: Confirm logout destroys session and invalidates tokens
13. **Admin IP Whitelist**: Test admin panel IP restriction works if enabled (`AdminIpWhitelist.php`)
14. **Failed Login Logging**: Verify failed attempts logged with IP, email, user agent
15. **Security Notifications**: Test admin alerts on suspicious login patterns (`SecurityNotificationService.php`)
16. **CSRF Protection**: Confirm CSRF tokens validated on all POST/PUT/DELETE requests
17. **Session Encryption**: Verify `SESSION_ENCRYPT=true` in production (`config/session.php`)
18. **Cookie Security**: Test Secure, HttpOnly, SameSite=strict flags on cookies
19. **HTTPS Enforcement**: Confirm HTTP redirects to HTTPS in production (`ForceHttps.php`)
20. **HSTS Header**: Verify Strict-Transport-Security header with 1-year max-age

---

## INPUT VALIDATION & XSS PREVENTION (21-35)

21. **SQL Injection Blocking**: Test InputSanitization blocks UNION SELECT, DROP TABLE, etc.
22. **XSS Pattern Detection**: Verify `<script>`, `javascript:`, event handlers are blocked
23. **Command Injection Prevention**: Test shell patterns (`;ls`, `|cat`, backticks) are blocked
24. **Path Traversal Detection**: Confirm `../` and URL-encoded variants are blocked
25. **HTML Purifier**: Verify `mews/purifier` sanitizes user-generated content
26. **Form Request Validation**: Audit all FormRequest classes have proper validation rules
27. **File Upload Restrictions**: Test file type whitelist, size limits on all uploads
28. **Avatar Upload Security**: Verify avatar uploads validate image type and size (`ProfileController.php`)
29. **Numeric Input Bounds**: Test price/quantity fields reject negative and overflow values
30. **Email Validation**: Confirm email inputs use proper RFC validation rules
31. **JSON API Validation**: Verify API endpoints validate JSON structure and types
32. **Webhook Payload Exclusion**: Test webhooks bypass sanitization correctly (`InputSanitization.php`)
33. **Encoded Input Detection**: Verify sanitization checks URL-decoded values too
34. **Content Security Policy**: Test CSP header with nonces blocks inline scripts (`SecurityHeaders.php`)
35. **CSP Nonce Directive**: Verify `@cspNonce` used in all Blade inline scripts/styles

---

## PAYMENT - STRIPE (36-45)

36. **Stripe API Keys**: Verify Stripe keys load from database or .env (`StripeService.php`)
37. **Stripe Checkout Session**: Test checkout session creation with correct line items
38. **Stripe Success Redirect**: Confirm success page loads with order details after payment
39. **Stripe Cancel Redirect**: Test cancel redirects back to checkout with cart intact
40. **Stripe Webhook Endpoint**: Verify `/webhooks/stripe` receives events correctly
41. **Stripe Signature Verification**: Confirm webhook signatures validated before processing
42. **checkout.session.completed**: Test order created and marked paid on webhook
43. **payment_intent.succeeded**: Verify payment intent handling for direct payments
44. **Stripe Refunds**: Test refund processing through Stripe API
45. **Stripe Error Handling**: Verify payment failures show user-friendly error messages

---

## PAYMENT - PAYPAL (46-52)

46. **PayPal API Credentials**: Verify PayPal client ID and secret configured correctly
47. **PayPal Order Creation**: Test PayPal order creation with correct amounts
48. **PayPal Approval Redirect**: Confirm redirect to PayPal for payment approval
49. **PayPal Capture**: Test payment capture after buyer approval
50. **PayPal Webhook Handling**: Verify PayPal IPN/webhooks process order status
51. **PayPal Refunds**: Test refund processing through PayPal API
52. **PayPal Sandbox Mode**: Verify sandbox/live mode switches correctly

---

## PAYMENT - WALLET SYSTEM (53-65)

53. **Wallet Balance Display**: Verify wallet shows available and held balance separately
54. **Wallet Deposit - Stripe**: Test adding funds via Stripe payment
55. **Wallet Deposit - PayPal**: Test adding funds via PayPal payment
56. **Wallet Checkout Payment**: Test paying full order with wallet balance
57. **Wallet Partial Payment**: Test wallet + card split payment at checkout
58. **Wallet Hold Creation**: Verify funds held during checkout (`WalletService.php` holdFunds)
59. **Wallet Hold Release**: Test held funds released on order cancellation
60. **Wallet Hold Capture**: Verify held funds captured on order completion
61. **Wallet Overdraft Prevention**: Test cannot spend more than available balance
62. **Wallet Idempotency Keys**: Confirm duplicate transactions prevented (`WalletIdempotencyKey.php`)
63. **Wallet Transaction History**: Verify all transactions logged with correct types
64. **Wallet Expiring Holds**: Test scheduled command expires old holds (`ExpireWalletHolds.php`)
65. **Wallet Idempotency Cleanup**: Verify old keys cleaned up (`CleanupWalletIdempotencyKeys.php`)

---

## PAYMENT - ESCROW SYSTEM (66-72)

66. **Escrow Fund Hold**: Verify seller funds held until buyer confirms delivery
67. **Escrow Platform Fee**: Confirm 20% platform fee calculated correctly
68. **Escrow Manual Release**: Test buyer can release funds to seller
69. **Escrow Auto-Release**: Verify funds auto-release after 3 days (`EscrowService.php`)
70. **Escrow Dispute Hold**: Test funds remain held during active disputes
71. **Escrow Refund Flow**: Verify refund releases escrow back to buyer
72. **Escrow Balance Tracking**: Confirm escrow amounts tracked separately from wallet

---

## PAYMENT - PAYOUTS (73-80)

73. **Payout Request Creation**: Test sellers can request payout with minimum balance
74. **Payout Method Selection**: Verify payout method options (PayPal, Payoneer, Bank)
75. **Payout Approval Queue**: Confirm payouts require admin approval (`PayoutResource.php`)
76. **Payout Approval Notification**: Test seller notified on payout approval (`PayoutApproved.php`)
77. **Payout Rejection Notification**: Verify seller notified on rejection with reason (`PayoutRejected.php`)
78. **Payout Processing**: Test actual payout execution through payment provider
79. **Payoneer Integration**: Verify Payoneer payouts work if configured (`PayoneerService.php`)
80. **Payout History**: Confirm sellers can view payout history with status

---

## PRODUCTS & DIGITAL GOODS (81-90)

81. **Product Creation**: Test product creation with title, description, price, category
82. **Product File Upload**: Verify secure file upload for digital downloads
83. **Product Images**: Test product image upload and gallery display
84. **Product Categories**: Verify category assignment and category filtering
85. **Product Search**: Test search returns relevant results by title/description
86. **Product Pricing**: Verify price display with correct currency formatting
87. **Product Visibility**: Test draft/published status controls visibility
88. **Product Bulk Import**: Verify CSV import with validation (`BulkProductImport.php`)
89. **License Key Generation**: Test license keys generated for applicable products (`LicenseService.php`)
90. **License Validation API**: Verify `/api/license/validate` endpoint with authentication

---

## SERVICES & ORDERS (91-100)

91. **Service Creation**: Test service creation with packages (Basic/Standard/Premium)
92. **Service Packages**: Verify package pricing and feature display
93. **Service Requirements**: Test buyer requirements collection during order
94. **Custom Quote Request**: Verify buyers can request custom quotes (`CustomQuoteController.php`)
95. **Order Placement**: Test complete order flow from cart to confirmation
96. **Order Status Updates**: Verify status changes (pending, processing, completed, cancelled)
97. **Order Notifications**: Test email notifications sent to buyer and seller
98. **Invoice Generation**: Verify PDF invoice generation with correct details
99. **Order History**: Test buyer order history with filtering and search
100. **Seller Order Management**: Verify seller dashboard shows orders with actions

---

## DOWNLOADS & DELIVERY (101-108)

101. **Download Authorization**: Verify only order owners can download files (`DownloadController.php`)
102. **Download Limit Enforcement**: Test download limits enforced with race condition protection
103. **Download Limit Locking**: Confirm `lockForUpdate()` prevents limit bypass
104. **Download Tracking**: Verify downloads logged with IP, timestamp, user agent
105. **Download Link Expiry**: Test download links expire after configured time
106. **Multiple File Downloads**: Verify ZIP packaging for multiple files
107. **Download Resume Support**: Test partial content / range requests work
108. **Delivery Confirmation**: Verify buyer can mark order as delivered

---

## DISPUTES & REFUNDS (109-118)

109. **Dispute Creation**: Test buyer can open dispute with reason and evidence
110. **Dispute Categories**: Verify dispute reason categories (not delivered, not as described, etc.)
111. **Dispute Evidence Upload**: Test file upload for dispute evidence
112. **Dispute Response**: Verify seller can respond with counter-evidence
113. **Dispute Admin Review**: Test admin can review and resolve disputes (`DisputeResource.php`)
114. **Dispute Resolution Options**: Verify refund/partial refund/release options
115. **Dispute Notifications**: Test all parties receive status update emails
116. **Refund Request**: Verify buyers can request refunds for eligible orders
117. **Refund Processing**: Test refund execution through original payment method (`RefundService.php`)
118. **Refund Wallet Credit**: Verify option to refund to wallet balance

---

## USER PROFILES & ACCOUNTS (119-128)

119. **Profile Update**: Test profile information update (name, bio, location)
120. **Avatar Upload**: Verify avatar upload with image validation
121. **Email Change Flow**: Test email change requires verification
122. **Password Update**: Verify password change requires current password
123. **Account Deletion**: Test account deletion with data handling
124. **Profile Privacy**: Verify privacy settings control public visibility
125. **User Dashboard**: Test dashboard shows relevant user statistics
126. **Notification Preferences**: Verify users can configure notification settings
127. **Activity History**: Test user can view their activity log
128. **Connected Accounts**: Verify social login connections if enabled

---

## SELLER FEATURES (129-140)

129. **Seller Application**: Test seller application submission process (`SellerApplicationController.php`)
130. **Seller Verification**: Verify identity verification workflow (`VerificationController.php`)
131. **Seller Approval**: Test admin approval/rejection of applications (`SellerResource.php`)
132. **Seller Dashboard**: Verify seller analytics and earnings display
133. **Seller Earnings**: Test earnings calculation from completed orders
134. **Seller Reviews**: Verify review display on seller profile
135. **Seller Rating**: Test average rating calculation from reviews
136. **Seller Products**: Verify sellers can manage their products (`ProductController.php`)
137. **Seller Services**: Test sellers can manage their services (`ServiceController.php`)
138. **Seller Contracts**: Verify job contract management (`JobContractController.php`)
139. **Seller Proposals**: Test proposal submission for job postings (`JobProposalController.php`)
140. **Seller Subscription**: Verify subscription tier access and features (`SubscriptionController.php`)

---

## JOB MARKETPLACE (141-148)

141. **Job Posting Creation**: Test job posting with requirements and budget (`JobPostingController.php`)
142. **Job Categories**: Verify job category filtering and search
143. **Job Proposals**: Test freelancers can submit proposals
144. **Proposal Review**: Verify clients can review and accept proposals
145. **Contract Creation**: Test contract created from accepted proposal
146. **Milestone Payments**: Verify milestone-based payment structure
147. **Contract Completion**: Test contract completion and payment release
148. **Job Posting Expiry**: Verify expired job postings handled correctly

---

## REVIEWS & RATINGS (149-155)

149. **Review Submission**: Test buyers can review after order completion
150. **Review Validation**: Verify only verified purchasers can review
151. **Star Rating System**: Test 1-5 star rating submission
152. **Review Display**: Verify reviews display on product/service pages
153. **Review Moderation**: Test admin can moderate reviews (`ReviewObserver.php`)
154. **Review Response**: Verify sellers can respond to reviews
155. **Rating Aggregation**: Test average rating calculation accuracy

---

## NOTIFICATIONS & EMAILS (156-165)

156. **Email Configuration**: Verify SMTP/mail driver configured correctly
157. **Order Confirmation Email**: Test buyer receives order confirmation
158. **Order Notification - Seller**: Verify seller receives new order notification
159. **Payment Confirmation**: Test payment success email
160. **Dispute Notifications**: Verify dispute status change emails
161. **Payout Notifications**: Test payout approval/rejection emails
162. **Newsletter System**: Verify newsletter signup and sending (`NewsletterController.php`)
163. **Newsletter Unsubscribe**: Test unsubscribe link works correctly
164. **Email Templates**: Verify all email templates render correctly
165. **Email Queue Processing**: Test queued emails process correctly

---

## ADMIN PANEL - CORE (166-180)

166. **Admin Login**: Verify admin authentication with 2FA requirement
167. **Admin Dashboard**: Test dashboard metrics and charts display correctly
168. **User Management**: Verify create, edit, suspend, delete users (`UserResource.php`)
169. **User Role Assignment**: Test role and permission assignment
170. **Order Management**: Verify admin can view and manage all orders (`OrderResource.php`)
171. **Product Management**: Test admin product oversight and moderation
172. **Service Management**: Verify admin service moderation (`ServiceResource.php`)
173. **Dispute Management**: Test admin dispute resolution (`DisputeResource.php`)
174. **Refund Management**: Verify admin refund processing (`RefundResource.php`)
175. **Payout Management**: Test admin payout approval workflow (`PayoutResource.php`)
176. **Wallet Management**: Verify admin wallet oversight (`WalletResource.php`)
177. **Seller Management**: Test seller approval and management (`SellerResource.php`)
178. **Support Tickets**: Verify ticket management (`SupportTicketResource.php`)
179. **Newsletter Subscribers**: Test subscriber management (`NewsletterSubscriberResource.php`)
180. **Activity Logs**: Verify admin action audit trail

---

## ADMIN PANEL - SETTINGS (181-190)

181. **Site Settings**: Test general site settings update
182. **Payment Settings**: Verify payment gateway configuration
183. **Email Settings**: Test SMTP configuration
184. **Security Settings**: Verify security feature toggles
185. **SEO Settings**: Test meta tags and SEO configuration
186. **Custom Code**: Verify custom header/footer code injection (review security)
187. **Backup Manager**: Test backup creation and restoration (`BackupManager.php`)
188. **Security Dashboard**: Verify security metrics display (`SecurityDashboard.php`)
189. **License API Key**: Configure license_api_key in Settings for API authentication
190. **Maintenance Mode**: Test maintenance mode toggle and bypass

---

## LOGGING & MONITORING (191-200)

191. **Slow Query Monitoring**: Verify queries >1s logged with severity levels (`AppServiceProvider.php`)
192. **Error Logging**: Test 403/404/419/500 errors logged with context
193. **Security Event Logging**: Verify security events recorded (`security_logs` table)
194. **Activity Logging**: Test user actions logged (`ActivityLogService.php`)
195. **Failed Login Logging**: Verify failed attempts tracked (`failed_login_attempts` table)
196. **Fraud Detection Logging**: Test fraud events logged to fraud channel
197. **Log Sanitization**: Verify sensitive data not logged (`CreateSanitizedLogger.php`)
198. **Log Rotation**: Test log files rotated and old logs cleaned
199. **Log Channels**: Verify stack, daily, errors, fraud channels configured
200. **Slack Alerts**: Test critical alerts sent to Slack if configured

---

## INFRASTRUCTURE & PERFORMANCE (201-215)

201. **Database Indexes**: Verify indexes on frequently queried columns
202. **Database Encryption**: Test sensitive fields encrypted (`Order.php` encrypted casts)
203. **Cache Configuration**: Verify cache driver works (Redis/file)
204. **Session Configuration**: Test session driver and encryption
205. **Queue Configuration**: Verify queue driver and worker processing
206. **Scheduled Tasks**: Test Laravel scheduler runs correctly (`routes/console.php`)
207. **Hold Expiration Job**: Verify `ExpireWalletHolds` runs on schedule
208. **Idempotency Cleanup Job**: Test `CleanupWalletIdempotencyKeys` runs
209. **Webhook Cleanup Job**: Verify `CleanupProcessedWebhooks` runs
210. **File Storage**: Test file storage configuration (local/S3)
211. **Private File Storage**: Verify private files not publicly accessible
212. **Public File Storage**: Test public file serving with symlink
213. **SSL Certificate**: Verify SSL certificate valid and not expiring
214. **Database Backups**: Test automated backups running and restorable
215. **Disk Space Monitoring**: Verify adequate disk space for logs/uploads

---

## API & WEBHOOKS (216-225)

216. **License API Authentication**: Test `LicenseApiAuth` middleware validates keys
217. **API Rate Limiting**: Verify API endpoints rate limited
218. **Webhook Replay Protection**: Test duplicate webhooks rejected (`WebhookReplayProtection.php`)
219. **Webhook Signature Validation**: Verify all payment webhooks validate signatures
220. **Processed Webhook Tracking**: Test processed webhooks logged (`ProcessedWebhook.php`)
221. **API Error Responses**: Verify consistent error response format
222. **API Versioning**: Test API versioning if implemented
223. **CORS Configuration**: Verify CORS headers for API endpoints
224. **API Documentation**: Check API documentation is up to date
225. **API Key Rotation**: Test API key rotation procedures

---

## SECURITY HARDENING (226-240)

226. **Malicious IP Blocking**: Test auto-blocking after suspicious requests (`BlockMaliciousIps.php`)
227. **IP Block Expiry**: Verify blocked IPs expire after configured time
228. **CIDR Range Blocking**: Test IP range blocking with CIDR notation
229. **Suspicious Pattern Detection**: Verify wp-admin, .env, .git access triggers alert
230. **Rate Limiting Middleware**: Test `DynamicRateLimiter` blocks excessive requests
231. **Rate Limit Whitelist**: Verify whitelisted IPs bypass rate limits
232. **Secure File Upload Rules**: Test `SecureFileUpload` validation rule
233. **No-Cache Headers**: Verify sensitive responses have no-cache headers (`NoCacheSensitiveResponse.php`)
234. **Security Alerts Table**: Test security alerts created for incidents
235. **Admin Notification Service**: Verify admins notified of security events
236. **Fraud Detection Service**: Test fraud detection on transactions (`FraudDetectionService.php`)
237. **Fraud Thresholds**: Verify fraud detection thresholds configured correctly
238. **Geo-Anomaly Detection**: Test geographic anomaly detection in fraud service
239. **Velocity Checks**: Verify transaction velocity limits work
240. **New Account Risk Scoring**: Test new accounts flagged as higher risk

---

## DATABASE & MIGRATIONS (241-250)

241. **Run Pending Migrations**: Execute `php artisan migrate:status` to check pending
242. **Wallet Holds Table**: Verify `wallet_holds` table created and indexed
243. **Wallet Idempotency Table**: Test `wallet_idempotency_keys` table exists
244. **Processed Webhooks Table**: Verify `processed_webhooks` table created
245. **Order Encryption Migration**: Test sensitive order fields encrypted
246. **Payout Approval Fields**: Verify approval fields added to payouts table
247. **Email Change Fields**: Test email change verification fields on users
248. **Coupon Usage Constraint**: Verify unique constraint on coupon_usages
249. **Foreign Key Constraints**: Test foreign keys enforce referential integrity
250. **Soft Deletes**: Verify soft delete columns on appropriate tables

---

## FINAL VERIFICATION (251-260)

251. **Complete Checkout Flow - Stripe**: Test full purchase with Stripe payment
252. **Complete Checkout Flow - PayPal**: Test full purchase with PayPal payment
253. **Complete Checkout Flow - Wallet**: Test full purchase with wallet balance
254. **Complete Checkout Flow - Partial**: Test wallet + card split payment
255. **Complete Seller Flow**: Test product creation to payout receipt
256. **Complete Dispute Flow**: Test dispute creation to resolution
257. **Complete Refund Flow**: Test refund request to completion
258. **Mobile Responsiveness**: Verify all pages work on mobile devices
259. **Browser Compatibility**: Test on Chrome, Firefox, Safari, Edge
260. **Performance Testing**: Verify page load times under 3 seconds

---

## RECOMMENDATIONS FOR IMMEDIATE ACTION

| Priority | Item | Action Required |
|----------|------|-----------------|
| HIGH | Backup Manager | Replace `exec()` with `spatie/laravel-backup` package |
| HIGH | License API Key | Configure `license_api_key` in Settings |
| HIGH | Run Migrations | Execute pending migrations for new tables |
| MEDIUM | Test Payment Flows | Verify all checkout flows after security fixes |
| MEDIUM | Queue Workers | Ensure queue workers running for scheduled jobs |
| MEDIUM | Email Configuration | Verify SMTP settings for transactional emails |
| LOW | API Documentation | Update API docs with new endpoints |
| LOW | Log Monitoring | Set up log aggregation and alerting |

---

*Generated: 2026-01-04*
*Total Items: 260*
