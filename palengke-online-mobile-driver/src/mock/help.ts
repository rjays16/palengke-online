import {
  helpCircleOutline, cashOutline, cubeOutline,
  personOutline, bicycleOutline
} from 'ionicons/icons'
import type { Topic, Faq } from './types'

export const mockTopics: Topic[] = [
  { id: 'all', name: 'All', icon: helpCircleOutline },
  { id: 'earnings', name: 'Earnings', icon: cashOutline },
  { id: 'orders', name: 'Orders', icon: cubeOutline },
  { id: 'account', name: 'Account', icon: personOutline },
  { id: 'delivery', name: 'Delivery', icon: bicycleOutline },
]

export const mockFaqs: Faq[] = [
  { id: 1, topic: 'earnings', question: 'When do I get paid?', answer: 'Earnings are credited to your account daily. You can withdraw to your GCash or bank account anytime.' },
  { id: 2, topic: 'earnings', question: 'How is my delivery fee calculated?', answer: 'Delivery fee is based on distance. The base fee starts at ₱50 and increases by ₱10 per kilometer. Tips from customers are added on top.' },
  { id: 3, topic: 'earnings', question: 'What is the commission deduction?', answer: 'Palengke Online deducts 10% commission from each delivery fee. Tips are NOT subject to commission - you keep 100% of tips!' },
  { id: 4, topic: 'orders', question: 'How do I accept an order?', answer: 'When a new order appears, tap the Accept button within 30 seconds. If Auto Accept is ON in settings, orders will be automatically accepted for you.' },
  { id: 5, topic: 'orders', question: 'Can I cancel an accepted order?', answer: 'Yes, but cancellations affect your acceptance rate. To cancel, go to Active Delivery, tap More, then Cancel Order. Please select the correct reason.' },
  { id: 6, topic: 'orders', question: 'What if the seller is not available?', answer: 'Call or chat the seller first. If still unreachable, you can cancel the order with the reason "Seller not available" without affecting your rate.' },
  { id: 7, topic: 'account', question: 'How do I update my documents?', answer: 'Go to Profile > Documents and tap on any document to update it. Make sure documents are not expired to avoid account suspension.' },
  { id: 8, topic: 'account', question: 'Why is my account suspended?', answer: 'Accounts may be suspended for: expired documents, low acceptance rate below 50%, multiple cancellations, or violation of terms of service.' },
  { id: 9, topic: 'delivery', question: 'What if the customer is not home?', answer: 'Try calling the customer first. If unreachable after 10 minutes, take a photo of the location and contact support through the Help Center.' },
  { id: 10, topic: 'delivery', question: 'What if items are damaged?', answer: 'Report damaged items immediately through the app. Take photos as evidence. Palengke Online will investigate and coordinate with the seller.' },
]
