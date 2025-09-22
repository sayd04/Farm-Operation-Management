import { defineStore } from 'pinia';
import axios from 'axios';

export const useAlertsStore = defineStore('alerts', {
  state: () => ({
    toasts: [],
    subscribed: false,
    pollingIntervalId: null,
  }),

  actions: {
    pushToast(toast) {
      const id = Date.now() + Math.random();
      this.toasts.push({ id, ...toast });
      setTimeout(() => this.removeToast(id), toast.duration || 6000);
    },
    removeToast(id) {
      this.toasts = this.toasts.filter(t => t.id !== id);
    },

    subscribe(userId) {
      if (this.subscribed || !window.Echo) {
        if (!window.Echo && !this.pollingIntervalId) {
          this.startPolling();
        }
        return;
      }
      try {
        // Private user channel
        window.Echo.private(`users.${userId}`)
          .listen('LowInventoryAlert', (e) => {
            this.pushToast({ title: 'Low Stock', message: e.message || 'An item is below minimum stock', type: 'warning' });
          })
          .listen('NewOrderPlaced', (e) => {
            this.pushToast({ title: 'New Order', message: 'You have a new order', type: 'info' });
          })
          .listen('WeatherAlert', (e) => {
            this.pushToast({ title: 'Weather Alert', message: e.alert?.title || 'Severe weather warning', type: 'danger' });
          });

        // Global weather alerts
        window.Echo.channel('weather')
          .listen('WeatherAlert', (e) => {
            this.pushToast({ title: 'Weather Alert', message: e.alert?.title || 'Severe weather warning', type: 'danger' });
          });

        this.subscribed = true;
      } catch (e) {
        console.warn('Echo subscribe failed, fallback to polling', e);
        this.startPolling();
      }
    },

    startPolling() {
      if (this.pollingIntervalId) return;
      // Poll basic alerts endpoint every 30s as fallback
      this.pollingIntervalId = setInterval(async () => {
        try {
          const res = await axios.get('/api/alerts');
          (res.data || []).forEach((a) => this.pushToast({ title: a.title, message: a.message, type: a.type }));
        } catch (_) {}
      }, 30000);
    },
    stopPolling() {
      if (this.pollingIntervalId) {
        clearInterval(this.pollingIntervalId);
        this.pollingIntervalId = null;
      }
    }
  }
});

