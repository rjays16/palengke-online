import type { OrderStatus, DriverInfo, PromoBanner } from './types'

export const orderStatus: OrderStatus = {
  eta: 'Arriving by 1:05 PM',
  statusText: 'On time · Out for delivery!',
  statusColor: 'green',
  deliveryType: 'Standard Delivery'
}

export const driverInfo: DriverInfo = {
  name: 'Juan Dela Cruz',
  rating: '5.0',
  vehicleId: 'ABC 1234 · Honda Click',
  image: 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d'
}

export const promoBanner: PromoBanner = {
  title: 'Save up to ₱50 on your next order',
  subtitle: 'Use code NEXT50 on your next delivery!'
}

export const storeLocation: [number, number] = [8.4820, 124.6472]
export const customerLocation: [number, number] = [8.4920, 124.6600]
export const driverPos: [number, number] = [8.4860, 124.6530]
