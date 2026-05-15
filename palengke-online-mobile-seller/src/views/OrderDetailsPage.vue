<template>
  <ion-page>
    <ion-header>
      <ion-toolbar color="success">
        <ion-buttons slot="start">
          <ion-back-button default-href="/orders"></ion-back-button>
        </ion-buttons>
        <ion-title>Order Details</ion-title>
        <ion-buttons slot="end">
          <ion-button @click="showMoreOptions">
            <ion-icon :icon="ellipsisVerticalOutline"></ion-icon>
          </ion-button>
        </ion-buttons>
      </ion-toolbar>
    </ion-header>

    <ion-content :fullscreen="true">
      <div v-if="order" class="order-details-container">
        
        <!-- Order Status Card -->
        <div class="status-card" :class="`status-${order.status}`">
          <div class="status-icon">
            <ion-icon :icon="getStatusIcon(order.status)"></ion-icon>
          </div>
          <div class="status-info">
            <h2>{{ getStatusTitle(order.status) }}</h2>
            <p>{{ getStatusMessage(order.status) }}</p>
          </div>
        </div>

        <!-- Order Info -->
        <div class="info-card">
          <div class="card-header">
            <h3>Order Information</h3>
            <ion-badge :color="getStatusColor(order.status)">
              {{ order.status.toUpperCase() }}
            </ion-badge>
          </div>
          <div class="info-row">
            <span class="label">Order ID:</span>
            <span class="value">#{{ order.id }}</span>
          </div>
          <div class="info-row">
            <span class="label">Order Date:</span>
            <span class="value">{{ formatFullDate(order.date) }}</span>
          </div>
          <div class="info-row">
            <span class="label">Total Items:</span>
            <span class="value">{{ order.itemCount }} item{{ order.itemCount > 1 ? 's' : '' }}</span>
          </div>
        </div>

        <!-- Customer Info -->
        <div class="info-card">
          <div class="card-header">
            <h3>Customer Information</h3>
          </div>
          <div class="customer-details">
            <div class="customer-row">
              <ion-icon :icon="personOutline"></ion-icon>
              <div class="customer-text">
                <span class="label">Name</span>
                <span class="value">{{ order.customerName }}</span>
              </div>
            </div>
            <div class="customer-row">
              <ion-icon :icon="callOutline"></ion-icon>
              <div class="customer-text">
                <span class="label">Phone</span>
                <span class="value">{{ order.customerPhone }}</span>
              </div>
              <ion-button fill="clear" size="small" @click="callCustomer">
                <ion-icon :icon="callOutline"></ion-icon>
              </ion-button>
            </div>
            <div class="customer-row">
              <ion-icon :icon="locationOutline"></ion-icon>
              <div class="customer-text">
                <span class="label">Delivery Address</span>
                <span class="value">{{ order.deliveryAddress }}</span>
              </div>
              <ion-button fill="clear" size="small" @click="openMap">
                <ion-icon :icon="navigateOutline"></ion-icon>
              </ion-button>
            </div>
          </div>
        </div>

        <!-- Order Items -->
        <div class="info-card">
          <div class="card-header">
            <h3>Order Items</h3>
          </div>
          <div class="items-list">
            <div v-for="(item, index) in order.items" :key="index" class="item-row">
              <div class="item-info">
                <span class="item-name">{{ item.name }}</span>
                <span class="item-qty">{{ item.qty }}{{ item.unit }}</span>
              </div>
              <span class="item-price">₱{{ formatPrice(item.price) }}</span>
            </div>
          </div>
          <div class="order-summary">
            <div class="summary-row subtotal">
              <span>Subtotal:</span>
              <span>₱{{ formatPrice(order.total) }}</span>
            </div>
            <div class="summary-row">
              <span>Delivery Fee:</span>
              <span>₱50.00</span>
            </div>
            <div class="summary-row total">
              <span>Total Amount:</span>
              <span>₱{{ formatPrice(order.total + 50) }}</span>
            </div>
          </div>
        </div>

        <!-- Payment Info -->
        <div class="info-card">
          <div class="card-header">
            <h3>Payment Information</h3>
          </div>
          <div class="info-row">
            <span class="label">Payment Method:</span>
            <span class="value">Cash on Delivery</span>
          </div>
          <div class="info-row">
            <span class="label">Payment Status:</span>
            <ion-badge color="warning">Pending</ion-badge>
          </div>
        </div>

        <!-- Dispute Info -->
        <div class="info-card dispute-card" v-if="dispute">
          <div class="card-header">
            <h3>⚠️ Customer Dispute</h3>
            <ion-badge color="danger">OPEN</ion-badge>
          </div>
          <div class="dispute-details">
            <div class="info-row">
              <span class="label">Reason:</span>
              <span class="value">{{ dispute.reasonLabel }}</span>
            </div>
            <div class="info-row">
              <span class="label">Description:</span>
              <span class="value">{{ dispute.description }}</span>
            </div>
            <div class="info-row">
              <span class="label">Filed on:</span>
              <span class="value">{{ dispute.date }}</span>
            </div>
            <div class="info-row" v-if="dispute.image">
              <span class="label">Photo:</span>
              <img :src="dispute.image" class="dispute-photo" />
            </div>
          </div>
        </div>

        <!-- Action Buttons -->
        <div class="action-buttons" v-if="order.status !== 'completed' && order.status !== 'cancelled'">
          <ion-button
            v-if="order.status === 'pending'"
            expand="block"
            color="danger"
            fill="outline"
            @click="cancelOrder"
          >
            <ion-icon :icon="closeCircleOutline" slot="start"></ion-icon>
            Decline Order
          </ion-button>
          
          <ion-button
            v-if="order.status === 'pending'"
            expand="block"
            color="success"
            @click="acceptOrder"
          >
            <ion-icon :icon="checkmarkCircleOutline" slot="start"></ion-icon>
            Accept Order
          </ion-button>

          <ion-button
            v-if="order.status === 'processing'"
            expand="block"
            color="success"
            @click="completeOrder"
          >
            <ion-icon :icon="checkmarkCircleOutline" slot="start"></ion-icon>
            Mark as Completed
          </ion-button>
        </div>

        <!-- Completed Message -->
        <div v-if="order.status === 'completed'" class="completed-message">
          <ion-icon :icon="checkmarkCircleOutline"></ion-icon>
          <p>This order has been completed</p>
        </div>

      </div>

      <!-- Loading State -->
      <div v-else class="loading-container">
        <div class="logo-wrapper">
          <img src="/assets/images/logo-without-circle.png" alt="Palengke Online" class="logo-image">
          <svg class="loading-circle" viewBox="0 0 200 200">
            <circle 
              cx="100" 
              cy="100" 
              r="85" 
              fill="none" 
              stroke="#e0e0e0" 
              stroke-width="6"
            />
            <circle 
              class="animated-stroke-spin" 
              cx="100" 
              cy="100" 
              r="85" 
              fill="none" 
              stroke="#2c2c2c" 
              stroke-width="6"
              stroke-linecap="round"
            />
          </svg>
        </div>
        <div class="loading-text">LOADING...</div>
      </div>
    </ion-content>
  </ion-page>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { getDisputeByOrderId } from '../services/disputeService';
