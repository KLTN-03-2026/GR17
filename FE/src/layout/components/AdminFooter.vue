<template>
  <footer class="admin-footer">
    <div class="admin-footer__inner">
      <div class="admin-footer__copyright">
        <span>Bản quyền &copy; Smart Travel {{ currentYear }}</span>
      </div>
    </div>
  </footer>
</template>

<script>
export default {
  name: "AdminFooter",
  data() {
    return {
      currentYear: new Date().getFullYear(),
    };
  },
};
</script>

<style scoped>
.admin-footer {
  flex-shrink: 0;
  padding: 1.5rem 1rem;
  background-color: #121a2b;
  border-top: 1px solid rgba(37, 99, 235, 0.18);
}

.admin-footer__inner {
  width: 100%;
  display: flex;
  justify-content: center;
}

.admin-footer__copyright {
  text-align: center;
  font-size: 0.875rem;
  color: #8fa3c7;
}
</style>
