import { reactive } from 'vue';

export const globalNotifications = reactive({
  items: [],
  /**
   * Add a notification to the bell menu
   * @param {string} message - Notification text
   * @param {string} type - 'success', 'error', 'primary', 'info'
   */
  add(message, type = 'success') {
    const id = Date.now() + Math.random().toString(36).substr(2, 5);
    this.items.unshift({
      id,
      message,
      type,
      time: new Date(),
    });

    // Keep memory clean, max 15 notifications
    if (this.items.length > 15) {
      this.items.pop();
    }
  },
  clear() {
    this.items = [];
  }
});

// Create a globally accessible function so any component (even outside Vue's setup tree or legacy options API)
// can trigger a notification without needing to import this file.
// Example usage: window.showAdminNotification("Đã thêm khách hàng thành công!", "success")
if (typeof window !== 'undefined') {
  window.showAdminNotification = (message, type = 'success') => {
    globalNotifications.add(message, type);
  };
}
