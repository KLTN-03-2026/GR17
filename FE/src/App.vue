<template>
  <router-view v-slot="{ Component, route }">
    <component :is="resolveLayout(route)">
      <keep-alive :max="12">
        <component
          v-if="route.meta?.keepAlive"
          :is="Component"
          :key="route.path"
        />
      </keep-alive>

      <Transition name="page-transition" mode="out-in">
        <component
          v-if="!route.meta?.keepAlive"
          :is="Component"
          :key="route.fullPath"
        />
      </Transition>
    </component>
  </router-view>
  <AppDialogHost />
</template>

<script>
import AppDialogHost from "./components/Shared/AppDialogHost.vue";

export default {
  name: "App",
  components: {
    AppDialogHost,
  },
  methods: {
    resolveLayout(route) {
      return route?.meta?.layout || "div";
    },
  },
};
</script>

<style>
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

html, body {
  height: 100%;
  width: 100%;
  overflow-x: hidden;
  overflow-y: auto;
}

#app {
  height: 100%;
  width: 100%;
  min-height: 100vh;
}

.page-transition-enter-active,
.page-transition-leave-active {
  transition: opacity 0.25s cubic-bezier(0.25, 0.8, 0.25, 1), transform 0.25s cubic-bezier(0.25, 0.8, 0.25, 1);
}

.page-transition-enter-from {
  opacity: 0;
  transform: translateY(10px) scale(0.99);
}

.page-transition-leave-to {
  opacity: 0;
  transform: translateY(-10px) scale(0.99);
}
</style>
