import { reactive } from "vue";

const defaultState = () => ({
  open: false,
  mode: "alert",
  tone: "warning",
  title: "",
  message: "",
  confirmText: "Đồng ý",
  cancelText: "Hủy",
});

const state = reactive({
  ...defaultState(),
  resolver: null,
});

function resetState() {
  Object.assign(state, defaultState(), { resolver: null });
}

function openDialog(config) {
  return new Promise((resolve) => {
    Object.assign(state, defaultState(), config, { open: true, resolver: resolve });
  });
}

export function resolveDialog(result) {
  const resolver = state.resolver;
  resetState();
  if (typeof resolver === "function") {
    resolver(result);
  }
}

export function showAlert({
  title = "Thông báo",
  message = "",
  tone = "info",
  confirmText = "Đóng",
} = {}) {
  return openDialog({
    mode: "alert",
    title,
    message,
    tone,
    confirmText,
  });
}

export function showConfirm({
  title = "Xác nhận thao tác",
  message = "",
  tone = "warning",
  confirmText = "Đồng ý",
  cancelText = "Hủy",
} = {}) {
  return openDialog({
    mode: "confirm",
    title,
    message,
    tone,
    confirmText,
    cancelText,
  });
}

export function useAppDialogState() {
  return state;
}
