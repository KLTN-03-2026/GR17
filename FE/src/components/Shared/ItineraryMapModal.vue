<template>
  <div v-if="show" class="modal-map-overlay">
    <div class="modal-map-container">
      <div class="modal-map-header">
        <h3><i class="fas fa-map-marked-alt"></i> Bản đồ lộ trình</h3>
        <button type="button" class="btn-close-map" @click="closeMap">
          <i class="fas fa-times"></i>
        </button>
      </div>
      <div class="modal-map-body">
        <div ref="mapContainer" class="itinerary-map-view"></div>
      </div>
    </div>
  </div>
</template>

<script>
import * as L from "leaflet";
import { markRaw } from "vue";

const DEFAULT_CENTER = [16.0544, 108.2022];

export default {
  name: "ItineraryMapModal",
  props: {
    show: { type: Boolean, default: false },
    activities: { type: Array, default: () => [] },
  },
  data() {
    return {};
  },
  created() {
    this.map = null;
    this.routingLayer = null;
    this.resizeObserver = null;
    this.containerCheckTimer = null;
    this.animationFrameId = null;
    this.fitTimers = [];
    this.currentPoints = [];
  },
  watch: {
    show(newVal) {
      if (newVal) {
        this.$nextTick(() => {
          this.scheduleMapInit();
        });
        return;
      }

      this.cleanupMap();
    },
    activities: {
      deep: true,
      handler() {
        if (!this.show || !this.map) return;
        this.drawRoute();
        this.scheduleFitRoute([0, 120, 360]);
      },
    },
  },
  beforeUnmount() {
    this.cleanupMap();
  },
  methods: {
    closeMap() {
      this.$emit("update:show", false);
    },
    clearDeferredWork() {
      if (this.containerCheckTimer) {
        clearTimeout(this.containerCheckTimer);
        this.containerCheckTimer = null;
      }

      if (this.animationFrameId) {
        cancelAnimationFrame(this.animationFrameId);
        this.animationFrameId = null;
      }

      this.fitTimers.forEach((timerId) => clearTimeout(timerId));
      this.fitTimers = [];
    },
    cleanupMap() {
      this.clearDeferredWork();

      if (this.resizeObserver) {
        this.resizeObserver.disconnect();
        this.resizeObserver = null;
      }

      if (this.routingLayer) {
        this.routingLayer.clearLayers();
        this.routingLayer = null;
      }

      if (this.map) {
        this.map.remove();
        this.map = null;
      }

      this.currentPoints = [];
    },
    scheduleMapInit() {
      this.clearDeferredWork();
      this.animationFrameId = requestAnimationFrame(() => {
        this.animationFrameId = null;
        this.waitForContainerAndInit();
      });
    },
    waitForContainerAndInit() {
      let attempts = 0;

      const checkContainer = () => {
        if (!this.show) return;

        const container = this.$refs.mapContainer;
        const rect = container?.getBoundingClientRect();
        const hasUsableSize = rect && rect.width > 100 && rect.height > 100;

        if (hasUsableSize || attempts >= 40) {
          this.containerCheckTimer = null;
          this.initMap();
          return;
        }

        attempts += 1;
        this.containerCheckTimer = setTimeout(checkContainer, 50);
      };

      checkContainer();
    },
    initMap() {
      const mapElement = this.$refs.mapContainer;
      if (!mapElement) return;

      if (this.resizeObserver) {
        this.resizeObserver.disconnect();
        this.resizeObserver = null;
      }

      if (this.map) {
        this.map.remove();
        this.map = null;
      }

      const mapInstance = L.map(mapElement, {
        zoomControl: true,
        attributionControl: false,
      });

      this.map = markRaw(mapInstance);

      L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
        maxZoom: 19,
        attribution: "© OpenStreetMap contributors",
      }).addTo(this.map);

      this.routingLayer = markRaw(L.featureGroup().addTo(this.map));
      this.resizeObserver = new ResizeObserver(() => {
        if (this.map) {
          this.scheduleFitRoute([0, 120]);
        }
      });
      this.resizeObserver.observe(mapElement);

      this.drawRoute();
      this.scheduleFitRoute([0, 150, 400]);
    },
    getActivityCoordinates(activity = {}) {
      const lat = Number(activity?.coordinates?.lat ?? activity?.vi_do ?? activity?.latitude ?? activity?.lat);
      const lng = Number(activity?.coordinates?.lng ?? activity?.kinh_do ?? activity?.longitude ?? activity?.lng);

      if (!Number.isFinite(lat) || !Number.isFinite(lng)) return null;
      if (lat < -90 || lat > 90 || lng < -180 || lng > 180) return null;

      return { lat, lng };
    },
    drawRoute() {
      if (!this.map || !this.routingLayer) return;

      this.routingLayer.clearLayers();

      const validActivities = this.activities
        .map((activity) => ({
          ...activity,
          coordinates: this.getActivityCoordinates(activity),
        }))
        .filter((activity) => activity.coordinates);

      const points = validActivities.map((activity) => [
        activity.coordinates.lat,
        activity.coordinates.lng,
      ]);

      validActivities.forEach((activity, index) => {
        const markerHtml = `<div class="custom-map-marker"><span>${index + 1}</span></div>`;
        const icon = L.divIcon({
          html: markerHtml,
          className: "route-marker-icon",
          iconSize: [30, 30],
          iconAnchor: [15, 30],
        });

        const popupContent = `
          <strong>${activity.tieuDe || ""}</strong><br/>
          <small>${activity.thoiGian || ""}</small><br/>
          <small>${activity.moTa ? `${String(activity.moTa).substring(0, 50)}...` : ""}</small>
        `;

        L.marker([activity.coordinates.lat, activity.coordinates.lng], { icon })
          .addTo(this.routingLayer)
          .bindPopup(popupContent);
      });

      if (points.length > 1) {
        L.polyline(points, {
          color: "#0369a1",
          weight: 4,
          opacity: 0.8,
          dashArray: "10, 10",
          lineJoin: "round",
        }).addTo(this.routingLayer);
      }

      this.currentPoints = points;
    },
    fitRoute() {
      if (!this.map) return;

      this.map.invalidateSize(true);

      if (this.currentPoints.length > 1) {
        this.map.fitBounds(L.latLngBounds(this.currentPoints), { padding: [50, 50] });
        return;
      }

      if (this.currentPoints.length === 1) {
        this.map.setView(this.currentPoints[0], 14);
        return;
      }

      this.map.setView(DEFAULT_CENTER, 6);
    },
    scheduleFitRoute(delays = [0]) {
      this.fitTimers.forEach((timerId) => clearTimeout(timerId));
      this.fitTimers = [];

      this.animationFrameId = requestAnimationFrame(() => {
        this.animationFrameId = null;
        this.fitRoute();
      });

      delays
        .filter((delay) => delay > 0)
        .forEach((delay) => {
          const timerId = setTimeout(() => {
            this.fitTimers = this.fitTimers.filter((id) => id !== timerId);
            this.fitRoute();
          }, delay);
          this.fitTimers.push(timerId);
        });
    },
  },
};
</script>

