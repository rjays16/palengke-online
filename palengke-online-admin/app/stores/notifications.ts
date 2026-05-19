export const useNotificationsStore = defineStore("notifications", () => {
  const allNotifications = ref([
    {
      id: 1,
      type: "order",
      message: "New order #ORD-1042 placed by Maria Santos",
      time: "2 mins ago",
      read: false,
    },
    {
      id: 2,
      type: "order",
      message: "Order #ORD-1041 marked as In Transit",
      time: "8 mins ago",
      read: false,
    },
    {
      id: 3,
      type: "system",
      message: "System backup completed successfully",
      time: "14 mins ago",
      read: false,
    },
    {
      id: 4,
      type: "alert",
      message: "Dispute opened on Order #ORD-1038 by Liza Morales",
      time: "22 mins ago",
      read: false,
    },
    {
      id: 5,
      type: "order",
      message: "New seller registration: Rosa's Produce",
      time: "1 hour ago",
      read: true,
    },
    {
      id: 6,
      type: "system",
      message: "New driver application: Leo Magno",
      time: "2 hours ago",
      read: true,
    },
    {
      id: 7,
      type: "promo",
      message: "Promo code PALENGKE20 used 45 times today",
      time: "3 hours ago",
      read: true,
    },
    {
      id: 8,
      type: "warning",
      message: "Payment payout for Pedro Merchants failed",
      time: "5 hours ago",
      read: true,
    },
    {
      id: 9,
      type: "order",
      message: "Order #ORD-1033 delivered successfully",
      time: "6 hours ago",
      read: true,
    },
    {
      id: 10,
      type: "system",
      message: "New app version 2.4.1 ready for deployment",
      time: "1 day ago",
      read: true,
    },
    {
      id: 11,
      type: "promo",
      message: "Flash sale: 20% off on all seafood",
      time: "1 day ago",
      read: true,
    },
    {
      id: 12,
      type: "warning",
      message: "Driver Mark Reyes reported late delivery",
      time: "2 days ago",
      read: true,
    },
  ]);
  let nextId = 13;

  const unreadCount = computed(
    () => allNotifications.value.filter((n) => !n.read).length,
  );

  function markAllRead() {
    allNotifications.value.forEach((n) => (n.read = true));
  }

  function markRead(id) {
    const n = allNotifications.value.find((n) => n.id === id);
    if (n) n.read = true;
  }

  function deleteNotification(id) {
    allNotifications.value = allNotifications.value.filter((n) => n.id !== id);
  }

  function addNotification(data) {
    allNotifications.value.unshift({ id: nextId++, ...data });
  }

  return {
    allNotifications,
    unreadCount,
    markAllRead,
    markRead,
    deleteNotification,
    addNotification,
  };
});
