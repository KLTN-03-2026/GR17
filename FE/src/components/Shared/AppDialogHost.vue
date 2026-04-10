<template>
  <Teleport to="body">
    <div v-if="dialog.open" class="app-dialog-backdrop" @click.self="onCancel">
      <section class="app-dialog" :class="`app-dialog--${dialog.tone}`" role="dialog" aria-modal="true">
        <button class="app-dialog__close" type="button" @click="onCancel">
          <i class="fas fa-xmark"></i>
        </button>

        <div class="app-dialog__icon">
          <i :class="iconClass"></i>
        </div>

        <div class="app-dialog__copy">
          <h2>{{ dialog.title }}</h2>
          <p>{{ dialog.message }}</p>
        </div>

        <div class="app-dialog__actions">
          <button
            v-if="dialog.mode === 'confirm'"
            type="button"
            class="app-dialog__button app-dialog__button--ghost"
            @click="onCancel"
          >
            {{ dialog.cancelText }}
          </button>
          <button
            type="button"
            class="app-dialog__button app-dialog__button--primary"
            @click="onConfirm"
          >
            {{ dialog.confirmText }}
          </button>
        </div>
      </section>
    </div>
  </Teleport>
</template>

<script>
import { resolveDialog, useAppDialogState } from "../../services/appDialog";

export default {
  name: "AppDialogHost",
  data() {
    return {
      dialog: useAppDialogState(),
    };
  },
  computed: {
    iconClass() {
      const map = {
        info: "fas fa-circle-info",
        success: "fas fa-circle-check",
        warning: "fas fa-triangle-exclamation",
        danger: "fas fa-circle-xmark",
      };
      return map[this.dialog.tone] || map.info;
    },
  },
  mounted() {
    window.addEventListener("keydown", this.onKeydown);
  },
  beforeUnmount() {
    window.removeEventListener("keydown", this.onKeydown);
  },
  methods: {
    onConfirm() {
      resolveDialog(true);
    },
    onCancel() {
      resolveDialog(false);
    },
    onKeydown(event) {
      if (event.key === "Escape" && this.dialog.open) {
        this.onCancel();
      }
    },
  },
};
</script>

<style scoped>
.app-dialog-backdrop {
  position: fixed;
  inset: 0;
  z-index: 2000;
  display: grid;
  place-items: center;
  padding: 1rem;
  background: rgba(15, 23, 42, 0.42);
  backdrop-filter: blur(6px);
}

.app-dialog {
  position: relative;
  width: min(460px, 100%);
  padding: 1.6rem;
  border-radius: 1.5rem;
  background:
    radial-gradient(circle at top right, rgba(186, 230, 253, 0.55), transparent 26%),
    linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
  border: 1px solid rgba(148, 163, 184, 0.26);
  box-shadow: 0 28px 60px rgba(15, 23, 42, 0.22);
}

.app-dialog__close {
  position: absolute;
  top: 0.9rem;
  right: 0.9rem;
  width: 2.5rem;
  height: 2.5rem;
  border: none;
  border-radius: 50%;
  background: #eef4fb;
  color: #315778;
}

.app-dialog__icon {
  width: 4rem;
  height: 4rem;
  display: grid;
  place-items: center;
  border-radius: 1.2rem;
  font-size: 1.45rem;
}

.app-dialog--info .app-dialog__icon {
  background: #dbeafe;
  color: #1d4ed8;
}

.app-dialog--success .app-dialog__icon {
  background: #dcfce7;
  color: #15803d;
}

.app-dialog--warning .app-dialog__icon {
  background: #fef3c7;
  color: #b45309;
}

.app-dialog--danger .app-dialog__icon {
  background: #fee2e2;
  color: #b91c1c;
}

.app-dialog__copy {
  margin-top: 1rem;
}

.app-dialog__copy h2 {
  margin: 0;
  color: #102f56;
  font-size: 1.45rem;
  font-weight: 900;
}

.app-dialog__copy p {
  margin: 0.55rem 0 0;
  color: #4d6d8d;
  line-height: 1.65;
}

.app-dialog__actions {
  margin-top: 1.4rem;
  display: flex;
  justify-content: flex-end;
  gap: 0.75rem;
}

.app-dialog__button {
  min-width: 112px;
  min-height: 2.9rem;
  padding: 0 1rem;
  border: none;
  border-radius: 999px;
  font-weight: 800;
}

.app-dialog__button--ghost {
  background: #fff4d6;
  color: #7a5800;
}

.app-dialog__button--primary {
  background: linear-gradient(135deg, #7a5c00 0%, #a97b00 100%);
  color: #ffffff;
  box-shadow: 0 14px 26px rgba(169, 123, 0, 0.24);
}

@media (max-width: 640px) {
  .app-dialog {
    padding: 1.25rem;
    border-radius: 1.2rem;
  }

  .app-dialog__actions {
    flex-direction: column-reverse;
  }

  .app-dialog__button {
    width: 100%;
  }
}
</style>
