<?php

namespace Database\Seeders;

use App\Models\Faq;
use Illuminate\Database\Seeder;

class FaqSeeder extends Seeder
{
    public function run(): void
    {
        $faqs = [
            ['question' => 'How do I add a new product?', 'answer' => 'Go to the Products page, tap the + button in the top right, fill in the product details (name, price, category, stock), add a photo, and tap Save.'],
            ['question' => 'How do I accept an order?', 'answer' => 'When you receive a new order notification, go to the Orders page, tap on the pending order, review the details, and tap the "Accept" button.'],
            ['question' => 'How do I receive payments?', 'answer' => 'Set up your payment methods (GCash or Bank Account) in your Profile settings. Payments will be transferred to your account after order completion.'],
            ['question' => 'What are the supported units?', 'answer' => 'You can sell products by kilogram (kg), liter (L), or piece (pc). Select the appropriate unit when adding your product.'],
            ['question' => 'How do I chat with customers?', 'answer' => 'Go to the Messages page from the dashboard. You can view all your conversations with customers and drivers there.'],
            ['question' => 'How do I mark an order as completed?', 'answer' => 'Open the order details, verify that the order has been delivered, then tap the "Mark as Completed" button.'],
            ['question' => 'Can I edit my product information?', 'answer' => 'Yes! Go to Products, find your product, tap the edit icon (pencil), make your changes, and save.'],
            ['question' => 'How do I handle low stock?', 'answer' => 'You\'ll receive notifications when stock is low. Update your product stock in the Products page by editing the product.'],
            ['question' => 'What if I need to decline an order?', 'answer' => 'Open the order details and tap "Decline Order". You can optionally provide a reason for declining.'],
            ['question' => 'How do I view my sales analytics?', 'answer' => 'Tap the Settings icon on the home page, then select "Analytics" to view your sales reports, top products, and performance metrics.'],
            ['question' => 'How do I change my shop information?', 'answer' => 'Go to Profile, tap on any field you want to edit (shop name, location, hours, etc.), make your changes, and save.'],
            ['question' => 'What is the delivery fee?', 'answer' => 'The standard delivery fee is ₱50. This is added to the customer\'s total at checkout.'],
        ];

        foreach ($faqs as $i => $faq) {
            Faq::create([
                'question' => $faq['question'],
                'answer' => $faq['answer'],
                'active' => true,
                'sort_order' => $i,
            ]);
        }

        $this->command->info('Seeded ' . count($faqs) . ' FAQs.');
    }
}