import {
  IonPage,
  IonHeader,
  IonToolbar,
  IonTitle,
  IonContent,
  IonButtons,
  IonButton,
  IonBackButton,
  IonIcon,
  IonBadge,
  IonSpinner,
  alertController,
  actionSheetController,
  toastController
} from '@ionic/vue';
import {
  ellipsisVerticalOutline,
  checkmarkCircleOutline,
  timeOutline,
  closeCircleOutline,
  personOutline,
  callOutline,
  locationOutline,
  navigateOutline,
  hourglassOutline,
  rocketOutline,
  printOutline,
  shareOutline
} from 'ionicons/icons';

const router = useRouter();
const route = useRoute();

const order = ref(null);

const dispute = computed(() => {
  if (!order.value) return null
  return getDisputeByOrderId(order.value.id)
})

// Mock order data (TODO: Replace with API call)
const mockOrders = {
  '001': {
    id: '001',
    customerName: 'Maria Santos',
    date: '2026-01-02T10:30:00',
    status: 'pending',
    itemCount: 5,
    total: 1250.50,
    items: [
      { name: 'Tomatoes', qty: '1', unit: 'kg', price: 80 },
      { name: 'Lettuce', qty: '1/2', unit: 'kg', price: 45 },
      { name: 'Carrots', qty: '1.5', unit: 'kg', price: 60 },
      { name: 'Onions', qty: '2', unit: 'kg', price: 50 },
      { name: 'Potatoes', qty: '3', unit: 'kg', price: 100 }
    ],
    customerPhone: '+63 917 123 4567',
    deliveryAddress: '123 Main St, Cagayan de Oro City'
  },
  '002': {
    id: '002',
    customerName: 'Juan Dela Cruz',
    date: '2026-01-02T09:15:00',
    status: 'processing',
    itemCount: 3,
    total: 850.00,
    items: [
      { name: 'Cabbage', qty: '2', unit: 'kg', price: 70 },
      { name: 'Eggplant', qty: '1.5', unit: 'kg', price: 120 },
      { name: 'Bell Peppers', qty: '1', unit: 'kg', price: 150 }
    ],
    customerPhone: '+63 918 234 5678',
    deliveryAddress: '456 Oak Ave, Cagayan de Oro City'
  },
  '003': {
    id: '003',
    customerName: 'Ana Reyes',
    date: '2026-01-01T14:20:00',
    status: 'completed',
    itemCount: 7,
    total: 2100.75,
    items: [
      { name: 'Mangoes', qty: '2', unit: 'kg', price: 300 },
      { name: 'Bananas', qty: '3', unit: 'kg', price: 150 },
      { name: 'Oranges', qty: '2.5', unit: 'kg', price: 200 },
      { name: 'Apples', qty: '1.5', unit: 'kg', price: 250 },
      { name: 'Grapes', qty: '1', unit: 'kg', price: 180 },
      { name: 'Watermelon', qty: '1', unit: 'pc', price: 200 },
      { name: 'Pineapple', qty: '2', unit: 'pcs', price: 160 }
    ],
    customerPhone: '+63 919 345 6789',
    deliveryAddress: '789 Pine Rd, Cagayan de Oro City'
  },
  '004': {
    id: '004',
    customerName: 'Pedro Garcia',
    date: '2026-01-01T11:45:00',
    status: 'cancelled',
    itemCount: 2,
    total: 450.00,
    items: [
      { name: 'Garlic', qty: '1/2', unit: 'kg', price: 90 },
      { name: 'Ginger', qty: '1/4', unit: 'kg', price: 80 }
    ],
    customerPhone: '+63 920 456 7890',
    deliveryAddress: '321 Elm St, Cagayan de Oro City'
  },
  '005': {
    id: '005',
    customerName: 'Lisa Ramos',
    date: '2026-01-02T12:00:00',
    status: 'pending',
    itemCount: 4,
    total: 980.00,
    items: [
      { name: 'Pork (Liempo)', qty: '1.5', unit: 'kg', price: 350 },
      { name: 'Chicken', qty: '2', unit: 'kg', price: 280 },
      { name: 'Bangus', qty: '3', unit: 'pcs', price: 200 },
      { name: 'Shrimp', qty: '1/2', unit: 'kg', price: 150 }
    ],
    customerPhone: '+63 921 567 8901',
    deliveryAddress: '654 Maple Dr, Cagayan de Oro City'
  }
};

