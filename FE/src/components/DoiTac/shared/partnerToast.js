import { createToaster } from "@meforma/vue-toaster";

const toaster = createToaster({
  position: "top-right",
  duration: 2600,
});

export function toastSuccess(message) {
  toaster.success(String(message || "Thao tác thành công."));
}

export function toastError(message) {
  toaster.error(String(message || "Có lỗi xảy ra."));
}

export function toastInfo(message) {
  toaster.info(String(message || "Đã cập nhật thông tin."));
}
