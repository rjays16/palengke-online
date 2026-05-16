export const mockOrderData = {
  id: '1234',
  sellerName: "Juan's Fresh Market",
  pickupAddress: 'Stall #15, CDO City Palengke, Cagayan de Oro',
  pickupPhone: '0912 345 6789',
  customerName: 'Maria Santos',
  deliveryAddress: 'Brgy. Carmen, Cagayan de Oro City',
  customerPhone: '0918 765 4321',
  itemCount: 5,
  totalAmount: 850,
  paymentMethod: 'cash' as const,
  driverEarnings: 85,
  pickupInstructions: 'Ask for Juan at Stall #15',
  items: ['2kg Pork', '1kg Tomatoes', '3pcs Cabbage']
}

export const driverStartLocation: [number, number] = [8.4850, 124.6510]
export const vendorLocation: [number, number] = [8.4820, 124.6472]
export const customerLocation: [number, number] = [8.4920, 124.6600]