onMounted(() => {
  loadOrderDetails();
});

const loadOrderDetails = () => {
  const orderId = route.params.id;
  // TODO: Replace with actual API call
  setTimeout(() => {
    order.value = mockOrders[orderId];
    
    // If order not found, show error and go back
    if (!order.value) {
      alertController.create({
        header: 'Order Not Found',
        message: `Order #${orderId} does not exist.`,
        buttons: [
          {
            text: 'OK',
            handler: () => {
              router.push('/orders');
            }
          }
        ]
      }).then(alert => alert.present());
    }
  }, 500);
};

const formatPrice = (price) => {
  return price.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
};

const formatFullDate = (dateString) => {
  const date = new Date(dateString);
  return date.toLocaleString('en-US', {
    month: 'long',
    day: 'numeric',
    year: 'numeric',
    hour: 'numeric',
    minute: '2-digit',
    hour12: true
  });
};

const getStatusColor = (status) => {
  const colors = {
    pending: 'warning',
    processing: 'primary',
    completed: 'success',
    cancelled: 'danger'
  };
  return colors[status] || 'medium';
};

const getStatusIcon = (status) => {
  const icons = {
    pending: hourglassOutline,
    processing: rocketOutline,
    completed: checkmarkCircleOutline,
    cancelled: closeCircleOutline
  };
  return icons[status] || timeOutline;
};

