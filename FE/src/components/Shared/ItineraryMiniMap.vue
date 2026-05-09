<template>
  <div
    class="itinerary-mini-map"
    :class="{
      'itinerary-mini-map--empty': !hasPoints,
      'itinerary-mini-map--image-error': imageLoadFailed,
    }"
  >
    <img
      v-if="!imageLoadFailed"
      class="itinerary-mini-map__background"
      :src="travelMapBackground"
      alt=""
      aria-hidden="true"
      @error="handleImageError"
    />

    <svg
      v-if="hasPoints"
      class="itinerary-mini-map__route-layer"
      viewBox="0 0 900 520"
      role="img"
      aria-label="Bản đồ lộ trình thu nhỏ"
      preserveAspectRatio="xMidYMid slice"
    >
      <polyline
        v-if="normalizedPoints.length >= 2"
        class="itinerary-mini-map__route-glow"
        :points="routePolylinePoints"
      />
      <polyline
        v-if="normalizedPoints.length >= 2"
        class="itinerary-mini-map__route"
        :points="routePolylinePoints"
      />

      <g
        v-for="point in normalizedPoints"
        :key="point.key"
        class="itinerary-mini-map__marker"
        :transform="`translate(${point.x} ${point.y})`"
      >
        <circle class="itinerary-mini-map__marker-shadow" cx="0" cy="4" r="19" />
        <circle class="itinerary-mini-map__marker-outer" cx="0" cy="0" r="18" />
        <circle class="itinerary-mini-map__marker-inner" cx="0" cy="0" r="13" />
        <text x="0" y="5" text-anchor="middle">{{ point.index }}</text>
      </g>
    </svg>

    <div v-if="!hasPoints" class="itinerary-mini-map__empty">
      <i class="fas fa-location-dot"></i>
      <span>Chưa có tọa độ để vẽ bản đồ</span>
    </div>
  </div>
</template>

<script>
const MAP_WIDTH = 900;
const MAP_HEIGHT = 520;
const ROUTE_PADDING = {
  top: 74,
  right: 116,
  bottom: 146,
  left: 116,
};

const TRAVEL_MAP_IMAGE_URL =
  "https://images.unsplash.com/photo-1596347958988-cb942eb22eb7?auto=format&fit=crop&w=900&q=80";

function readPoint(activity) {
  const lat = Number(activity?.coordinates?.lat ?? activity?.vi_do ?? activity?.lat);
  const lng = Number(activity?.coordinates?.lng ?? activity?.kinh_do ?? activity?.lng);

  if (!Number.isFinite(lat) || !Number.isFinite(lng)) return null;
  if (lat < -90 || lat > 90 || lng < -180 || lng > 180) return null;
  return { lat, lng };
}

function normalizePoint(point, bounds, index) {
  const drawableWidth = MAP_WIDTH - ROUTE_PADDING.left - ROUTE_PADDING.right;
  const drawableHeight = MAP_HEIGHT - ROUTE_PADDING.top - ROUTE_PADDING.bottom;
  const lngRange = bounds.maxLng - bounds.minLng;
  const latRange = bounds.maxLat - bounds.minLat;

  const xRatio = lngRange === 0 ? 0.5 : (point.lng - bounds.minLng) / lngRange;
  const yRatio = latRange === 0 ? 0.5 : 1 - (point.lat - bounds.minLat) / latRange;

  return {
    key: `${point.lat}-${point.lng}-${index}`,
    index: index + 1,
    x: Math.round(ROUTE_PADDING.left + xRatio * drawableWidth),
    y: Math.round(ROUTE_PADDING.top + yRatio * drawableHeight),
  };
}

