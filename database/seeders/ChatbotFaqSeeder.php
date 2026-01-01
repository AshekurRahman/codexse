<?php

namespace Database\Seeders;

use App\Models\ChatbotFaq;
use Illuminate\Database\Seeder;

class ChatbotFaqSeeder extends Seeder
{
    public function run(): void
    {
        $faqs = [
            // Account & Login
            [
                'question' => 'How do I create an account?',
                'answer' => '<p>Creating an account is easy:</p><ol><li>Click the <strong>Register</strong> button in the top right corner</li><li>Enter your name, email, and create a password</li><li>Verify your email address by clicking the link we send you</li><li>You\'re all set!</li></ol>',
                'keywords' => 'register, signup, sign up, create account, new account, join',
                'category' => 'Account',
                'sort_order' => 1,
                'is_suggested' => true, // Show in chat widget
            ],
            [
                'question' => 'How do I reset my password?',
                'answer' => '<p>To reset your password:</p><ol><li>Go to the <strong>Login</strong> page</li><li>Click <strong>Forgot Password</strong></li><li>Enter your email address</li><li>Check your email for a reset link</li><li>Create a new password</li></ol><p>If you don\'t receive the email, check your spam folder.</p>',
                'keywords' => 'password, forgot, reset, change password, lost password, recover',
                'category' => 'Account',
                'sort_order' => 2,
            ],
            [
                'question' => 'How do I update my profile?',
                'answer' => '<p>To update your profile:</p><ol><li>Log in to your account</li><li>Click on your name in the top right corner</li><li>Select <strong>Profile</strong></li><li>Update your information and click <strong>Save</strong></li></ol>',
                'keywords' => 'profile, update, edit, change name, avatar, bio',
                'category' => 'Account',
                'sort_order' => 3,
            ],

            // Orders & Purchases
            [
                'question' => 'How do I purchase a product?',
                'answer' => '<p>To purchase a product:</p><ol><li>Browse our products and find one you like</li><li>Click <strong>Add to Cart</strong></li><li>Go to your cart and click <strong>Checkout</strong></li><li>Enter your payment details</li><li>Complete the purchase</li></ol><p>You\'ll receive a confirmation email with your download links.</p>',
                'keywords' => 'buy, purchase, order, checkout, payment, how to buy',
                'category' => 'Orders',
                'sort_order' => 4,
                'is_suggested' => true, // Show in chat widget
            ],
            [
                'question' => 'Where can I find my orders?',
                'answer' => '<p>To view your orders:</p><ol><li>Log in to your account</li><li>Go to your <strong>Dashboard</strong></li><li>Click on <strong>Purchases</strong></li></ol><p>Here you can see all your past orders, download products, and view license keys.</p>',
                'keywords' => 'orders, purchases, order history, my orders, find order',
                'category' => 'Orders',
                'sort_order' => 5,
            ],
            [
                'question' => 'What payment methods do you accept?',
                'answer' => '<p>We accept the following payment methods:</p><ul><li><strong>Wallet Balance</strong> - Use your Codexse wallet for instant checkout</li><li><strong>Credit/Debit Cards</strong> - Visa, Mastercard, American Express</li><li><strong>Digital Wallets</strong> - Apple Pay, Google Pay</li></ul><p>All payments are securely processed through Stripe.</p>',
                'keywords' => 'payment, credit card, debit card, visa, mastercard, pay, payment methods, wallet',
                'category' => 'Payments',
                'sort_order' => 6,
            ],
            [
                'question' => 'What is the wallet and how do I use it?',
                'answer' => '<p>Your Codexse wallet is a secure balance you can use for purchases:</p><ol><li>Go to <strong>Dashboard</strong> → <strong>Wallet</strong></li><li>Click <strong>Add Funds</strong> to deposit money</li><li>Use your balance at checkout for instant payment</li></ol><p>Benefits: Faster checkout, no need to enter card details each time, and you can receive refunds directly to your wallet.</p>',
                'keywords' => 'wallet, balance, add funds, deposit, wallet balance, codexse wallet',
                'category' => 'Payments',
                'sort_order' => 7,
                'is_suggested' => true,
            ],
            [
                'question' => 'How do I add funds to my wallet?',
                'answer' => '<p>To add funds to your wallet:</p><ol><li>Log in to your account</li><li>Go to <strong>Dashboard</strong> → <strong>Wallet</strong></li><li>Click <strong>Add Funds</strong></li><li>Enter the amount you want to deposit</li><li>Complete the payment with your card</li></ol><p>Funds are available immediately after payment.</p>',
                'keywords' => 'add funds, deposit, wallet deposit, add money, top up',
                'category' => 'Payments',
                'sort_order' => 8,
            ],

            // Downloads & Licenses
            [
                'question' => 'How do I download my purchased products?',
                'answer' => '<p>To download your products:</p><ol><li>Log in to your account</li><li>Go to <strong>Dashboard</strong> → <strong>Downloads</strong></li><li>Click the <strong>Download</strong> button next to your product</li></ol><p>You can also find download links in your purchase confirmation email.</p>',
                'keywords' => 'download, get files, access files, download product, where to download',
                'category' => 'Downloads',
                'sort_order' => 7,
                'is_suggested' => true, // Show in chat widget
            ],
            [
                'question' => 'Where can I find my license key?',
                'answer' => '<p>Your license keys are available in:</p><ol><li>Go to <strong>Dashboard</strong> → <strong>Purchases</strong></li><li>Click on the order containing your product</li><li>Your license key will be displayed there</li></ol><p>License keys are also sent in your purchase confirmation email.</p>',
                'keywords' => 'license, key, activation, license key, serial, product key',
                'category' => 'Downloads',
                'sort_order' => 8,
            ],
            [
                'question' => 'How many times can I download a product?',
                'answer' => '<p>You can download your purchased products <strong>unlimited times</strong>. There\'s no limit on downloads.</p><p>Just log in to your account and go to your Downloads page whenever you need to re-download.</p>',
                'keywords' => 'download limit, how many downloads, re-download, download again',
                'category' => 'Downloads',
                'sort_order' => 9,
            ],

            // Refunds
            [
                'question' => 'What is your refund policy?',
                'answer' => '<p>We offer a <strong>30-day money-back guarantee</strong> on most products.</p><p>To request a refund:</p><ol><li>Go to <strong>Dashboard</strong> → <strong>Support</strong></li><li>Create a new support ticket</li><li>Select <strong>Refund</strong> as the category</li><li>Explain the reason for your refund request</li></ol><p>We\'ll process your request within 2-3 business days.</p>',
                'keywords' => 'refund, money back, return, cancel, refund policy, get refund',
                'category' => 'Refunds',
                'sort_order' => 10,
                'is_suggested' => true, // Show in chat widget
            ],
            [
                'question' => 'How long does a refund take?',
                'answer' => '<p>Once approved, refunds are processed within <strong>5-10 business days</strong> depending on your payment method and bank.</p><p>Credit card refunds typically appear within 5-7 days.</p>',
                'keywords' => 'refund time, how long refund, when refund, refund processing',
                'category' => 'Refunds',
                'sort_order' => 11,
            ],

            // Technical Support
            [
                'question' => 'How do I contact support?',
                'answer' => '<p>You can contact our support team by:</p><ol><li>Go to <strong>Dashboard</strong> → <strong>Support</strong></li><li>Click <strong>Create New Ticket</strong></li><li>Describe your issue and submit</li></ol><p>We typically respond within 24 hours.</p>',
                'keywords' => 'support, help, contact, ticket, customer service, assistance',
                'category' => 'Technical',
                'sort_order' => 12,
            ],
            [
                'question' => 'The product is not working, what should I do?',
                'answer' => '<p>If you\'re experiencing issues:</p><ol><li>Check the product documentation</li><li>Make sure you meet the system requirements</li><li>Try re-downloading and reinstalling</li><li>Clear your cache if it\'s a web product</li></ol><p>If the issue persists, please create a support ticket with details about the problem.</p>',
                'keywords' => 'not working, broken, issue, problem, bug, error, help',
                'category' => 'Technical',
                'sort_order' => 13,
            ],

            // General
            [
                'question' => 'Do you offer discounts?',
                'answer' => '<p>Yes! We offer various discounts:</p><ul><li><strong>Coupon codes</strong> - Check our newsletter for exclusive codes</li><li><strong>Bundle deals</strong> - Save when buying multiple products</li><li><strong>Seasonal sales</strong> - Special discounts during holidays</li></ul><p>Subscribe to our newsletter to stay updated on deals!</p>',
                'keywords' => 'discount, coupon, sale, promo, promotion, deal, save money',
                'category' => 'General',
                'sort_order' => 14,
            ],
            [
                'question' => 'How do I become a seller?',
                'answer' => '<p>To become a seller:</p><ol><li>Log in to your account</li><li>Go to your <strong>Dashboard</strong></li><li>Click <strong>Become a Seller</strong></li><li>Fill out the application form</li><li>Wait for approval (usually 1-2 business days)</li></ol><p>Once approved, you can start listing your products!</p>',
                'keywords' => 'seller, sell, vendor, become seller, sell products, author',
                'category' => 'General',
                'sort_order' => 15,
            ],
        ];

        foreach ($faqs as $faq) {
            ChatbotFaq::create($faq);
        }
    }
}