const getStatusTitle = (status) => {
  const titles = {
    pending: 'Order Received',
    processing: 'Order is Being Prepared',
    completed: 'Order Completed',
    cancelled: 'Order Cancelled'
  };
  return titles[status] || 'Unknown Status';
};

const getStatusMessage = (status) => {
  const messages = {
    pending: 'Waiting for your confirmation',
    processing: 'Preparing the order for delivery',
    completed: 'Order has been delivered successfully',
    cancelled: 'This order was cancelled'
  };
  return messages[status] || '';
};

const showSuccessModal = async (message) => {
  const alert = await alertController.create({
    cssClass: 'success-modal',
    header: ' ',
    message: message,
    buttons: ['OK']
  });
  
  await alert.present();
  
  // Add the image after modal is presented
  setTimeout(() => {
    const alertElement = document.querySelector('.success-modal .alert-message');
    if (alertElement) {
      // Clear the text content
      const textContent = alertElement.textContent;
      alertElement.innerHTML = '';
      
      // Create wrapper for centering
      const wrapper = document.createElement('div');
      wrapper.style.display = 'flex';
      wrapper.style.flexDirection = 'column';
      wrapper.style.alignItems = 'center';
      wrapper.style.textAlign = 'center';
      
      // Create and add the image
      const img = document.createElement('img');
      img.src = '/assets/images/checkmark_success.png';
      img.style.width = '80px';
      img.style.height = '80px';
      img.style.margin = '0 auto 16px auto';
      img.style.display = 'block';
      
      // Create and add the text
      const textDiv = document.createElement('div');
      textDiv.textContent = textContent;
      textDiv.style.fontSize = '18px';
      textDiv.style.fontWeight = '600';
      textDiv.style.color = '#1a1a1a';
      textDiv.style.textAlign = 'center';
      
      // Append to wrapper
      wrapper.appendChild(img);
      wrapper.appendChild(textDiv);
      
      // Append wrapper to alert
      alertElement.appendChild(wrapper);
    }
  }, 50);
  
  // Auto dismiss after 2 seconds
  setTimeout(() => {
    alert.dismiss();
  }, 2000);
};

const acceptOrder = async () => {
  const alert = await alertController.create({
    header: 'Accept Order',
    message: `Accept this order from ${order.value.customerName}?`,
    buttons: [
      {
        text: 'Cancel',
        role: 'cancel'
      },
      {
        text: 'Accept',
        handler: async () => {
          order.value.status = 'processing';
          await showSuccessModal('Order accepted successfully!');
        }
      }
    ]
  });
  await alert.present();
};

const completeOrder = async () => {
  const alert = await alertController.create({
    header: 'Complete Order',
    message: 'Mark this order as completed?',
    buttons: [
      {
        text: 'Cancel',
        role: 'cancel'
      },
      {
        text: 'Complete',
        handler: async () => {
          order.value.status = 'completed';
          await showSuccessModal('Order completed successfully!');
        }
      }
    ]
  });
  await alert.present();
};

const cancelOrder = async () => {
  const alert = await alertController.create({
    header: 'Decline Order',
    message: 'Are you sure you want to decline this order?',
    inputs: [
      {
        name: 'reason',
        type: 'textarea',
        placeholder: 'Reason for declining (optional)'
      }
    ],
    buttons: [
      {
        text: 'Cancel',
        role: 'cancel'
      },
      {
        text: 'Decline',
        role: 'destructive',
        handler: async () => {
          order.value.status = 'cancelled';
          
          // Show error/warning modal instead of toast
          const declineAlert = await alertController.create({
            cssClass: 'decline-modal',
            header: ' ',
            message: '<font-awesome-icon icon="times-circle" /> Order declined',
            buttons: ['OK']
          });
          await declineAlert.present();
          
          // Auto dismiss after 2 seconds
          setTimeout(() => {
            declineAlert.dismiss();
          }, 2000);
        }
      }
    ]
  });
  await alert.present();
};

