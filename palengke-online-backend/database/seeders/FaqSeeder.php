<?php

namespace Database\Seeders;

use App\Models\Faq;
use Illuminate\Database\Seeder;

class FaqSeeder extends Seeder
{
    const DRIVER_FAQS = [
        ['question' => 'When do I get paid?', 'answer' => 'Earnings are credited to your account daily. You can withdraw to your GCash or bank account anytime.', 'topic' => 'earnings'],
        ['question' => 'How is my delivery fee calculated?', 'answer' => 'Delivery fee is based on distance. The base fee starts at ₱50 and increases by ₱10 per kilometer. Tips from customers are added on top.', 'topic' => 'earnings'],
        ['question' => 'What is the commission deduction?', 'answer' => 'Palengke Online deducts 10% commission from each delivery fee. Tips are NOT subject to commission — you keep 100% of tips!', 'topic' => 'earnings'],
        ['question' => 'How do I accept an order?', 'answer' => 'When a new order appears, tap the Accept button within 30 seconds. If Auto Accept is ON in settings, orders will be automatically accepted for you.', 'topic' => 'orders'],
        ['question' => 'Can I cancel an accepted order?', 'answer' => 'Yes, but cancellations affect your acceptance rate. To cancel, go to Active Delivery, tap More, then Cancel Order. Please select the correct reason.', 'topic' => 'orders'],
        ['question' => 'What if the seller is not available?', 'answer' => 'Call or chat the seller first. If still unreachable, you can cancel the order with the reason "Seller not available" without affecting your rate.', 'topic' => 'orders'],
        ['question' => 'How do I update my documents?', 'answer' => 'Go to Profile > Documents and tap on any document to update it. Make sure documents are not expired to avoid account suspension.', 'topic' => 'account'],
        ['question' => 'Why is my account suspended?', 'answer' => 'Accounts may be suspended for: expired documents, low acceptance rate below 50%, multiple cancellations, or violation of terms of service.', 'topic' => 'account'],
        ['question' => 'What if the customer is not home?', 'answer' => 'Try calling the customer first. If unreachable after 10 minutes, take a photo of the location and contact support through the Help Center.', 'topic' => 'delivery'],
        ['question' => 'What if items are damaged?', 'answer' => 'Report damaged items immediately through the app. Take photos as evidence. Palengke Online will investigate and coordinate with the seller.', 'topic' => 'delivery'],
    ];

    const SELLER_FAQS = [
        ['question' => 'How do I add a new product?', 'answer' => 'Go to the Products page, tap the + button in the top right, fill in the product details (name, price, category, stock), add a photo, and tap Save.', 'topic' => 'products'],
        ['question' => 'How do I accept an order?', 'answer' => 'When you receive a new order notification, go to the Orders page, tap on the pending order, review the details, and tap the "Accept" button.', 'topic' => 'orders'],
        ['question' => 'How do I receive payments?', 'answer' => 'Set up your payment methods (GCash or Bank Account) in your Profile settings. Payments will be transferred to your account after order completion.', 'topic' => 'payments'],
        ['question' => 'What are the supported units?', 'answer' => 'You can sell products by kilogram (kg), liter (L), or piece (pc). Select the appropriate unit when adding your product.', 'topic' => 'products'],
        ['question' => 'How do I chat with customers?', 'answer' => 'Go to the Messages page from the dashboard. You can view all your conversations with customers and drivers there.', 'topic' => 'orders'],
        ['question' => 'How do I mark an order as completed?', 'answer' => 'Open the order details, verify that the order has been delivered, then tap the "Mark as Completed" button.', 'topic' => 'orders'],
        ['question' => 'Can I edit my product information?', 'answer' => 'Yes! Go to Products, find your product, tap the edit icon (pencil), make your changes, and save.', 'topic' => 'products'],
        ['question' => 'How do I handle low stock?', 'answer' => 'You\'ll receive notifications when stock is low. Update your product stock in the Products page by editing the product.', 'topic' => 'products'],
        ['question' => 'What if I need to decline an order?', 'answer' => 'Open the order details and tap "Decline Order". You can optionally provide a reason for declining.', 'topic' => 'orders'],
        ['question' => 'How do I view my sales analytics?', 'answer' => 'Tap the Settings icon on the home page, then select "Analytics" to view your sales reports, top products, and performance metrics.', 'topic' => 'analytics'],
        ['question' => 'How do I change my shop information?', 'answer' => 'Go to Profile, tap on any field you want to edit (shop name, location, hours, etc.), make your changes, and save.', 'topic' => 'account'],
        ['question' => 'What is the delivery fee?', 'answer' => 'The standard delivery fee is ₱50. This is added to the customer\'s total at checkout.', 'topic' => 'orders'],
    ];

    public function run(): void
    {
        $count = 0;

        foreach (self::DRIVER_FAQS as $i => $faq) {
            Faq::create([
                'question' => $faq['question'],
                'answer' => $faq['answer'],
                'role' => 'driver',
                'topic' => $faq['topic'],
                'active' => true,
                'sort_order' => $i,
            ]);
            $count++;
        }

        foreach (self::SELLER_FAQS as $i => $faq) {
            Faq::create([
                'question' => $faq['question'],
                'answer' => $faq['answer'],
                'role' => 'seller',
                'topic' => $faq['topic'],
                'active' => true,
                'sort_order' => $i,
            ]);
            $count++;
        }

        $this->command->info("Seeded {$count} FAQs.");
    }
}
