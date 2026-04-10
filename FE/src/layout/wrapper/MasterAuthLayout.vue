<template>
  <div id="auth-layout" :class="{ 'is-dark': isDarkTheme }">
    <slot></slot>
  </div>
</template>

<script>
export default {
  name: "MasterAuthLayout",
  computed: {
    isDarkTheme() {
      const path = this.$route?.path || "";
      return path.includes("/doi-tac/") || path.includes("/admin/");
    }
  }
};
</script>

<style scoped>
#auth-layout {
  min-height: 100vh;
  background: linear-gradient(180deg, #f8f9fc 0%, #eef2ff 100%);
  isolation: isolate;
  transition: background 0.4s ease;
}

#auth-layout.is-dark {
  background:
    radial-gradient(circle at 10% 20%, rgba(56, 189, 248, 0.16), transparent 28%),
    radial-gradient(circle at 85% 15%, rgba(14, 165, 233, 0.2), transparent 22%),
    linear-gradient(120deg, #031432 0%, #020b22 55%, #010617 100%);
}
</style>