const callCustomer = () => {
  window.location.href = `tel:${order.value.customerPhone}`;
};

const openMap = () => {
  const address = encodeURIComponent(order.value.deliveryAddress);
  window.open(`https://www.google.com/maps/search/?api=1&query=${address}`, '_blank');
};

const showMoreOptions = async () => {
  const actionSheet = await actionSheetController.create({
    header: 'More Options',
    buttons: [
      {
        text: 'Print Receipt',
        icon: printOutline,
        handler: () => {
          showToast('Print feature coming soon');
        }
      },
      {
        text: 'Share Order',
        icon: shareOutline,
        handler: () => {
          showToast('Share feature coming soon');
        }
      },
      {
        text: 'Call Customer',
        icon: callOutline,
        handler: () => {
          callCustomer();
        }
      },
      {
        text: 'Cancel',
        role: 'cancel'
      }
    ]
  });
  await actionSheet.present();
};

const showToast = async (message, color = 'dark') => {
  const toast = await toastController.create({
    message,
    duration: 2000,
    color,
    position: 'top'
  });
  await toast.present();
};
</script>

<style scoped>
.order-details-container {
  padding: 16px;
  padding-bottom: 100px;
}

/* Status Card */
.status-card {
  background: white;
  border-radius: 16px;
  padding: 20px;
  margin-bottom: 16px;
  display: flex;
  align-items: center;
  gap: 16px;
  box-shadow: 0 2px 12px rgba(0, 0, 0, 0.1);
}