<style>
.modal-map-overlay {
  position: fixed;
  inset: 0;
  z-index: 9999;
  display: flex;
  align-items: center;
  justify-content: center;
  background: rgba(0, 0, 0, 0.6);
}

.modal-map-container {
  display: flex;
  flex-direction: column;
  width: min(90vw, 1728px);
  height: min(85vh, 860px);
  overflow: hidden;
  background: #fff;
  border-radius: 12px;
  box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
}

.modal-map-header {
  display: flex;
  flex: 0 0 auto;
  align-items: center;
  justify-content: space-between;
  padding: 16px 24px;
  border-bottom: 1px solid #e2e8f0;
}

.modal-map-header h3 {
  margin: 0;
  font-size: 1.25rem;
  color: #0f172a;
}

.btn-close-map {
  border: none;
  background: none;
  color: #64748b;
  cursor: pointer;
  font-size: 1.5rem;
}

.modal-map-body {
  position: relative;
  flex: 1 1 auto;
  min-height: 0;
}

.itinerary-map-view {
  position: absolute;
  inset: 0;
  min-height: 360px;
  background: #eef2f7;
}

.itinerary-map-view.leaflet-container {
  width: 100%;
  height: 100%;
}

.itinerary-map-view [class*="leaflet-tile"] {
  animation: none !important;
  transition: none !important;
}

.route-marker-icon {
  background: transparent;
  border: 0;
}

.custom-map-marker {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 30px;
  height: 30px;
  color: white;
  font-weight: bold;
  background: #0ea5e9;
  border: 2px solid white;
  border-radius: 50% 50% 50% 0;
  box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.3);
  transform: rotate(-45deg);
}

.custom-map-marker span {
  transform: rotate(45deg);
}

@media (max-width: 768px) {
  .modal-map-container {
    width: 94vw;
    height: 82vh;
  }

  .modal-map-header {
    padding: 14px 18px;
  }
}
</style>