export default {
  name: "ItineraryMiniMap",
  props: {
    activities: {
      type: Array,
      default: () => [],
    },
  },
  data() {
    return {
      imageLoadFailed: false,
    };
  },
  computed: {
    travelMapBackground() {
      return TRAVEL_MAP_IMAGE_URL;
    },
    points() {
      return this.activities.map(readPoint).filter(Boolean);
    },
    hasPoints() {
      return this.points.length > 0;
    },
    normalizedPoints() {
      if (this.points.length === 0) return [];

      const bounds = this.points.reduce(
        (acc, point) => ({
          minLat: Math.min(acc.minLat, point.lat),
          maxLat: Math.max(acc.maxLat, point.lat),
          minLng: Math.min(acc.minLng, point.lng),
          maxLng: Math.max(acc.maxLng, point.lng),
        }),
        {
          minLat: this.points[0].lat,
          maxLat: this.points[0].lat,
          minLng: this.points[0].lng,
          maxLng: this.points[0].lng,
        },
      );

      return this.points.map((point, index) => normalizePoint(point, bounds, index));
    },
    routePolylinePoints() {
      return this.normalizedPoints.map((point) => `${point.x},${point.y}`).join(" ");
    },
  },
  methods: {
    handleImageError() {
      this.imageLoadFailed = true;
    },
  },
};
</script>

<style scoped>
.itinerary-mini-map {
  position: relative;
  width: 100%;
  height: 100%;
  min-height: 260px;
  overflow: hidden;
  background:
    radial-gradient(circle at 24% 24%, rgba(14, 165, 233, 0.2), transparent 34%),
    radial-gradient(circle at 76% 20%, rgba(245, 158, 11, 0.18), transparent 28%),
    linear-gradient(135deg, #f8fafc 0%, #e0f2fe 100%);
  isolation: isolate;
}

.itinerary-mini-map::before {
  content: "";
  position: absolute;
  inset: 0;
  z-index: 2;
  pointer-events: none;
  background:
    linear-gradient(180deg, rgba(15, 23, 42, 0.12), rgba(15, 23, 42, 0.24)),
    radial-gradient(circle at 50% 45%, rgba(255, 255, 255, 0.22), transparent 45%);
}

.itinerary-mini-map::after {
  content: "";
  position: absolute;
  right: 0;
  bottom: 0;
  left: 0;
  z-index: 5;
  height: 92px;
  pointer-events: none;
  background: linear-gradient(180deg, rgba(255, 255, 255, 0), rgba(248, 250, 252, 0.97));
}

.itinerary-mini-map--image-error::before {
  background:
    linear-gradient(180deg, rgba(255, 255, 255, 0.16), rgba(15, 23, 42, 0.08)),
    repeating-linear-gradient(
      -18deg,
      rgba(3, 105, 161, 0.08) 0,
      rgba(3, 105, 161, 0.08) 1px,
      transparent 1px,
      transparent 28px
    );
}

.itinerary-mini-map__background {
  position: absolute;
  inset: 0;
  z-index: 1;
  width: 100%;
  height: 100%;
  object-fit: cover;
  object-position: center;
  filter: saturate(0.98) contrast(0.98) brightness(0.88);
  transform: scale(1.015);
}

.itinerary-mini-map__route-layer {
  position: absolute;
  inset: 0;
  z-index: 4;
  width: 100%;
  height: 100%;
}

.itinerary-mini-map__route-glow {
  fill: none;
  stroke: rgba(255, 255, 255, 0.96);
  stroke-width: 15;
  stroke-linecap: round;
  stroke-linejoin: round;
}

.itinerary-mini-map__route {
  fill: none;
  stroke: #005f86;
  stroke-width: 8;
  stroke-dasharray: 18 15;
  stroke-linecap: round;
  stroke-linejoin: round;
}

.itinerary-mini-map__marker {
  paint-order: stroke;
}

.itinerary-mini-map__marker-shadow {
  fill: rgba(15, 23, 42, 0.28);
  filter: blur(2.5px);
}

.itinerary-mini-map__marker-outer {
  fill: #ffffff;
}

.itinerary-mini-map__marker-inner {
  fill: #006d94;
}

.itinerary-mini-map__marker text {
  fill: #ffffff;
  font-size: 18px;
  font-weight: 900;
  font-family: Arial, sans-serif;
}

.itinerary-mini-map__empty {
  position: absolute;
  inset: 0;
  z-index: 6;
  display: grid;
  place-items: center;
  gap: 8px;
  padding: 24px;
  text-align: center;
  background: rgba(248, 250, 252, 0.72);
  color: #475569;
  font-weight: 800;
}

.itinerary-mini-map__empty i {
  color: #0f79a8;
  font-size: 1.35rem;
}
</style>