.status-card.status-pending {
  background: linear-gradient(135deg, #FFF3E0 0%, #FFE0B2 100%);
}

.status-card.status-processing {
  background: linear-gradient(135deg, #E3F2FD 0%, #BBDEFB 100%);
}

.status-card.status-completed {
  background: linear-gradient(135deg, #E8F5E9 0%, #C8E6C9 100%);
}

.status-card.status-cancelled {
  background: linear-gradient(135deg, #FFEBEE 0%, #FFCDD2 100%);
}

.status-icon {
  width: 60px;
  height: 60px;
  border-radius: 50%;
  background: rgba(255, 255, 255, 0.9);
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.status-icon ion-icon {
  font-size: 32px;
  color: #4CAF50;
}

.status-card.status-pending .status-icon ion-icon {
  color: #FF9800;
}

.status-card.status-processing .status-icon ion-icon {
  color: #2196F3;
}

.status-card.status-cancelled .status-icon ion-icon {
  color: #f44336;
}

.status-info h2 {
  font-size: 18px;
  font-weight: 700;
  color: #1a1a1a;
  margin: 0 0 4px 0;
}

.status-info p {
  font-size: 13px;
  color: #666;
  margin: 0;
}

/* Info Cards */
.info-card {
  background: white;
  border-radius: 16px;
  padding: 16px;
  margin-bottom: 12px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
}

.card-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 16px;
  padding-bottom: 12px;
  border-bottom: 2px solid #f0f0f0;
}

.card-header h3 {
  font-size: 16px;
  font-weight: 700;
  color: #1a1a1a;
  margin: 0;
}

.info-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 10px 0;
  border-bottom: 1px solid #f5f5f5;
}

.info-row:last-child {
  border-bottom: none;
}

.info-row .label {
  font-size: 14px;
  color: #666;
}

.info-row .value {
  font-size: 14px;
  font-weight: 600;
  color: #1a1a1a;
}

/* Customer Details */
.customer-details {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.customer-row {
  display: flex;
  align-items: flex-start;
  gap: 12px;
}

.customer-row > ion-icon {
  font-size: 24px;
  color: #4CAF50;
  flex-shrink: 0;
  margin-top: 2px;
}

.customer-text {
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.customer-text .label {
  font-size: 12px;
  color: #999;
}

.customer-text .value {
  font-size: 14px;
  font-weight: 600;
  color: #1a1a1a;
  line-height: 1.4;
}

/* Items List */
.items-list {
  margin-bottom: 16px;
}

.item-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 12px 0;
  border-bottom: 1px solid #f5f5f5;
}

.item-row:last-child {
  border-bottom: none;
}

.item-info {
  display: flex;
  align-items: center;
  gap: 8px;
  flex: 1;
}

.item-name {
  font-size: 14px;
  font-weight: 600;
  color: #1a1a1a;
}

.item-qty {
  font-size: 13px;
  color: #999;
}

.item-price {
  font-size: 14px;
  font-weight: 700;
  color: #4CAF50;
}

/* Order Summary */
.order-summary {
  padding-top: 16px;
  border-top: 2px solid #f0f0f0;
}

.summary-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 8px 0;
  font-size: 14px;
  color: #666;
}

.summary-row.subtotal {
  color: #999;
  font-size: 13px;
}

.summary-row.total {
  padding-top: 12px;
  margin-top: 8px;
  border-top: 1px solid #f0f0f0;
  font-size: 16px;
  font-weight: 700;
  color: #1a1a1a;
}

.summary-row.total span:last-child {
  color: #4CAF50;
  font-size: 20px;
}

/* Action Buttons */
.action-buttons {
  display: flex;
  flex-direction: column;
  gap: 12px;
  margin-top: 24px;
}

.action-buttons ion-button {
  --border-radius: 12px;
  height: 52px;
  font-size: 15px;
  font-weight: 600;
  text-transform: none;
}

/* Dispute Card */
.dispute-card { border: 1.5px solid #fecaca; background: #fef2f2; }
.dispute-details { margin-top: 8px; }
.dispute-photo { width: 100%; max-height: 140px; object-fit: cover; border-radius: 8px; margin-top: 8px; }

/* Completed Message */
.completed-message {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 40px 20px;
  text-align: center;
}

.completed-message ion-icon {
  font-size: 64px;
  color: #4CAF50;
  margin-bottom: 16px;
}

.completed-message p {
  font-size: 16px;
  font-weight: 600;
  color: #666;
  margin: 0;
}

/* Loading Container */
.loading-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  min-height: 100vh;
  background: white;
}

.logo-wrapper {
  position: relative;
  width: 200px;
  height: 200px;
  display: flex;
  justify-content: center;
  align-items: center;
}

.logo-image {
  width: 140px;
  height: auto;
  position: relative;
  z-index: 2;
}

.loading-circle {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  z-index: 1;
  transform: rotate(-90deg);
  animation: rotateSVG 2s linear infinite;
}

.animated-stroke-spin {
  stroke-dasharray: 400, 534;
  stroke-dashoffset: 0;
}

.loading-text {
  margin-top: 40px;
  font-size: 14px;
  font-weight: 500;
  color: #333;
  letter-spacing: 3px;
  text-transform: uppercase;
  animation: textPulse 1.8s ease-in-out infinite;
}

@keyframes rotateSVG {
  0% {
    transform: rotate(-90deg);
  }
  100% {
    transform: rotate(270deg);
  }
}

@keyframes textPulse {
  0%, 100% {
    opacity: 0.3;
  }
  50% {
    opacity: 1;
  }
}

/* Success Modal */
:deep(.success-modal .alert-wrapper) {
  border-radius: 20px;
  text-align: center;
  max-width: 320px;
  padding: 30px 20px;
}

:deep(.success-modal .alert-head) {
  padding: 0;
  min-height: 0;
}

:deep(.success-modal .alert-message) {
  font-size: 18px;
  font-weight: 600;
  color: #1a1a1a;
  padding: 20px 10px;
}

:deep(.success-modal button) {
  background: #4CAF50;
  color: white;
  font-weight: 600;
  border-radius: 12px;
  height: 44px;
}

/* Decline Modal */
:deep(.decline-modal .alert-wrapper) {
  border-radius: 20px;
  text-align: center;
  max-width: 320px;
  padding: 30px 20px;
}

:deep(.decline-modal .alert-head) {
  padding: 0;
  min-height: 0;
}

:deep(.decline-modal .alert-message) {
  font-size: 18px;
  font-weight: 600;
  color: #1a1a1a;
  padding: 20px 10px;
}

:deep(.decline-modal button) {
  background: #f44336;
  color: white;
  font-weight: 600;
  border-radius: 12px;
  height: 44px;
}

/* Responsive */
@media (max-width: 480px) {
  .status-icon {
    width: 50px;
    height: 50px;
  }

  .status-icon ion-icon {
    font-size: 28px;
  }

  .status-info h2 {
    font-size: 16px;
  }
}
</style>